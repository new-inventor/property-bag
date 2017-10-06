<?php

namespace PropertyBag;


use Codeception\Test\Unit;
use NewInventor\DataStructure\DataStructureMetadata;
use NewInventor\DataStructure\Transformer\ArrayToCsvString;
use NewInventor\DataStructure\Transformer\BoolToMixed;
use NewInventor\DataStructure\Transformer\ChainTransformer;
use NewInventor\DataStructure\Transformer\ToBool;
use NewInventor\DataStructure\Transformer\ToInt;

class DataStructureMetadataTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }
    
    protected function _after()
    {
    }
    
    public function test()
    {
        $meta = new DataStructureMetadata();
        $meta->loadConfig(__DIR__ . '/data/TestBag.yml');
        $this->assertSame('TestsDataStructure', $meta->getNamespace());
        $this->assertSame('Some1\Some2\Some3\Parent', $meta->getParent());
        $this->assertSame('TestBag', $meta->getClassName());
        $this->assertSame('TestsDataStructure\TestBag', $meta->getFullClassName());
        $this->assertTrue($meta->isAbstract());
        $this->assertSame(['prop2', 'prop3', 'prop4', 'prop5', 'prop6', 'prop7', 'prop8'], $meta->getGetters());
        $this->assertSame(['prop0'], $meta->getSetters());
        $transformers = $meta->getForwardTransformer();
        $this->assertNull($transformers->getTransformer('prop0'));
        $this->assertSame(ToInt::class, get_class($transformers->getTransformer('prop1')));
        $this->assertSame(ToInt::class, get_class($transformers->getTransformer('prop2')));
        $this->assertNull($transformers->getTransformer('prop3'));
        $this->assertSame(ToBool::class, get_class($transformers->getTransformer('prop4')));
        $this->assertSame(ToBool::class, get_class($transformers->getTransformer('prop5')));
        $this->assertSame(ChainTransformer::class, get_class($transformers->getTransformer('prop6')));
        $this->assertSame(ChainTransformer::class, get_class($transformers->getTransformer('prop7')));
        $transformers = $meta->getBackwardTransformer();
        $this->assertNull($transformers->getTransformer('prop0'));
        $this->assertNull($transformers->getTransformer('prop1'));
        $this->assertNull($transformers->getTransformer('prop2'));
        $this->assertNull($transformers->getTransformer('prop3'));
        $this->assertSame(BoolToMixed::class, get_class($transformers->getTransformer('prop4')));
        $this->assertSame(BoolToMixed::class, get_class($transformers->getTransformer('prop5')));
        $this->assertSame(ArrayToCsvString::class, get_class($transformers->getTransformer('prop6')));
        $this->assertNull($transformers->getTransformer('prop7'));
        $validators = $meta->getClassValidationMetadata();
        $this->assertSame(['prop1', 'prop2'], array_keys($validators->getters));
        $this->assertSame(
            ['Symfony\Component\Validator\Constraints\GreaterThan'],
            array_map(
                function (&$item) {
                    return get_class($item);
                },
                $validators->getters['prop1']->constraints
            )
        );
        $this->assertSame(
            [
                'Symfony\Component\Validator\Constraints\GreaterThan',
                'Symfony\Component\Validator\Constraints\LessThanOrEqual',
            ],
            array_map(
                function (&$item) {
                    return get_class($item);
                },
                $validators->getters['prop2']->constraints
            )
        );
        $this->assertSame(['prop0'], array_keys($validators->properties));
        $this->assertSame(
            [
                'Symfony\Component\Validator\Constraints\GreaterThan',
            ],
            array_map(
                function (&$item) {
                    return get_class($item);
                },
                $validators->properties['prop0']->constraints
            )
        );
    }
}