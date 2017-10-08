<?php
/**
 * Project: property-bag
 * User: george
 * Date: 08.10.17
 */

namespace NewInventor\DataStructure;


use NewInventor\DataStructure\Transformer\TransformerContainerInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\BooleanNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\VariableNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class MetadataConfiguration implements ConfigurationInterface
{
    const DEFAULT_GROUP_NAME = 'default';
    
    /**
     * @return TreeBuilder
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $builder
            ->root('metadata')
            ->append($this->getNamespaceNode())
            ->append($this->getParentNode())
            ->append($this->getAbstractNode())
            ->append($this->getMethodsNode('getters'))
            ->append($this->getMethodsNode('setters'))
            ->append($this->getValidationNode())
            ->append($this->getPropertiesNode());
        
        return $builder;
    }
    
    protected function getNamespaceNode()
    {
        return (new ScalarNodeDefinition('namespace'))->defaultValue('')->example('Some\Namespace\String');
    }
    
    protected function getParentNode()
    {
        return (new ScalarNodeDefinition('parent'))->defaultNull()->example('Some\Class\Name');
    }
    
    protected function getAbstractNode()
    {
        return (new BooleanNodeDefinition('abstract'))->defaultFalse();
    }
    
    protected function getMethodsNode($name)
    {
        return (new ArrayNodeDefinition($name))
            ->treatTrueLike(['generate' => true])
            ->treatNullLike(['generate' => false])
            ->treatFalseLike(['generate' => false])
            ->children()
            ->booleanNode('generate')->defaultFalse()->end()
            ->arrayNode('except')->scalarPrototype()->end()->end()
            ->arrayNode('only')->scalarPrototype()->end()->end()
            ->end();
    }
    
    protected function getValidationNode()
    {
        return (new ArrayNodeDefinition('validation'))
            ->children()
            ->arrayNode('constraints')->variablePrototype()->end()->end()
            ->arrayNode('getters')->arrayPrototype()->variablePrototype()->end()->end()->end()
            ->arrayNode('properties')->arrayPrototype()->variablePrototype()->end()->end()->end()
            ->end();
    }
    
    protected function getPropertiesNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('properties');
        
        $node
            ->useAttributeAsKey('name')
            ->arrayPrototype()
            ->beforeNormalization()
            ->ifString()
            ->then(
                function ($v) {
                    return ['transformers' => $v];
                }
            )
            ->end()
            ->append($this->getDefaultNode())
            ->append($this->getTransformersNode())
            ->append($this->getPropertyValidationNode())
            ->end();
        
        return $node;
    }
    
    protected function getDefaultNode()
    {
        return (new VariableNodeDefinition(self::DEFAULT_GROUP_NAME))
            ->beforeNormalization()
            ->ifTrue(\Closure::fromCallable([$this, 'checkCustomParameters']))
            ->then(\Closure::fromCallable([$this, 'normalizeDefault']))
            ->end()
            ->defaultNull()
            ->example(1);
    }
    
    protected function getTransformersNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('transformers');
        
        $node
            ->beforeNormalization()
            ->ifString()
            ->then(
                function ($v) {
                    return [[$v => [['groups' => self::DEFAULT_GROUP_NAME]]]];
                }
            )
            ->end()
            ->arrayPrototype()
            ->validate()
            ->ifTrue(
                function ($v) {
                    return array_keys($v)[0] === 'ChainTransformer';
                }
            )
            ->then(
                function ($v) {
                    $v = $this->evaluateInnerTransformer('ChainTransformer')->getNode(true)->finalize($v);
                    
                    return $v;
                }
            )
            ->end()
            ->validate()
            ->ifTrue(
                function ($v) {
                    return array_keys($v)[0] === 'InnerTransformer';
                }
            )
            ->then(
                function ($v) {
                    $v = $this->evaluateInnerTransformer('InnerTransformer')->getNode(true)->finalize($v);
                    
                    return $v;
                }
            )
            ->end()
            ->validate()
            ->ifTrue(
                function ($v) {
                    $transformerName = array_keys($v)[0];
                    
                    return class_exists($transformerName) &&
                           in_array(TransformerContainerInterface::class, class_implements($transformerName), true);
                }
            )
            ->then(
                function ($v) {
                    $transformerName = array_keys($v)[0];
                    $v = $this->evaluateInnerTransformer($transformerName)->getNode(true)->finalize($v);
                    
                    return $v;
                }
            )
            ->end()
            ->validate()
            ->ifTrue(
                function ($v) {
                    $transformerName = array_keys($v)[0];
                    
                    return $transformerName !== 'InnerTransformer' &&
                           $transformerName !== 'ChainTransformer' &&
                           $transformerName !== 'groups' &&
                           (!class_exists($transformerName) ||
                            !in_array(TransformerContainerInterface::class, class_implements($transformerName), true)
                           );
                }
            )
            ->then(
                function ($v) {
                    $v = $this->evaluateTransformer(array_keys($v)[0])->getNode(true)->finalize($v);
                    
                    return $v;
                }
            )
            ->end()
            ->variablePrototype()
            ->beforeNormalization()
            ->ifTrue(\Closure::fromCallable([$this, 'checkCustomParameters']))
            ->then(\Closure::fromCallable([$this, 'normalizeCustomParameters']))
            ->end()
            ->end();
        
        return $node;
    }
    
    protected function normalizeDefault($v)
    {
        if ($v === null) {
            return null;
        }
        if (is_callable($v)) {
            return call_user_func($v);
        }
        if (is_scalar($v)) {
            return $v;
        }
        foreach ($v as $key => $parameter) {
            if (is_callable($parameter)) {
                return call_user_func($parameter);
                continue;
            }
            if (is_array($parameter) && count($parameter) === 1) {
                if (isset($parameter['const'])) {
                    [$className, $constantName] = $parameter['const'];
                    
                    return constant("$className::$constantName");
                }
                if (isset($parameter['static'])) {
                    [$className, $staticName] = $parameter['static'];
                    
                    return $className::$$staticName;
                }
            }
        }
        
        return $v;
    }
    
    protected function checkCustomParameters($v)
    {
        if ($v === null) {
            return true;
        }
        if (is_callable($v)) {
            return true;
        }
        if (is_scalar($v)) {
            return true;
        }
        foreach ($v as $parameter) {
            if (is_callable($parameter)) {
                return true;
            }
            if (is_array($parameter) && count($parameter) === 1) {
                if (
                    isset($parameter['const']) ||
                    isset($parameter['static']) ||
                    isset($parameter['groups'])
                ) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    protected function normalizeCustomParameters($v)
    {
        if ($v === null) {
            return [];
        }
        if (is_callable($v)) {
            return call_user_func($v);
        }
        if (is_scalar($v)) {
            return [$v];
        }
        $groupSet = false;
        foreach ($v as $key => $parameter) {
            if (is_callable($parameter)) {
                $v[$key] = call_user_func($parameter);
                continue;
            }
            if (is_array($parameter) && count($parameter) === 1) {
                if (isset($parameter['const'])) {
                    [$className, $constantName] = $parameter['const'];
                    $v[$key] = constant("$className::$constantName");
                } else if (isset($parameter['static'])) {
                    [$className, $staticName] = $parameter['static'];
                    $v[$key] = $className::$$staticName;
                } else if (isset($parameter['groups'])) {
                    if ($parameter['groups'] === null) {
                        $groupSet = true;
                        $v[$key]['groups'] = [self::DEFAULT_GROUP_NAME];
                    } else if (is_string($parameter['groups'])) {
                        $groupSet = true;
                        $v[$key]['groups'] = [$parameter['groups']];
                    } else if (!is_array($parameter['groups'])) {
                        throw new \InvalidArgumentException('groups must be string or array');
                    }
                }
            }
        }
        if (!$groupSet) {
            $v[]['groups'] = [self::DEFAULT_GROUP_NAME];
        }
        
        return $v;
    }
    
    protected function getPropertyValidationNode()
    {
        return (new ArrayNodeDefinition('validation'))->variablePrototype()->end();
    }
    
    protected function evaluateInnerTransformer($name)
    {
        $builder = new TreeBuilder();
        $node = $builder->root($name);
        
        $node
            ->arrayPrototype()
            ->variablePrototype()
            ->validate()
            ->ifTrue(
                function ($v) {
                    return array_keys($v)[0] === 'ChainTransformer';
                }
            )
            ->then(
                function ($v) {
                    $v = $this->evaluateInnerTransformer('ChainTransformer')->getNode(true)->finalize($v);
                    
                    return $v;
                }
            )
            ->end()
            ->validate()
            ->ifTrue(
                function ($v) {
                    return array_keys($v)[0] === 'InnerTransformer';
                }
            )
            ->then(
                function ($v) {
                    $v = $this->evaluateInnerTransformer('InnerTransformer')->getNode(true)->finalize($v);
                    
                    return $v;
                }
            )
            ->end()
            ->validate()
            ->ifTrue(
                function ($v) {
                    $transformerName = array_keys($v)[0];
                    
                    return class_exists($transformerName) &&
                           in_array(TransformerContainerInterface::class, class_implements($transformerName), true);
                }
            )
            ->then(
                function ($v) {
                    $transformerName = array_keys($v)[0];
                    $v = $this->evaluateInnerTransformer($transformerName)->getNode(true)->finalize($v);
                    
                    return $v;
                }
            )
            ->end()
            ->validate()
            ->ifTrue(
                function ($v) {
                    $transformerName = array_keys($v)[0];
                    
                    return $transformerName !== 'InnerTransformer' &&
                           $transformerName !== 'ChainTransformer' &&
                           $transformerName !== 'groups' &&
                           (!class_exists($transformerName) ||
                            !in_array(TransformerContainerInterface::class, class_implements($transformerName), true)
                           );
                }
            )
            ->then(
                function ($v) use ($name) {
                    $v = $this->evaluateTransformer($name)->getNode(true)->finalize($v);
                    
                    return $v;
                }
            )
            ->end()
            ->beforeNormalization()
            ->ifTrue(\Closure::fromCallable([$this, 'checkCustomParameters']))
            ->then(\Closure::fromCallable([$this, 'normalizeCustomParameters']))
            ->end()
            ->end()
            ->end();
        
        return $node;
    }
    
    protected function evaluateTransformer($name)
    {
        $builder = new TreeBuilder();
        $node = $builder->root($name);
        
        $node
            ->validate()
            ->ifTrue(
                function ($v) {
                    return array_values($v)[0] === null;
                }
            )
            ->then(
                Function ($v) {
                    return [array_keys($v)[0] => []];
                }
            )
            ->end()
            ->validate()
            ->ifTrue(
                function ($v) {
                    return is_scalar(array_values($v)[0]);
                }
            )
            ->then(
                Function ($v) {
                    return [array_keys($v)[0] => [array_values($v)[0]]];
                }
            )
            ->end()
            ->variablePrototype()
            ->beforeNormalization()
            ->ifTrue(\Closure::fromCallable([$this, 'checkCustomParameters']))
            ->then(\Closure::fromCallable([$this, 'normalizeCustomParameters']))
            ->end()
            ->end()
            ->end();
        
        return $node;
    }
}