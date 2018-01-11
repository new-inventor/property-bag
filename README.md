# Property bag
This utility provide to you:
* base property bag class
* property bab interface
* configuration for property bag
* metadata classes for property bag
* generator command to generate classes from configuration

#### Installation

composer require new-inventor/property-bag


## PropertyBag usage

### Property bag creation

1. Create property bag configuration.
2. run `php bin/console generate:bag <configuration path> <destination path> [--base-namespace="Base\Namespase"] [-f(to force rewrite destination files)]`
3. check if file exist in destination folder
4. if you want, you can write some code between comments `CustomCodeBegin` and `CustomCodeEnd`

### Stand alone object with no configuration
```php
$propertyBag = new PropertyBag();
$propertyBag->add('time', new \DateTime());
$propertyBag->add('value');
$propertyBag->get('time');
$propertyBag->set('value', 123);
$propertyBag->get('value');
```

### Object with configuration

#### Configuration file
```yaml
parent: Some1\Some2\Some3\Parent
abstract: true
validation:
  constraints:
    - Callback: ['TestsDataStructure\TestStatic', 'GetTrue']
  getters:
    prop1:
      - GreaterThan:
          value: 0
          message: "Field 'prop1' must be greater than {{ compared_value }}"
  properties:
    prop0:
      - GreaterThan: 0
properties:
  prop1: NewInventor\Transformers\Transformer\ToInt
  prop2:
    transformers:
      - ToInt: ~
    validation:
      - GreaterThan:
          value: 5
          message: "Field 'prop2' must be > {{ compared_value }}"
      - LessThanOrEqual:
          value: 1000
          message: "Field 'prop2' must be <= {{ compared_value }}"
  prop3:
    transformers:
      - ToBool:
          - ['TestsDataStructure\TestStatic', 'GetTrue']
  prop4:
    transformers:
      - ToBool:
          - groups: forward
      - BoolToMixed:
          - static: ['TestsDataStructure\TestStatic', 'bbb']
          - const: ['TestsDataStructure\TestStatic', 'AAA']
          - groups: backward
  prop5:
    transformers:
      - ToBool:
          - groups: forward
      - BoolToMixed:
          - 1
          - 0
          - groups: backward
  prop6:
    transformers:
      - ToString:
          - groups: forward
      - CsvStringToArray:
          - groups: forward
      - InnerTransformer:
          - groups: forward
          - ToInt: ~
      - ArrayToCsvString:
          - groups: backward
  prop7:
    default: 2222
    transformers:
      - ToString: ~
      - StringToDateTime:
          - 'd.m.Y'
          - groups: forward
  prop8:
    nested:
      class: TestsDataStructure\TestBag2
      array: true
  prop9:
    nested:
      class: TestsDataStructure\TestBag1
getters:
  generate: true
  except:
    - prop0
    - prop1
setters:
  generate: true
  only:
    - prop0
```

Where:
* parent(root) - parent class. If does not specified, then `NewInventor\PropertyBag\PropertyBag`
* abstract(root) - is generated class abstract
* validation(root) - symfony validation config
* properties(root) - list of properties
* validation(in property) - symfony validation for class getter
* transformers(in property) - short class names from [https://github.com/new-inventor/transformers] or full class name from you code
* nested(in property) - property is nested class so it must provide class and can provide array key(to be an array)
* default(in property) - default value of property 
* getters(root) - can be bool or object. Which getters generate in class.
* setters(root) - can be bool or object. Which setters generate in class.

In transformers you can specify group, transformer parameters. In `InnerTransformer` and `ChainTransformer` you must pass as parameters another Transformers. 

If you do not specify the group, then it will be 'default'.

You can pass to parameters scalars, arrays, callable, and special arrays with one element(const, static).

#### Usage

```php
//We use parser to parse configuration files into array.
//Configuration is a description of configuration fields
$parser = new NewInventor\DataStructure\Configuration\Parser\Yaml(new NewInventor\PropertyBag\Configuration\Configuration())

// MetadataLoader constructs the metadata object with transformers parameters and nested elements. It loads config from path by class name and removes from class name BaseNamespace 
$metadataLoader = new NewInventor\PropertyBag\Metadata\Loader('/path/To/Generated/Bags', $parser, 'Base\Namespace');
//Factory is the main class that loads Metadata and caches it.
$metadataFactory = new NewInventor\PropertyBag\Metadata\Factory($loader, Psr\Cache\CacheItemPoolInterface);

// Validation loader is the wrapper for Symfony metadata loader. it parses config file and loads class validation
$validationLoader = new NewInventor\DataStructure\Validation\Loader('/path/To/Generated/Bags', $parser, 'Base\Namespace');
// Factory is the class that construct validator object.
$validationFactory = new NewInventor\DataStructure\Validation\Factory($loader, Symfony\Component\Validator\Mapping\Cache\CacheInterface);
// TestBag class is generated(or Written) propertyBag
$bag = new TestBag();
$values = [1, 'qwe', 4, 'qweqw'];

$metadataLoader = new NewInventor\PropertyBag\Metadata\Loader('/path/To/Generated/Bags', $parser, 'Base\Namespace');
$metadataFactory = new NewInventor\PropertyBag\Metadata\Factory($loader, Psr\Cache\CacheItemPoolInterface);
//transformer make transformation on arrays and prepare data to loading to the object
$transformer = $metadataFactory->getMetadataFor(TestBag::class)->getTransformer('default');
$values = $transfirmer->transform($values);
//method loads values to the object from array
$bag->load($values);
$validationLoader = new NewInventor\DataStructure\Validation\Loader('/path/To/Generated/Bags', $parser, 'Base\Namespace');
$validationFactory = new NewInventor\DataStructure\Validation\Factory($loader, Symfony\Component\Validator\Mapping\Cache\CacheInterface);
//If validation fails then $errors will be a not empty array otherwise it will be an empty array
$errors = $validationFactory->getValidator()->validate($bag)->getErrors();
//after this preparations object will be clear? walid and ready to manipulations on it.
... do some staff

```
