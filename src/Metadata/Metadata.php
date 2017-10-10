<?php
/**
 * Project: property-bag
 * User: george
 * Date: 04.10.17
 */

namespace NewInventor\PropertyBag\Metadata;


use NewInventor\DataStructure\Metadata\Metadata as BaseMetadata;
use Symfony\Component\Config\Definition\Processor;

class Metadata extends BaseMetadata
{
    /** @var string */
    protected $parent = '';
    /** @var bool */
    protected $abstract = false;
    /** @var string[] */
    protected $getters = [];
    /** @var string[] */
    protected $setters = [];
    
    public function loadConfig(string $file)
    {
        $config = self::getConfig($file);
        $this->className = self::getClassNameFromFile($file);
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [$config]);
        if (isset($config['namespace'])) {
            $this->namespace = $config['namespace'];
        }
        $this->initValidation($config);
        if (isset($config['parent'])) {
            $this->parent = $config['parent'];
        }
        if (isset($config['abstract'])) {
            $this->abstract = $config['abstract'];
        }
        if (isset($config['properties'])) {
            foreach ($config['properties'] as $propertyName => $metadata) {
                $this->prepareProperty($propertyName, $metadata);
            }
        }
        if (isset($config['getters'])) {
            $this->getters = $this->prepareMethods($config['getters']);
        }
        if (isset($config['setters'])) {
            $this->setters = $this->prepareMethods($config['setters']);
        }
        
        $this->classValidator = $this->createValidator();
        unset($this->classValidationMetadata);
        
        return $this;
    }
    
    protected function prepareMethods($config)
    {
        if ($config['only'] !== []) {
            return $config['only'];
        }
        if ($config['except'] !== []) {
            return array_values(array_diff(array_keys($this->properties), $config['except']));
        }
    
        return array_keys($this->properties);
    }
    
    /**
     * @return string
     */
    public function getParent(): string
    {
        return $this->parent;
    }
    
    /**
     * @return bool
     */
    public function isAbstract(): bool
    {
        return $this->abstract;
    }
    
    /**
     * @return string[]
     */
    public function getGetters(): array
    {
        return $this->getters;
    }
    
    /**
     * @return string[]
     */
    public function getSetters(): array
    {
        return $this->setters;
    }
}