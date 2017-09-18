# Property bag
This utility provide to you some normalizers and formatters plus class that handle properties as normalizable and formattable objects.

#### Installation

composer require new-inventor/property-bag

### Simple usage of normalizer

```php
$normalizer = new IntNormalizer();
$value = $normalizer->normalize($value);
or
$value = IntNormalizer::make()->normalize($value);
```

### Simple usage of formatter

```php
$formatter = new ArrayFormatter();
$value = $formatter->format($value);
or
$value = ArrayFormatter::make()->format($value);
```

**Normalizers** normalize values from different types to needed type

**Formatters** format values to strings

Some normalizers and formatters have parameters in constructor.

### Signatures of normalizer constructors
* ArrayNormalizer - __construct(...$normalizers) // normalizers must implement NormalizerInterface
* BoolNormalizer - __construct(array $true = [], array $false = [])
* CsvRowNormalizer - __construct(...$normalizers) // normalizers must implement NormalizerInterface
* CurrencyNormalizer - __construct()
* DateTimeNormalizer - __construct(string $format = 'd.m.Y H:i:s')
* EmailNormalizer - __construct()
* EmptyNormalizer - __construct()
* EnumNormalizer - __construct(array $availableValues, NormalizerInterface $normalizer = null)
* FloatNormalizer - __construct()
* FloatRangeNormalizer - __construct(float $min = null, float $max = null)
* IntNormalizer - __construct()
* IntRangeNormalizer - __construct(int $min = null, int $max = null)
* MccNormalizer - __construct()
* PhoneNormalizer - __construct()
* PropertyBagNormalizer - __construct(string $class) // class must implement PropertyBagInterface
* RangeNormalizer - __construct($min = null, $max = null, callable $compareFunction = null)
* RegExpNormalizer - __construct(string $regExp)
* StringNormalizer - __construct()

### Signatures of formatter constructors
* ArrayFormatter - __construct(...$formatters) // formatters must implement FormatterInterface
* BoolFormatter - __construct(string $true = '1', string $false = '0')
* DateTimeFormatter - __construct(string $format = 'd.m.Y H:i:s')

## Usage of Property
Property is the representation of property that can be prepared and formatted

If you does not provide normalizer or formatter, then value will be passed as is.

You can do some actions like this:
```php
$property = new Property();
$property->setNormalizer(DateTimeNormalizer('d.m.Y'));
$property->setFormatter(DateTimeFormatter('d.m.Y'));
or
$property = Property::make()
    ->setNormalizer(DateTimeNormalizer('d.m.Y'))
    ->setFormatter(DateTimeFormatter('d.m.Y'));

$property->setValue('2000');// NormalizerException throwed
$property->setValue('12.02.2000');
$property->getValue(); // \DateTime object
$property->getFormattedValue(); // '12.02.2000'
```

## Usage of PropertyBag

Property bag can work as **stand alone** object or as **parent** for custom class (the second way is preferred).

```php
$propertyBag = new PropertyBag();
```

#### As stand alone
When it works as stand alone object, properties can-not be cached by inner mechanism so you should write your own cache.

You can add property by calling this method:
```php
$propertyBag->addProperty('name', new Property());
```

Then you can set/get/getFormatted value of property as shown below:
```php
$propertyBag->set('name', $value);
$name = $propertyBag->get('name');
$name = $propertyBag->getFormatted('name');
```

Also you can load array into the property bag:

```php
$propertyBag->load([
    'name0' => $value0,
    'name1' => $value1,
    'name2' => $value2,
]);
```

If property name does not exists in property bag the PropertyNotFoundException will be thrown.

If you want to convert property bag to array, you can call one of this two methods:

```php
$propertyBag->toRawArray();// Return array of values by calling get() method
$propertyBag->toFormattedArray(); // Return array of values by calling getFormatted() method
```

If property value is `null` no property is returned.

#### As parent

You can do all staff of stand alone object but you should overwrite the getProperties method, like this:

```php
class Parameters {
    protected function getProperties(): array
    {
        return [
            'id' => Property::make()->setNormalizer(new IntNormalizer()),
            'name' => Property::make()->setNormalizer(new StringNormalizer()),
            'createdAt' => Property::make()
                ->setNormalizer(DateTimeNormalizer::make('d.m.Y'))
                ->setFormatter(DateTimeFormatter::make('d.m.Y')),
        ];
    }
}
```

In this case you can specify the cache driver
```php
PropertyBag::setCacheDriver(<Psr\SimpleCache\CacheInterface>);
```
and properties will be cached automatically.

**You should remember that anonymous functions can not be cached**

Now you can set and get properties like in 'Stand alone' section.

### Custom normalizers and formatters

* Custom normalizers should implement NormalizerInterface or extend AbstractNormalizer class
* Custom formatters should implement FormatterInterface or extend AbstractFormatter class

To cache this custom normalizers/formatters you should overwrite `preloadClasses` method:
```php
    protected function preloadClasses()
    {
        parent:: preloadClasses();
        CustomNormalizer::class;
        CustomFormatter::class;
    }
```
to preload classes before unserialize;
