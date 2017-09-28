<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 27.09.17
 */

namespace TPMailSender\ApiBundle\Command;


use NewInventor\PropertyBag\Formatter\FormatterInterface;
use NewInventor\PropertyBag\Normalizer\NormalizerInterface;
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
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \LogicException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $configPath = $this->getAbsolutePath($input->getArgument('config'));
        $outputPath = $this->getAbsolutePath($input->getArgument('output-dir'));
        $baseNamespace = $input->getOption('base-namespace');
        $force = $input->getOption('force');
        if (!is_dir($outputPath)) {
            $this->fileSystem->mkdir($outputPath);
        }
        $output->writeln('Start generation');
        if (is_file($configPath)) {
            $this->generateBagFromFile($configPath, $outputPath, $baseNamespace, $force);
            $output->writeln("File '$configPath' processed");
        } else if (is_dir($configPath)) {
            $files = $this->getFilesInDir($configPath, ['yml']);
            foreach ($files as $file) {
                $this->generateBagFromFile($file, $outputPath, $baseNamespace, $force);
                $output->writeln("File '$file' processed");
            }
        }
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
    
    protected function generateBagFromFile(string $configPath, string $outputPath, string $baseNamespace, bool $force)
    {
        $config = Yaml::parse(file_get_contents($configPath));
        $template = $this->getPropertyBagTemplate();
        $namespace = $config['namespace'] ?? '';
        $className = pathinfo($configPath, PATHINFO_FILENAME);
        $template = str_replace(
            ['%namespace%', '%class%'],
            ["namespace $namespace", $className],
            $template
        );
        $getters = [];
        $setters = [];
        $properties = [];
        foreach ($config['properties'] as $property => $propertyConfig) {
            $propertyStr = str_replace('%name%', $property, $this->propertyTemplate());
            $normalizer = '';
            $formatter = '';
            $default = '';
            if (is_string($propertyConfig)) {
                $normalizer = '->setNormalizer(' . $this->generateNormalizer($propertyConfig) . ')';
            } else if (is_array($propertyConfig)) {
                if (isset($propertyConfig['normalizer'])) {
                    $normalizer = '->setNormalizer(' . $this->generateNormalizer($propertyConfig['normalizer']) . ')';
                }
                if (isset($propertyConfig['formatter'])) {
                    $formatter = '->setFormatter(' . $this->generateFormatter($propertyConfig['formatter']) . ')';
                }
                if (isset($propertyConfig['default'])) {
                    $default = $this->generateDefault($propertyConfig['default']);
                }
            }
            $propertyStr = str_replace(
                ['%normalizer%', '%formatter%', '%default%'],
                [$normalizer, $formatter, $default],
                $propertyStr
            );
            $properties[] = $propertyStr;
            if (isset($config['getters']) && $config['getters']) {
                $getterStr = str_replace(
                    ['%name%', '%ucfirstName%'],
                    [$property, ucfirst($property)],
                    $this->getterTemplate()
                );
                $getters[] = $getterStr;
            }
            if (isset($config['setters']) && $config['setters']) {
                $setterStr = str_replace(
                    ['%name%', '%ucfirstName%'],
                    [$property, ucfirst($property)],
                    $this->setterTemplate()
                );
                $setters[] = $setterStr;
            }
        }
        $template = str_replace(
            ['%properties%', '%getters%', '%setters%'],
            [implode(",\n", $properties), implode($getters), implode($setters)],
            $template
        );
        /** @noinspection NotOptimalRegularExpressionsInspection */
        preg_match_all('/\\\\?\w+(?:\\\\\w+)+(?=::|->|\(|\n)/', $template, $uses);
        $uses = array_unique($uses[0]);
        foreach ($uses as &$use) {
            $use = str_replace('%class%', $use, $this->useTemplate());
        }
        unset($use);
        $template = str_replace('%use%', implode($uses), $template);
        /** @noinspection NotOptimalRegularExpressionsInspection */
        $template = preg_replace('/(\\\\?\w+(?:\\\\\w+)*\\\\)(?=\w+(?:::|->|\(|\n))/', '', $template);
        
        $dirName = str_replace(
            '\\',
            '/',
            $outputPath . DIRECTORY_SEPARATOR . str_replace($baseNamespace, '', $config['namespace'])
        );
        $this->fileSystem->mkdir($dirName);
        
        $fileName = $dirName . DIRECTORY_SEPARATOR . $className . '.php';
        $currentContent = file_get_contents($fileName);
        if (
            !$force &&
            file_exists($fileName) &&
            preg_match('/\\/\\/place custom code below\\n(.+)\\}/s', $currentContent, $matches)
        ) {
            $template = str_replace(
                "//place custom code below\n",
                "//place custom code below\n{$matches[1]}",
                $template
            );
        }
        $this->fileSystem->dumpFile($fileName, $template);
    }
    
    protected function generateNormalizer($normalizer): string
    {
        $res = [];
        if (is_string($normalizer)) {
            $res['fullName'] = $this->getNormalizerFullName($normalizer);
            $res['parameters'] = '';
        } else if (is_array($normalizer)) {
            $res['fullName'] = $this->getNormalizerFullName($normalizer['name']);
            $res['parameters'] = $this->generateParameters($normalizer['params']);
        } else {
            throw new \InvalidArgumentException('Normalizer must be string or array');
        }
        $template = $this->normalizerTemplate();
        
        return str_replace(['%normalizerName%', '%normalizerParameters%'], $res, $template);
    }
    
    protected function getNormalizerFullName(string $name): string
    {
        if (!class_exists($name) || !is_a($name, NormalizerInterface::class)) {
            return 'NewInventor\PropertyBag\Normalizer\\' . ucfirst($name) . 'Normalizer';
        }
        
        return $name;
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
            } else if (is_scalar($parameter)) {
                $res[] = (string)$parameter;
            } else if (is_array($parameter)) {
                TypeChecker::check($parameter)->inner()->tscalar()->fail();
                $data = [];
                foreach ($parameter as $name => $value) {
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
    
    protected function generateFormatter($normalizer)
    {
        $res = [];
        if (is_string($normalizer)) {
            $res['fullName'] = $this->getFormatterFullName($normalizer);
            $res['parameters'] = '';
        } else if (is_array($normalizer)) {
            $res['fullName'] = $this->getFormatterFullName($normalizer['name']);
            $res['parameters'] = $this->generateParameters($normalizer['params']);
        } else {
            throw new \InvalidArgumentException('Formatter must be string or array');
        }
        $template = $this->formatterTemplate();
        
        return str_replace(['%formatterName%', '%formatterParameters%'], $res, $template);
    }
    
    protected function getFormatterFullName($name)
    {
        if (!class_exists($name) || !is_a($name, FormatterInterface::class)) {
            return 'NewInventor\PropertyBag\Formatter\\' . ucfirst($name) . 'Formatter';
        }
        
        return $name;
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
class %class% extends NewInventor\PropertyBag\PropertyBag
{
    //Auto generated code begin
    protected function getProperties(): array
    {
        return [
%properties%
        ];
    }
    %getters%%setters%
    //Auto generated code end
    //Place custom code below
}';
    }
    
    protected function getterTemplate()
    {
        return '
    public function get%ucfirstName%()
    {
        return $this->properties[\'%name%\']->getValue();
    }
    ';
    }
    
    protected function setterTemplate()
    {
        return '
    public function set%ucfirstName%($%name%)
    {
        $this->properties[\'%name%\']->setValue($%name%);
        
        return $this;
    }
    ';
    }
    
    protected function useTemplate()
    {
        return "use %class%;\n";
    }
    
    protected function propertyTemplate()
    {
        return "            '%name%' => NewInventor\\PropertyBag\\Property::make(%default%)%normalizer%%formatter%";
    }
    
    protected function normalizerTemplate()
    {
        return 'new %normalizerName%(%normalizerParameters%)';
    }
    
    protected function formatterTemplate()
    {
        return 'new %formatterName%(%formatterParameters%)';
    }
}