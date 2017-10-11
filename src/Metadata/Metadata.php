<?php
/**
 * Project: property-bag
 * User: george
 * Date: 04.10.17
 */

namespace NewInventor\PropertyBag\Metadata;


use NewInventor\DataStructure\Metadata\Metadata as BaseMetadata;
use NewInventor\PropertyBag\PropertyBag;

class Metadata extends BaseMetadata
{
    /** @var string */
    protected $parent = PropertyBag::class;
    /** @var bool */
    protected $abstract = false;
    /** @var string[] */
    protected $getters = [];
    /** @var string[] */
    protected $setters = [];
    
    public function loadConfig(string $file)
    {
        parent::loadConfig($file);
        if (isset($this->configArray['parent'])) {
            $this->parent = $this->configArray['parent'];
        }
        if (isset($this->configArray['abstract'])) {
            $this->abstract = $this->configArray['abstract'];
        }
        if (isset($this->configArray['getters'])) {
            $this->getters = $this->prepareMethods($this->configArray['getters']);
        }
        if (isset($this->configArray['setters'])) {
            $this->setters = $this->prepareMethods($this->configArray['setters']);
        }
        
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