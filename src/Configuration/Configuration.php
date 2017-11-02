<?php
/**
 * Project: property-bag
 * User: george
 * Date: 08.10.17
 */

namespace NewInventor\PropertyBag\Configuration;


use NewInventor\DataStructure\Configuration\Configuration as BaseConfiguration;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\BooleanNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration extends BaseConfiguration
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
    
    protected function getParentNode(): ScalarNodeDefinition
    {
        return (new ScalarNodeDefinition('parent'))->defaultNull()->example('Some\Class\Name');
    }
    
    protected function getAbstractNode(): BooleanNodeDefinition
    {
        return (new BooleanNodeDefinition('abstract'))->defaultFalse();
    }
    
    protected function getMethodsNode($name): ArrayNodeDefinition
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
}