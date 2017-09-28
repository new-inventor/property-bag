<?php

namespace PropertyBag\Normalizer;


use NewInventor\PropertyBag\Normalizer\CamelCaseNormalizer;
use NewInventor\PropertyBag\Normalizer\StringNormalizer;
use NewInventor\TypeChecker\Exception\TypeException;

class CamelCaseNormalizerTest extends \Codeception\Test\Unit
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
    
    // tests
    public function testNormalizer()
    {
        $normalizer = CamelCaseNormalizer::make();
        $this->assertSame('qweFqweDasd', $normalizer->normalize('qweFqweDasd'));
        $this->assertSame('qweFqweDasd', $normalizer->normalize('qwe_fqwe_dasd'));
        $this->assertSame('qweFqweDasd', $normalizer->normalize('qwe-fqwe_dasd'));
        $this->assertSame('qweFqweDasd', $normalizer->normalize('qwe fqwe_dasd'));
        $this->assertSame('qweFqweDasd0934', $normalizer->normalize('qwe fqwe_dasd093[4]'));
        $this->assertSame('qweFqweDasd093SdfSdfsdf', $normalizer->normalize('qwe fqwe_dasd093[sdf][sdfsdf]'));
    }
    
    public function testNormalizer1()
    {
        $normalizer = CamelCaseNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize(new StringNormalizer());
    }
    
    public function testNormalizer2()
    {
        $normalizer = CamelCaseNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize(['11', '111']);
    }
}