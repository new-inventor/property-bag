<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 06.09.17
 */

namespace TestsDataStructure;


use NewInventor\DataStructure\PropertyBag;

class TestBag3 extends PropertyBag
{
    protected function initProperties(): void
    {
        $this->properties = [
            'prop1' => null,
            'prop2' => 1,
            'prop3' => null,
            'prop4' => null,
        ];
    }
    
    public function getProp1()
    {
        return $this->properties['prop1'];
    }
    
    public function getProp2()
    {
        return $this->properties['prop2'];
    }
    
    public function getProp3()
    {
        return $this->properties['prop3'];
    }
    
    public function getProp4()
    {
        return $this->properties['prop4'];
    }
}