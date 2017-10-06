<?php
/**
 * Project: property-bag
 * User: george
 * Date: 06.10.17
 */

namespace NewInventor\DataStructure\Transformer\Property;


use NewInventor\TypeChecker\TypeChecker;

class Utf8StringToAsciiString extends Transformer
{
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)->tstring()->fail();
    }
    
    protected function transformInputValue($value)
    {
        $target = '';
        $strLen = mb_strlen($value);
        for ($i = 0; $i < $strLen; $i++) {
            $char = mb_substr($value, $i, 1);
            if ($this->isAscii($char)) {
                $target .= iconv('UTF-8', 'ASCII', $char);
            } else {
                $target .= iconv('UTF-8', 'ASCII', $this->ordutf8($char));
            }
        }
        
        return $target;
    }
    
    protected function isAscii($char)
    {
        return mb_detect_encoding($char, 'ASCII', true) === 'ASCII';
    }
    
    protected function ordutf8($string)
    {
        $offset = 0;
        $code = ord($string[$offset]);
        if ($code >= 128) {        //otherwise 0xxxxxxx
            $bytesnumber = 2;
            if ($code < 224) {
                $bytesnumber = 2;
            }                //110xxxxx
            else if ($code < 240) {
                $bytesnumber = 3;
            }        //1110xxxx
            else if ($code < 248) {
                $bytesnumber = 4;
            }    //11110xxx
            $codetemp = $code - 192 - ($bytesnumber > 2 ? 32 : 0) - ($bytesnumber > 3 ? 16 : 0);
            for ($i = 2; $i <= $bytesnumber; $i++) {
                $code2 = ord($string[++$offset]) - 128;        //10xxxxxx
                $codetemp = $codetemp * 64 + $code2;
            }
            $code = $codetemp;
        }
        
        return 'U+' . strtoupper(dechex($code));
    }
}