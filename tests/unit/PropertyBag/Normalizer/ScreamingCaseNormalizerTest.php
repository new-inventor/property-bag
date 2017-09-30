<?php

namespace PropertyBag\Normalizer;


use NewInventor\PropertyBag\Normalizer\ScreamingCaseNormalizer;
use NewInventor\PropertyBag\Normalizer\StringNormalizer;
use NewInventor\TypeChecker\Exception\TypeException;

class ScreamingCaseNormalizerTest extends \Codeception\Test\Unit
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
        $normalizer = ScreamingCaseNormalizer::make();
        $this->assertSame('QWE_FQWE_DASD', $normalizer->normalize('qweFqweDasd'));
        $this->assertSame('QWE_FQWE_DASD', $normalizer->normalize('qwe_fqwe_dasd'));
        $this->assertSame('QWE_FQWE_DASD_FDHF', $normalizer->normalize('qwe-fqwe_dasdFdhf  '));
        $this->assertSame('QWE_FQWE_DASD', $normalizer->normalize('qwe fqwe_dasd'));
        $this->assertSame('QWE_FQWE_DASD_093_4', $normalizer->normalize('qwe fqwe_dasd093[4]'));
        $this->assertSame('QWE_FQWE_DASD_093_SDF_SDFSDF', $normalizer->normalize('qwe fqwe_dasd093[sdf][sdfsdf]'));
    }
    
    public function testNormalizer1()
    {
        $normalizer = ScreamingCaseNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize(new \stdClass());
    }
    
    public function testNormalizer2()
    {
        $normalizer = ScreamingCaseNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize(['11', '111']);
    }
}