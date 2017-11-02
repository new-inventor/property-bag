<?php
/**
 * Project: property-bag
 * User: george
 * Date: 12.10.17
 */

namespace NewInventor\PropertyBag\Metadata;


use NewInventor\DataStructure\Metadata\Loader as BaseLoader;
use NewInventor\DataStructure\Metadata\MetadataInterface;

class Loader extends BaseLoader
{
    /**
     * @param MetadataInterface|Metadata $metadata
     * @param array                      $data
     */
    public function loadData($metadata, array $data): void
    {
        parent::loadData($metadata, $data);
        
        if (isset($data['parent'])) {
            $metadata->parent = $data['parent'];
        }
        if (isset($data['abstract'])) {
            $metadata->abstract = $data['abstract'];
        }
        if (isset($data['getters'])) {
            $metadata->getters = $this->prepareMethods($metadata, $data['getters']);
        }
        if (isset($data['setters'])) {
            $metadata->setters = $this->prepareMethods($metadata, $data['setters']);
        }
    }
    
    protected function prepareMethods($metadata, $config)
    {
        if ($config['only'] !== []) {
            return $config['only'];
        }
        if ($config['except'] !== []) {
            return array_values(array_diff(array_keys($metadata->properties), $config['except']));
        }
        
        return array_keys($metadata->properties);
    }
}