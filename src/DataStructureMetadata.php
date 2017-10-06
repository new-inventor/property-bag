<?php
/**
 * Project: property-bag
 * User: george
 * Date: 04.10.17
 */

namespace NewInventor\DataStructure;


use NewInventor\DataStructure\Transformer\DataStructure\DataStructureTransformer;
use NewInventor\DataStructure\Transformer\DataStructure\StructureTransformerInterface;
use NewInventor\DataStructure\Transformer\Property\ChainTransformer;
use NewInventor\DataStructure\Transformer\Property\InnerTransformer;
use NewInventor\DataStructure\Transformer\Property\TransformerInterface;
use NewInventor\TypeChecker\TypeChecker;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Context\ExecutionContextFactory;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Yaml\Yaml;

class DataStructureMetadata
{
    /** @var string */
    protected $namespace = '';
    /** @var string */
    protected $className;
    /** @var ClassMetadata */
    protected $classValidationMetadata;
    /** @var ValidatorInterface */
    protected $classValidator;
    /** @var string */
    protected $parent = PropertyBag::class;
    /** @var bool */
    protected $abstract = false;
    /** @var string[] */
    protected $properties = [];
    /** @var StructureTransformerInterface */
    protected $forwardTransformer;
    /** @var StructureTransformerInterface */
    protected $backwardTransformer;
    /** @var string[] */
    protected $getters = [];
    /** @var string[] */
    protected $setters = [];
    /** @var CacheItemPoolInterface */
    private $cacheDriver;
    
    public function __construct(CacheItemPoolInterface $cacheDriver)
    {
        $this->forwardTransformer = new DataStructureTransformer();
        $this->backwardTransformer = new DataStructureTransformer();
        $this->cacheDriver = $cacheDriver;
    }
    
    public static function getConfig(string $file)
    {
        return Yaml::parse(file_get_contents($file));
    }
    
    public static function getClassNameFromFile(string $file)
    {
        return pathinfo($file, PATHINFO_FILENAME);
    }
    
    public function loadConfig(string $file)
    {
        $config = self::getConfig($file);
        $this->className = self::getClassNameFromFile($file);
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
        
        return $this;
    }
    
    protected function createValidator()
    {
        $metadataFactory = new LazyLoadingMetadataFactory(
            new ValidatorLoader($this),
            $this->cacheDriver
        );
        
        $validatorFactory = new ConstraintValidatorFactory();
        $translator = new IdentityTranslator();
        $translator->setLocale('en');
        
        $contextFactory = new ExecutionContextFactory($translator, null);
        
        return new RecursiveValidator(
            $contextFactory,
            $metadataFactory,
            $validatorFactory
        );
    }
    
    protected function prepareMethods($config)
    {
        if (is_bool($config) && $config) {
            return array_keys($this->properties);
        }
        if (is_array($config) && $config['generate']) {
            if (isset($config['only'])) {
                return $config['only'];
            }
            if (isset($config['except'])) {
                return array_values(array_diff(array_keys($this->properties), $config['except']));
            }
        }
        
        return [];
    }
    
    protected function prepareProperty($propertyName, $metadata): void
    {
        TypeChecker::check($metadata)->tarray()->tnull()->tstring()->fail();
        $this->properties[$propertyName] = null;
        if ($metadata === null) {
            return;
        }
        if (is_string($metadata)) {
            $this->forwardTransformer->setTransformer($propertyName, $this->prepareTransformer($metadata));
            
            return;
        }
        if (isset($metadata['transformer'])) {
            $this->forwardTransformer->setTransformer(
                $propertyName,
                $this->prepareTransformers($metadata['transformer'])
            );
        }
        if (isset($metadata['backTransformer'])) {
            $this->backwardTransformer->setTransformer(
                $propertyName,
                $this->prepareTransformers($metadata['backTransformer'])
            );
        }
        if (isset($metadata['validators'])) {
            $this->prepareGetterValidators($propertyName, $metadata['validators']);
        }
        if (isset($metadata['default'])) {
            $this->properties[$propertyName] = $metadata['default'];
        }
    }
    
    protected function prepareTransformers($metadata)
    {
        if (is_string($metadata)) {
            return $this->prepareTransformer($metadata);
        }
        if (!is_array($metadata)) {
            throw new \InvalidArgumentException('Transformers config must be string or array');
        }
        $transformers = [];
        foreach ($metadata as $name => $data) {
            $transformers[] = $this->prepareTransformer($name, $data);
        }
        if (count($transformers) > 1) {
            return new ChainTransformer(...$transformers);
        }
        
        return $transformers[0];
    }
    
    /**
     * @param string $name
     * @param mixed  $parameters
     *
     * @return mixed
     */
    protected function prepareTransformer(string $name, $parameters = null)
    {
        if ($name === 'InnerTransformer' && is_array($parameters)) {
            return $this->prepareInnerTransformer($parameters);
        }
        if ($name === 'ChainTransformer' && is_array($parameters)) {
            return $this->prepareChainTransformer($parameters);
        }
        $transformerClass = $this->getFullName($name);
        if ($parameters === null || $parameters === []) {
            return new $transformerClass();
        }
        $parameters = (array)$parameters;
        $resParameters = [];
        foreach ($parameters as $parameter) {
            $resParameters[] = $this->prepareParameter($parameter);
        }
        
        return new $transformerClass(...$resParameters);
    }
    
    protected function prepareInnerTransformer(array $parameters): Transformer\Property\InnerTransformer
    {
        $transformers = [];
        foreach ($parameters as $transformerName => $params) {
            $transformers[] = $this->prepareTransformer($transformerName, $params);
        }
        
        return new InnerTransformer(...$transformers);
    }
    
    protected function prepareChainTransformer(array $parameters): Transformer\Property\ChainTransformer
    {
        $transformers = [];
        foreach ($parameters as $transformerName => $params) {
            $transformers[] = $this->prepareTransformer($transformerName, $params);
        }
        
        return new ChainTransformer(...$transformers);
    }
    
    protected function getFullName(string $name): string
    {
        if (class_exists($name) && in_array(TransformerInterface::class, class_implements($name), true)) {
            return $name;
        }
        
        return 'NewInventor\DataStructure\Transformer\Property\\' . $name;
    }
    
    protected function prepareParameter($parameter)
    {
        if (is_callable($parameter)) {
            return $parameter();
        }
        if (is_scalar($parameter)) {
            return $parameter;
        }
        if (is_array($parameter)) {
            if (isset($parameter['const']) && is_array($parameter['const'])) {
                [$className, $constantName] = $parameter['const'];
                
                return constant("$className::$constantName");
            }
            if (isset($parameter['static']) && is_array($parameter['static'])) {
                [$className, $staticName] = $parameter['static'];
                
                return $className::$$staticName;
            }
            
            return $parameter;
        }
        throw new \InvalidArgumentException('Transformer parameter not valid');
    }
    
    protected function initValidation($config): void
    {
        $this->classValidationMetadata = new ClassMetadata($this->getFullClassName());
        if (!isset($config['validation'])) {
            return;
        }
        if (isset($config['validation']['constraints']) && is_array($config['validation']['constraints'])) {
            foreach ($config['validation']['constraints'] as $constraint) {
                $this->classValidationMetadata->addConstraint($this->prepareValidator($constraint));
            }
        }
        if (isset($config['validation']['getters']) && is_array($config['validation']['getters'])) {
            foreach ($config['validation']['getters'] as $propertyName => $validators) {
                if (!array_key_exists($propertyName, $this->properties)) {
                    $this->properties[$propertyName] = null;
                }
                $this->prepareGetterValidators($propertyName, $validators);
            }
        }
        if (isset($config['validation']['properties']) && is_array($config['validation']['properties'])) {
            foreach ($config['validation']['properties'] as $propertyName => $validators) {
                if (!array_key_exists($propertyName, $this->properties)) {
                    $this->properties[$propertyName] = null;
                }
                $this->preparePropertyValidators($propertyName, $validators);
            }
        }
    }
    
    protected function prepareGetterValidators(string $propertyName, array $validators): void
    {
        foreach ($validators as $validator) {
            $this->classValidationMetadata->addGetterConstraint($propertyName, $this->prepareValidator($validator));
        }
    }
    
    protected function preparePropertyValidators(string $propertyName, array $validators): void
    {
        foreach ($validators as $validator) {
            $this->classValidationMetadata->addPropertyConstraint($propertyName, $this->prepareValidator($validator));
        }
    }
    
    protected function prepareClassValidators(array $validators): void
    {
        foreach ($validators as $validator) {
            $this->classValidationMetadata->addConstraint($this->prepareValidator($validator));
        }
    }
    
    protected function prepareValidator($validator)
    {
        $validatorClass = $validatorName = array_keys($validator)[0];
        if (!class_exists($validatorName)) {
            $validatorClass = 'Symfony\Component\Validator\Constraints\\' . $validatorName;
        }
        if (!in_array(Constraint::class, class_parents($validatorClass), true)) {
            throw new \InvalidArgumentException('Validator must extend ' . Constraint::class);
        }
        
        return new $validatorClass($validator[$validatorName]);
    }
    
    /**
     * @return string
     */
    public function getFullClassName(): string
    {
        return $this->namespace . '\\' . $this->className;
    }
    
    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }
    
    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }
    
    /**
     * @return ClassMetadata
     */
    public function getClassValidationMetadata(): ClassMetadata
    {
        return $this->classValidationMetadata;
    }
    
    /**
     * @return ValidatorInterface
     */
    public function getClassValidator(): ValidatorInterface
    {
        return $this->classValidator;
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
    public function getProperties(): array
    {
        return $this->properties;
    }
    
    /**
     * @return StructureTransformerInterface
     */
    public function getForwardTransformer(): StructureTransformerInterface
    {
        return $this->forwardTransformer;
    }
    
    /**
     * @return StructureTransformerInterface
     */
    public function getBackwardTransformer(): StructureTransformerInterface
    {
        return $this->backwardTransformer;
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