<?php
/**
 * Project: property-bag
 * User: george
 * Date: 06.10.17
 */

namespace NewInventor\DataStructure;


use Psr\Cache\CacheItemPoolInterface;

class MetadataLoader
{
    /** @var string */
    protected $path;
    /** @var string */
    protected $baseNamespace;
    /** @var CacheItemPoolInterface */
    protected $cacheDriver;
    
    public function __construct(string $path, string $baseNamespace = '')
    {
        $this->path = $path;
        $this->baseNamespace = $baseNamespace;
    }
    
    /**
     * @param CacheItemPoolInterface $cacheDriver
     *
     * @return $this
     */
    public function setCacheDriver(CacheItemPoolInterface $cacheDriver)
    {
        $this->cacheDriver = $cacheDriver;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
    
    /**
     * @return string
     */
    public function getBaseNamespace(): string
    {
        return $this->baseNamespace;
    }
    
    public function loadMetadataFor(string $class): Metadata
    {
        if ($this->cacheDriver !== null) {
            $key = $this->getCacheKey($class);
            $item = $this->cacheDriver->getItem($key);
            if (!$item->isHit()) {
                $config = $this->getMetadataObj($class);
                $item->set($config);
                $this->cacheDriver->save($item);
            }
            
            return $item->get();
        }
        
        return $this->getMetadataObj($class);
    }
    
    protected function getMetadataObj(string $class)
    {
        $path = $this->getFilePath($class);
        if (!file_exists($path)) {
            throw new \RuntimeException('Metadata file does not exist');
        }
    
        return (new Metadata())->loadConfig($path);
    }
    
    protected function getCacheKey(string $class)
    {
        return str_replace('\\', '_', $class);
    }
    
    public function getFilePath(string $class)
    {
        if (is_dir($this->path) && is_readable($this->path)) {
            return $this->path .
                   DIRECTORY_SEPARATOR .
                   str_replace([$this->baseNamespace, '\\'], ['', DIRECTORY_SEPARATOR], $class) .
                   '.yml';
        }
        if (is_file($this->path) && is_readable($this->path)) {
            return $this->path;
        }
        throw new \RuntimeException('Invalid metadata file path.');
    }
}