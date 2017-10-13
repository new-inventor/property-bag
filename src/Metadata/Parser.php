<?php
/**
 * Project: property-bag
 * User: george
 * Date: 12.10.17
 */

namespace NewInventor\PropertyBag\Metadata;


use NewInventor\DataStructure\Metadata\MetadataInterface;
use NewInventor\DataStructure\Metadata\Parser as BaseParser;

class Parser extends BaseParser
{
    /**
     * @param                            $file
     * @param MetadataInterface|Metadata $metadata
     *
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     */
    public function parse($file, MetadataInterface $metadata): void
    {
        parent::parse($file, $metadata);
        if (isset($this->metadata->configArray['parent'])) {
            $this->metadata->parent = $this->metadata->configArray['parent'];
        }
        if (isset($this->metadata->configArray['abstract'])) {
            $this->metadata->abstract = $this->metadata->configArray['abstract'];
        }
        if (isset($this->metadata->configArray['getters'])) {
            $this->metadata->getters = $this->prepareMethods($this->metadata->configArray['getters']);
        }
        if (isset($this->metadata->configArray['setters'])) {
            $this->metadata->setters = $this->prepareMethods($this->metadata->configArray['setters']);
        }
    }
    
    protected function prepareMethods($config)
    {
        if ($config['only'] !== []) {
            return $config['only'];
        }
        if ($config['except'] !== []) {
            return array_values(array_diff(array_keys($this->metadata->properties), $config['except']));
        }
        
        return array_keys($this->metadata->properties);
    }
}