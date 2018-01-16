<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 27.09.17
 */

namespace NewInventor\PropertyBag\Command;


use NewInventor\DataStructure\Configuration\Parser\Yaml;
use NewInventor\PropertyBag\Configuration\Configuration;
use NewInventor\PropertyBag\Metadata\Factory;
use NewInventor\PropertyBag\Metadata\Loader;
use NewInventor\PropertyBag\Metadata\Metadata;
use NewInventor\PropertyBag\PropertyBag;
use NewInventor\Transformers\Transformer\CsvStringToArray;
use NewInventor\Transformers\Transformer\StringToCamelCase;
use NewInventor\Transformers\Transformer\StringToScreamingSnakeCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class GenerateBagCommand extends Command
{
    /** @var Filesystem */
    private $fileSystem;
    /** @var string */
    private $callPath;
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
            ->setName('generate:bag')
            ->setDescription('Generate property bag from file or directory')
            ->addArgument('config', InputArgument::REQUIRED, 'Path to config')
            ->addArgument('output-dir', InputArgument::REQUIRED, 'Path to generated files')
            ->addOption(
                'base-namespace',
                'b',
                InputOption::VALUE_OPTIONAL,
                'Provide base namespace for all config files',
                ''
            )->addOption('force', 'f', InputOption::VALUE_NONE, 'Force rewrite files')
            ->addOption(
                'only',
                'o',
                InputOption::VALUE_OPTIONAL,
                'Coma separated namespaces from config directory to process only it. With leader slash. (\<namespace>)'
            );
    }
    
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @throws \NewInventor\TypeChecker\Exception\TypeException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \RuntimeException
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->configPath = $this->getAbsolutePath($input->getArgument('config'));
        $this->outputPath = $this->getAbsolutePath($input->getArgument('output-dir'));
        $this->baseNamespace = trim($input->getOption('base-namespace'), "\t\n\r\0\x0B\\");
        $this->force = $input->getOption('force');
        if (!is_dir($this->outputPath)) {
            $this->fileSystem->mkdir($this->outputPath);
        }
        $output->writeln('Start generation');
        $only = CsvStringToArray::make()->transform($input->getOption('only'));
        if($only === null){
            $only = [];
        }
        
        $parser = new Yaml(new Configuration());
        $loader = new Loader($this->configPath, $parser, $this->baseNamespace);
        $factory = new Factory($loader);
        if (is_dir($this->configPath)) {
            $files = $this->getFilesInDir($this->configPath, ['yml']);
            foreach ($files as $file) {
                $className = $this->getClassName($file);
                $withoutBase = str_replace($this->baseNamespace, '', $className);
                if (!\in_array($withoutBase, $only, true) && $only !== []) {
                    continue;
                }
                /** @var Metadata $metadata */
                $metadata = $factory->getMetadataFor($className);
                $this->generateFile($metadata);
                $output->writeln("File '$file' processed");
            }
        }
        $output->writeln('Generation complete');
    }
    
    protected function getClassName(string $file)
    {
        $res = $this->baseNamespace . '\\';
        $dir = str_replace(
            '/',
            '\\',
            trim(str_replace($this->configPath, '', pathinfo($file, PATHINFO_DIRNAME)), "\t\n\r\0\x0B\\/")
        );
        $res .= $dir;
        if ($dir !== '') {
            $res .= '\\';
        }
        $res .= pathinfo($file, PATHINFO_FILENAME);
        
        return $res;
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
    
    protected function generateFile(Metadata $metadata)
    {
        $bag = $this->getPropertyBagTemplate();
        $properties = $metadata->properties;
        $propertyData = $this->initPropertyNames($properties);
        
        $bag = str_replace(
            [
                '%namespace%',
                '%abstract%',
                '%class%',
                '%parent%',
                '%propertyConstants%',
                '%initProperty%',
                '%getters%',
                '%setters%',
            ],
            [
                $this->prepareNamespace($metadata->getNamespace()),
                $metadata->abstract ? 'abstract ' : '',
                $metadata->getClassName(),
                $metadata->parent,
                $this->preparePropertiesConstants($propertyData),
                $this->prepareInitPropertiesMethod($propertyData, $metadata->parent !== PropertyBag::class),
                $this->prepareGetters($propertyData, $metadata->getters),
                $this->prepareSetters($propertyData, $metadata->setters),
            ],
            $bag
        );
        
        $dirName = $this->getOutputDirName($metadata->getNamespace());
        $fileName = $this->getOutputFileName($dirName, $metadata);
        $uses = $this->generateUses($bag, $metadata);
        /** @noinspection NotOptimalRegularExpressionsInspection */
        $bag = preg_replace('/(\\\\?\w+(?:\\\\\w+)*\\\\)(?=\w+(?:::|->|\(|\n))/', '', $bag);
        $bag = preg_replace('/\n{4,}/', "\n", $bag);
        
        if (!$this->force && file_exists($fileName)) {
            $currentContent = file_get_contents($fileName);
            $bag = $this->copyCustomCode($bag, $currentContent);
            $uses = $this->mergeUses($uses, $currentContent);
        }
        $bag = str_replace('%use%', implode("\n", $uses), $bag);
        
        $this->fileSystem->mkdir($dirName);
        $this->fileSystem->dumpFile($fileName, $bag);
    }
    
    protected function initPropertyNames(array $propertyNames)
    {
        $res = [];
        $selfConstantTemplate = $this->selfConstantTemplate();
        foreach ($propertyNames as $propertyName => $default) {
            $res[$propertyName]['default'] = $this->prepareParameter($default);
            $res[$propertyName]['camel'] = StringToCamelCase::make()->transform($propertyName);
            $res[$propertyName]['scream'] = StringToScreamingSnakeCase::make()->transform($propertyName);
            $res[$propertyName]['ucfirstCamel'] = ucfirst($res[$propertyName]['camel']);
            $res[$propertyName]['selfConstant'] = str_replace(
                '%screamName%',
                $res[$propertyName]['scream'],
                $selfConstantTemplate
            );
        }
        
        return $res;
    }
    
    protected function preparePropertiesConstants(array $propertyData)
    {
        $res = [];
        $template = $this->constPropertyTemplate();
        foreach ($propertyData as $propertyName => $vars) {
            $res[] = str_repeat(' ', 4) .
                     str_replace(['%screamName%', '%name%'], [$vars['scream'], $propertyName], $template);
        }
        
        return implode("\n", $res);
    }
    
    protected function prepareParameter($default)
    {
        if ($default === null) {
            return 'null';
        }
        if (is_string($default)) {
            return $this->prepareStringValue($default);
        }
        if (is_int($default) || is_float($default)) {
            return $default;
        }
        if (is_bool($default)) {
            return $default ? 'true' : 'false';
        }
        if (is_array($default) && isset($default['as_is'])) {
            return $default['as_is'];
        }
        if (is_array($default)) {
            $data = [];
            foreach ($default as $key => $value) {
                if (is_string($value)) {
                    $value = $this->prepareStringValue($value);
                }
                $data[] = (is_int($key) ? '' : $this->prepareStringValue($key) . ' => ') . $value;
            }
            
            return implode(', ', $data);
        }
        
        return null;
    }
    
    protected function prepareStringValue($value)
    {
        $value = str_replace("'", "\'", $value);
        
        return "'$value'";
    }
    
    protected function prepareInitPropertiesMethod(array $propertyData, bool $parentCall)
    {
        $template = $parentCall ? $this->propertiesChildIniterTemplate() : $this->propertiesIniterTemplate();
        $propertyTemplate = $this->arrayPropertyTemplate();
        $res = [];
        foreach ($propertyData as $name => $data) {
            $res[] = str_repeat(' ', 12) .
                     str_replace(
                         ['%selfConstant%', '%default%'],
                         [$data['selfConstant'], $data['default']],
                         $propertyTemplate
                     );
        }
        
        return str_replace('%properties%', implode("\n", $res), $template);
    }
    
    protected function prepareGetters(array $propertyData, $getters)
    {
        $template = $this->getterTemplate();
        $res = [];
        foreach ($getters as $propertyName) {
            $res[] = str_replace(
                ['%ucfirstName%', '%selfConstant%'],
                [$propertyData[$propertyName]['ucfirstCamel'], $propertyData[$propertyName]['selfConstant']],
                $template
            );
        }
        
        return implode("\n", $res);
    }
    
    protected function prepareSetters(array $propertyData, $setters)
    {
        $template = $this->setterTemplate();
        $res = [];
        foreach ($setters as $propertyName) {
            $res[] = str_replace(
                ['%ucfirstName%', '%selfConstant%', '%camelName%'],
                [
                    $propertyData[$propertyName]['ucfirstCamel'],
                    $propertyData[$propertyName]['selfConstant'],
                    $propertyData[$propertyName]['camel'],
                ],
                $template
            );
        }
        
        return implode("\n", $res);
    }
    
    protected function prepareNamespace($namespace)
    {
        return $namespace === '' ? '' : "namespace $namespace;";
    }
    
    protected function getAbsolutePath(string $path)
    {
        if (!$this->fileSystem->isAbsolutePath($path)) {
            return $this->callPath . DIRECTORY_SEPARATOR . $path;
        }
        
        return $path;
    }
    
    protected function getOutputDirName($namespace)
    {
        return str_replace(
            '\\',
            '/',
            $this->outputPath . DIRECTORY_SEPARATOR . str_replace($this->baseNamespace, '', $namespace)
        );
    }
    
    protected function getOutputFileName($dirName, Metadata $metadata)
    {
        return $dirName . DIRECTORY_SEPARATOR . $metadata->getClassName() . '.php';
    }
    
    protected function generateUses($bag, Metadata $metadata)
    {
        /** @noinspection NotOptimalRegularExpressionsInspection */
        preg_match_all('/\\\\?\w+(?:\\\\\w+)+(?=::|->|\(|\n)/', $bag, $matches);
        $namespace = $metadata->getNamespace();
        $matches = array_unique($matches[0]);
        $uses = [];
        foreach ($matches as $use) {
            if ($use === $namespace . '\\' . $metadata->getClassName()) {
                continue;
            }
            $namespaceLength = strlen($namespace);
            if (strpos($use, $namespace) !== false && strpos($use, '\\', $namespaceLength + 2) === false) {
                continue;
            }
            $uses[] = str_replace('%class%', $use, $this->useTemplate());
        }
        
        return $uses;
    }
    
    protected function copyCustomCode($bag, $currentContent)
    {
        if (preg_match('/\\/\\/CustomCodeBegin.+?\\/\\/CustomCodeEnd/s', $currentContent, $matches)) {
            $bag = preg_replace('/\\/\\/CustomCodeBegin.+?\\/\\/CustomCodeEnd/s', $matches[0], $bag);
        }
        
        return $bag;
    }
    
    protected function mergeUses($uses, $currentContent)
    {
        if (preg_match('/namespace.*?;\s*((?:use .+?;\n)+)/s', $currentContent, $matches)) {
            $usesCurrent = explode("\n", $matches[1]);
            unset($usesCurrent[count($usesCurrent) - 1]);
            if (count($uses) !== count($usesCurrent)) {
                $uses = array_merge($usesCurrent, array_diff($uses, $usesCurrent));
            }
        }
        
        return $uses;
    }
    
    protected function getPropertyBagTemplate()
    {
        return '<?php

%namespace%


%use%

%abstract%class %class% extends %parent%
{
    //CustomCodeBegin
    
    //CustomCodeEnd
    
    //GeneratedCodeBegin
%propertyConstants%
%initProperty%
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
    }
    ';
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
    
    protected function selfConstantTemplate()
    {
        return 'self::%screamName%';
    }
    
    protected function constPropertyTemplate()
    {
        return "const %screamName% = '%name%';";
    }
    
    protected function getterTemplate()
    {
        return '    public function get%ucfirstName%()
    {
        return $this->get(%selfConstant%);
    }
    ';
    }
    
    protected function setterTemplate()
    {
        return '    public function set%ucfirstName%($%camelName%)
    {
        $this->set(%selfConstant%, $%camelName%);
        
        return $this;
    }
    ';
    }
    
    protected function useTemplate()
    {
        return 'use %class%;';
    }
    
    protected function arrayPropertyTemplate()
    {
        return '%selfConstant% => %default%,';
    }
}
