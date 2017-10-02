<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 27.09.17
 */

namespace NewInventor\PropertyBag\Command;


use NewInventor\PropertyBag\Formatter\FormatterInterface;
use NewInventor\PropertyBag\Normalizer\CamelCaseNormalizer;
use NewInventor\PropertyBag\Normalizer\NormalizerInterface;
use NewInventor\PropertyBag\Normalizer\ScreamingCaseNormalizer;
use NewInventor\PropertyBag\PropertyBag;
use NewInventor\TypeChecker\TypeChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class GenerateBagCommand extends Command
{
    /** @var Filesystem */
    private $fileSystem;
    /** @var string */
    private $callPath;
    private $namespace = '';
    private $className = '';
    private $getters = [];
    private $setters = [];
    private $properties = [];
    private $formatters = [];
    private $normalizers = [];
    private $listProperties = [];
    private $constProperties = [];
    private $uses = [];
    private $bag;
    private $configPath;
    private $outputPath;
    private $baseNamespace;
    private $force;
    
    protected static $rootParent = PropertyBag::class;
    
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->fileSystem = new Filesystem();
        $this->callPath = getcwd();
    }
    
    protected function configure(): void
    {
        $this
            ->setName('bag:generate')
            ->setDescription('Generate property bag from file or directory')
            ->addArgument('config', InputArgument::REQUIRED, 'Path to config')
            ->addArgument('output-dir', InputArgument::REQUIRED, 'Path to generated files')
            ->addOption(
                'base-namespace',
                'b',
                InputOption::VALUE_OPTIONAL,
                'Provide base namespace for all config files',
                ''
            )->addOption('force', 'f', InputOption::VALUE_NONE, 'Force rewrite files');
    }
    
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \LogicException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->configPath = $this->getAbsolutePath($input->getArgument('config'));
        $this->outputPath = $this->getAbsolutePath($input->getArgument('output-dir'));
        $this->baseNamespace = $input->getOption('base-namespace');
        $this->force = $input->getOption('force');
        if (!is_dir($this->outputPath)) {
            $this->fileSystem->mkdir($this->outputPath);
        }
        $output->writeln('Start generation');
        if (is_file($this->configPath)) {
            $this->generateBagFromFile($this->configPath);
            $output->writeln("File '$this->configPath' processed");
        } else if (is_dir($this->configPath)) {
            $files = $this->getFilesInDir($this->configPath, ['yml']);
            foreach ($files as $file) {
                $this->generateBagFromFile($file);
                $output->writeln("File '$file' processed");
            }
        }
        $output->writeln('Generation complete');
    }
    
    protected function getFilesInDir(string $dir, array $extensions = [], array &$results = []): array
    {
        $files = scandir($dir, SCANDIR_SORT_ASCENDING);
        foreach ($files as $key => $value) {
            $path = $dir . DIRECTORY_SEPARATOR . $value;
            if (!is_dir($path)) {
                $extension = pathinfo($path, PATHINFO_EXTENSION);
                if ($extensions === [] || in_array($extension, $extensions, true)) {
                    $results[] = $path;
                }
            } else if ($value !== '.' && $value !== '..') {
                $this->getFilesInDir($path, $extensions, $results);
            }
        }
        
        return $results;
    }
    
    protected function getAbsolutePath(string $path)
    {
        if (!$this->fileSystem->isAbsolutePath($path)) {
            return $this->callPath . DIRECTORY_SEPARATOR . $path;
        }
        
        return $path;
    }
    
    protected function generateBagFromFile($file)
    {
        $this->getters = [];
        $this->setters = [];
        $this->properties = [];
        $this->listProperties = [];
        $this->constProperties = [];
        $this->uses = [];
        $this->formatters = [];
        $this->normalizers = [];
        $config = Yaml::parse(file_get_contents($file));
        $this->bag = $this->getPropertyBagTemplate();
        $this->namespace = $config['namespace'] ?? '';
        $this->className = pathinfo($file, PATHINFO_FILENAME);
        $this->bag = str_replace(
            ['%namespace%', '%class%'],
            ["namespace $this->namespace", $this->className],
            $this->bag
        );
        if (isset($config['properties']) && is_array($config['properties'])) {
            foreach ($config['properties'] as $property => $propertyConfig) {
                $screamingCasePropertyName = ScreamingCaseNormalizer::make()->normalize($property);
                $camelCasePropertyName = CamelCaseNormalizer::make()->normalize($property);
                $ucCamelCasePropertyName = ucfirst($camelCasePropertyName);
                $listPropertyStr = $this->generateListProperty($screamingCasePropertyName);
                $this->listProperties[] = '                ' . $listPropertyStr;
                $this->constProperties[] = $this->generateConstProperty($property, $screamingCasePropertyName);
                $this->properties[] = $this->generateProperty('                ' . $listPropertyStr, $propertyConfig);
                if (isset($config['getters'])) {
                    $this->getters[] =
                        $this->generateGetter($config['getters'], $property, $listPropertyStr, $ucCamelCasePropertyName);
                }
                if (isset($config['setters'])) {
                    $this->setters[] = $this->generateSetter(
                        $config['setters'],
                        $property,
                        $listPropertyStr,
                        $ucCamelCasePropertyName,
                        $camelCasePropertyName
                    );
                }
            }
        }
        $this->getters = array_filter($this->getters);
        $this->setters = array_filter($this->setters);
        $parent = $config['parent'] ?? self::$rootParent;
        $abstract = $this->isAbstract($config) ? 'abstract ' : '';
        $propertiesIniter = '';
        if(count($this->properties) > 0){
            $template = $this->propertiesIniterTemplate();
            if($parent !== self::$rootParent){
                $template = $this->propertiesChildIniterTemplate();
            }
            $propertiesIniter = str_replace(
                '%properties%',
                implode(",\n", $this->properties),
                $template
            );
        }
        $normalizersIniter = '';
        if(count($this->normalizers) > 0){
            $template = $this->normalizersIniterTemplate();
            if($parent !== self::$rootParent){
                $template = $this->normalizersChildIniterTemplate();
            }
            $normalizersIniter = str_replace(
                '%normalizers%',
                implode(",\n", $this->normalizers),
                $template
            );
        }
        $formattersIniter = '';
        if(count($this->formatters) > 0){
            $template = $this->formattersIniterTemplate();
            if($parent !== self::$rootParent){
                $template = $this->formattersChildIniterTemplate();
            }
            $formattersIniter = str_replace(
                '%formatters%',
                implode(",\n", $this->formatters),
                $template
            );
        }
        $this->bag = str_replace(
            [
                '%propertiesIniter%',
                '%normalizersIniter%',
                '%formattersIniter%',
                '%constProperties%',
                '%getters%',
                '%setters%',
                '%parent%',
                '%abstract%',
            ],
            [
                $propertiesIniter,
                $normalizersIniter,
                $formattersIniter,
                implode("\n", $this->constProperties),
                implode("\n\n", $this->getters),
                implode("\n\n", $this->setters),
                $parent,
                $abstract,
            ],
            $this->bag
        );
        $dirName = $this->getOutputDirName($config['namespace']);
        $fileName = $this->getOutputFileName($dirName);
        $this->generateUses($config);
        /** @noinspection NotOptimalRegularExpressionsInspection */
        $this->bag = preg_replace('/(\\\\?\w+(?:\\\\\w+)*\\\\)(?=\w+(?:::|->|\(|\n))/', '', $this->bag);
        $this->bag = preg_replace('/\n{4,}/', "\n", $this->bag);
        
        if (!$this->force && file_exists($fileName)) {
            $currentContent = file_get_contents($fileName);
            $this->copyCustomCode($currentContent);
            $this->mergeUses($currentContent);
        }
        $this->bag = str_replace('%use%', implode("\n", $this->uses), $this->bag);
        
        $this->fileSystem->mkdir($dirName);
        $this->fileSystem->dumpFile($fileName, $this->bag);
    }
    
    protected function isAbstract($config)
    {
        return isset($config['abstract']) && $config['abstract'];
    }
    
    protected function getOutputDirName($namespace)
    {
        return str_replace(
            '\\',
            '/',
            $this->outputPath . str_replace($this->baseNamespace, '', $namespace)
        );
    }
    
    protected function getOutputFileName($dirName)
    {
        return $dirName . DIRECTORY_SEPARATOR . $this->className . '.php';
    }
    
    protected function generateConstProperty(string $propertyName, string $camelCasePropertyName): string
    {
        return str_replace(
            ['%capsName%', '%name%'],
            [$camelCasePropertyName, $propertyName],
            $this->constPropertyTemplate()
        );
    }
    
    protected function generateListProperty(string $camelCasePropertyName): string
    {
        return str_replace('%capsName%', $camelCasePropertyName, $this->listPropertyTemplate());
    }
    
    protected function generateProperty(string $listPropertyStr, $propertyConfig): string
    {
        $propertyStr = str_replace('%listProperty%', $listPropertyStr, $this->propertyTemplate());
        $default = 'null';
        if (is_string($propertyConfig)) {
            $this->normalizers[] = $listPropertyStr . ' => ' . $this->generateNormalizer($propertyConfig);
        } else if (is_array($propertyConfig)) {
            if (isset($propertyConfig['normalizer'])) {
                $this->normalizers[] =
                    $listPropertyStr . ' => ' . $this->generateNormalizer($propertyConfig['normalizer']);
            }
            if (isset($propertyConfig['formatter'])) {
                $this->formatters[] =
                    $listPropertyStr . ' => ' . $this->generateFormatter($propertyConfig['formatter']);
            }
            if (isset($propertyConfig['default'])) {
                $default = $this->generateDefault($propertyConfig['default']);
            }
        }
        
        return str_replace('%default%', $default, $propertyStr);
    }
    
    protected function generateGetter($config, $propertyName, $listPropertyName, $ucCamelCasePropertyName): ?string
    {
        if ($this->canGenerateGetterOrSetter($config, $propertyName)) {
            return str_replace(
                ['%listPropertyName%', '%ucfirstName%'],
                [$listPropertyName, $ucCamelCasePropertyName],
                $this->getterTemplate()
            );
        }
        
        return null;
    }
    
    protected function generateSetter($config, $propertyName, $listPropertyName, $ucCamelCasePropertyName, $camelCasePropertyName): ?string
    {
        if ($this->canGenerateGetterOrSetter($config, $propertyName)) {
            return str_replace(
                ['%listPropertyName%', '%ucfirstName%', '%varName%'],
                [$listPropertyName, $ucCamelCasePropertyName, $camelCasePropertyName],
                $this->setterTemplate()
            );
        }
        
        return null;
    }
    
    protected function generateUses($config)
    {
        /** @noinspection NotOptimalRegularExpressionsInspection */
        preg_match_all('/\\\\?\w+(?:\\\\\w+)+(?=::|->|\(|\n)/', $this->bag, $matches);
        $matches = array_unique($matches[0]);
        foreach ($matches as $use) {
            if ($use === $config['namespace'] . '\\' . $this->className) {
                continue;
            }
            $namespaceLength = strlen($config['namespace']);
            if(strpos($use, $config['namespace']) !== false && strpos($use, '\\', $namespaceLength + 2) === false){
                continue;
            }
            $this->uses[] = str_replace('%class%', $use, $this->useTemplate());
        }
    }
    
    protected function copyCustomCode($currentContent)
    {
        if (preg_match('/\\/\\/CustomCodeBegin.+?\\/\\/CustomCodeEnd/s', $currentContent, $matches)) {
            $this->bag = preg_replace('/\\/\\/CustomCodeBegin.+?\\/\\/CustomCodeEnd/s', $matches[0], $this->bag);
        }
    }
    
    protected function mergeUses($currentContent)
    {
        if (preg_match('/namespace.*?;\s*((?:use .+?;\n)+)/s', $currentContent, $matches)) {
            $usesCurrent = explode("\n", $matches[1]);
            unset($usesCurrent[count($usesCurrent) - 1]);
            if (count($this->uses) !== count($usesCurrent)) {
                $this->uses = array_merge($usesCurrent, array_diff($this->uses, $usesCurrent));
            }
        }
    }
    
    protected function canGenerateGetterOrSetter($config, $property)
    {
        return (
                   is_bool($config) && $config
               ) ||
               (
                   is_array($config) &&
                   $config['generate'] &&
                   (
                       (
                           isset($config['except']) &&
                           !in_array($property, $config['except'], true)
                       ) ||
                       (
                           isset($config['only']) &&
                           in_array($property, $config['only'], true)
                       ) ||
                       (
                           !isset($config['except']) &&
                           !isset($config['only'])
                       )
                   )
               );
    }
    
    protected function generateNormalizer($normalizer): string
    {
        $res = [];
        if (is_string($normalizer)) {
            $res['fullName'] = $this->getNormalizerFullName($normalizer);
            $res['parameters'] = '';
        } else if (is_array($normalizer)) {
            $res['fullName'] = $this->getNormalizerFullName(array_keys($normalizer)[0]);
            $res['parameters'] = $this->generateParameters(array_values($normalizer)[0]);
        } else {
            throw new \InvalidArgumentException('Normalizer must be string or array(<name> => <params>)');
        }
        $template = $this->normalizerTemplate();
        
        return str_replace(['%normalizerName%', '%normalizerParameters%'], $res, $template);
    }
    
    protected function getNormalizerFullName(string $name): string
    {
        if (class_exists($name) && in_array(NormalizerInterface::class, class_implements($name), true)) {
            return $name;
        }
        
        return 'NewInventor\PropertyBag\Normalizer\\' . ucfirst($name) . 'Normalizer';
    }
    
    protected function generateParameters(array $parameters): string
    {
        $res = [];
        foreach ($parameters as $parameter) {
            if (isset($parameter['as_is'])) {
                $res[] = $parameter['as_is'];
            } else if (isset($parameter['normalizer'])) {
                $res[] = $this->generateNormalizer($parameter['normalizer']);
            } else if (isset($parameter['formatter'])) {
                $res[] = $this->generateFormatter($parameter['formatter']);
            } else if (is_string($parameter)) {
                $res[] = "'$parameter'";
            } else if (is_bool($parameter)) {
                $res[] = $parameter ? 'true' : 'false';
            } else if (is_scalar($parameter)) {
                $res[] = (string)$parameter;
            } else if (is_array($parameter)) {
                TypeChecker::check($parameter)->inner()->tscalar()->fail();
                $data = [];
                foreach ($parameter as $name => $value) {
                    if (is_string($value)) {
                        $value = "'$value'";
                    }
                    if ($name === 'as_is') {
                        $data[] = $value;
                    } else {
                        $data[] = (is_int($name) ? '' : "$name => ") . $value;
                    }
                }
                $res[] = '[' . implode(', ', $data) . ']';
            }
        }
        
        return implode(', ', $res);
    }
    
    protected function getNameFromNamespace($namespace)
    {
        return substr($namespace, strrpos($namespace, '\\') + 1);
    }
    
    protected function generateFormatter($formatter)
    {
        $res = [];
        if (is_string($formatter)) {
            $res['fullName'] = $this->getFormatterFullName($formatter);
            $res['parameters'] = '';
        } else if (is_array($formatter)) {
            $res['fullName'] = $this->getFormatterFullName(array_keys($formatter)[0]);
            $res['parameters'] = $this->generateParameters(array_values($formatter)[0]);
        } else {
            throw new \InvalidArgumentException('Formatter must be string or array');
        }
        $template = $this->formatterTemplate();
        
        return str_replace(['%formatterName%', '%formatterParameters%'], $res, $template);
    }
    
    protected function getFormatterFullName($name)
    {
        if (class_exists($name) && in_array(FormatterInterface::class, class_implements($name), true)) {
            return $name;
        }
        return 'NewInventor\PropertyBag\Formatter\\' . ucfirst($name) . 'Formatter';
        
    }
    
    protected function generateDefault($default)
    {
        return $this->generateParameters([$default]);
    }
    
    protected function getPropertyBagTemplate()
    {
        return '<?php

%namespace%;


%use%

%abstract%class %class% extends %parent%
{
    //CustomCodeBegin
    
    //CustomCodeEnd
    
    //GeneratedCodeBegin
%constProperties%
%propertiesIniter%
%normalizersIniter%
%formattersIniter%
%getters%

%setters%
    //GeneratedCodeEnd
}';
    }
    
    protected function propertiesIniterTemplate()
    {
        return '
    protected function initProperties(): void
    {
        $this->properties = [
%properties%
        ];
    }';
    }
    
    protected function normalizersIniterTemplate()
    {
        return '
    protected function initNormalizers(): void
    {
        $this->normalizers = [
%normalizers%
        ];
    }';
    }
    
    protected function formattersIniterTemplate()
    {
        return '
    protected function initFormatters(): void
    {
        $this->formatters = [
%formatters%
        ];
    }';
    }
    
    protected function propertiesChildIniterTemplate()
    {
        return '
    protected function initProperties(): void
    {
        parent::initProperties();
        $this->properties = array_merge(
            $this->properties,
            [
%properties%
            ]
        );
    }';
    }
    
    protected function normalizersChildIniterTemplate()
    {
        return '
    protected function initNormalizers(): void
    {
        parent::initNormalizers();
        $this->normalizers = array_merge(
            $this->normalizers,
            [
%normalizers%
            ]
        );
    }';
    }
    
    protected function formattersChildIniterTemplate()
    {
        return '
    protected function initFormatters(): void
    {
        parent::initFormatters();
        $this->formatters = array_merge(
            $this->formatters,
            [
%formatters%
            ]
        );
    }';
    }
    
    protected function listPropertyTemplate()
    {
        return 'self::%capsName%';
    }
    
    protected function constPropertyTemplate()
    {
        return "    const %capsName% = '%name%';";
    }
    
    protected function getterTemplate()
    {
        return '    public function get%ucfirstName%()
    {
        return $this->properties[%listPropertyName%];
    }';
    }
    
    protected function setterTemplate()
    {
        return '    public function set%ucfirstName%($%varName%)
    {
        $this->set(%listPropertyName%, $%varName%);
        
        return $this;
    }';
    }
    
    protected function useTemplate()
    {
        return 'use %class%;';
    }
    
    protected function propertyTemplate()
    {
        return '%listProperty% => %default%';
    }
    
    protected function normalizerTemplate()
    {
        return '%normalizerName%::make(%normalizerParameters%)';
    }
    
    protected function formatterTemplate()
    {
        return '%formatterName%::make(%formatterParameters%)';
    }
}