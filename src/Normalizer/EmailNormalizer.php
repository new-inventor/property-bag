<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 12.09.17
 */

namespace NewInventor\PropertyBag\Normalizer;


class EmailNormalizer extends RegExpNormalizer
{
    /**
     * PhoneNormalizer constructor.
     */
    public function __construct()
    {
        parent::__construct($this->getEmailRegexp());
    }
    
    public function getEmailRegexp()
    {
        //ABNF Базовые правила / Начало
        $ALPHA = "[\x41-\x5A\x61-\x7A]";
        $BIT = '(?:0|1)';
        $CHAR = "[\x01-\x7F]";
        $LF = "\x0A";
        $CR = "\x0D";
        $CRLF = "(?:{$CR}{$LF})";
        $CTL = "[\x00-\x1F\x7F]";
        $DIGIT = "[\x30-\x39]";
        $DQUOTE = "\x22";
        $HEXDIG = "(?:$DIGIT|A|B|C|D|E|F)";
        $OCTET = "[\x00-\xFF]";
        $VCHAR = "[\x21-\x7E]";
        $SP = ' ';
        $HTAB = "\x09";
        $WSP = "(?:{$SP}|{$HTAB})";
        $LWSP = "(?:(?:{$WSP}|{$CRLF}{$WSP})*)";
        //ABNF Базовые правила / Конец
        
        //RFC 5322 / Начало
        $FWS = "(?:(?:{$WSP}*{$CRLF})?{$WSP}+)";
        $ctext = '[!-\'*-\\[\\]-~]';
        $quotedPair = "(?:\\\\(?:{$VCHAR}|{$WSP}))";
        $ccontent = "(?:{$ctext}|{$quotedPair})";
        $comment = "(?:\\((?:{$FWS}?{$ccontent})*{$FWS}?\\))";
        $CFWS = "(?:(?:{$FWS}?{$comment})+{$FWS}?|{$FWS})";
        $signs = '[-!#$%&\'*+\\/=?^_`{|\\}~\\\\]';
        $atext = "(?:{$ALPHA}|{$DIGIT}|{$signs})";
        $atom = "(?:{$CFWS}?{$atext}+{$CFWS}?)";
        $dotAtomText = "(?:{$atext}+(?:\\.{$atext}+)*)";
        $dotAtom = "(?:{$CFWS}?{$dotAtomText}{$CFWS}?)";
        $specials = "(?:[\\(\\)<>\\[\\]:;@\\,.]|{$DQUOTE})";
        $qtext = '[!-\'*-\\[\\]-~]';
        $qcontent = "(?:{$qtext}|{$quotedPair})";
        $quotedString = "(?:{$CFWS}?{$DQUOTE}(?:{$FWS}?{$qcontent})*{$FWS}?{$DQUOTE}{$CFWS}?)";
        $word = "(?:{$atom}|{$quotedString})";
        $phrase = "(?:{$word}+)";
        $unstructured = "(?:(?:{$FWS}?{$VCHAR})*{$WSP}*)";
        $dayName = '(?:Mon|Tue|Wed|Thu|Fri|Sat|Sun)';
        $dayOfWeek = "(?:{$FWS}?{$dayName})";
        $day = "(?:{$FWS}?{$DIGIT}{1,2}{$FWS})";
        $month = '(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)';
        $year = "(?:{$FWS}{$DIGIT}{4}{$FWS})";
        $date = "(?:{$day}{$month}{$year})";
        $hour = "(?:{$DIGIT}{2})";
        $minute = "(?:{$DIGIT}{2})";
        $second = "(?:{$DIGIT}{2})";
        $timeOfDay = "(?:{$hour}:{$minute}(?:\\:{$second})?)";
        $zone = "(?:{$FWS}(?:\\+|-){$DIGIT}{4})";
        $time = "(?:{$timeOfDay}{$zone})";
        $dateTime = "(?:{$dayOfWeek}|,)?{$date}{$time}";
        $displayName = $phrase;
        $dtext = "[\x21-\x5A\x5E-\x7E]";
        $domainLiteral = "(?:{$CFWS}?\\[(?:{$FWS}{$dtext})*{$FWS}?\\]{$CFWS})";
        $domain = "(?:{$dotAtom}|{$domainLiteral})";
        $localPart = "(?:{$dotAtom}|{$quotedString})";
        $addrSpec = "(?:{$localPart}@{$domain})";
        $angleAddr = "(?:{$CFWS}?<{$addrSpec}>{$CFWS}?)";
        $nameAddr = "(?:{$displayName}?{$angleAddr})";
        $mailbox = "(?:{$nameAddr}|{$addrSpec})";
        $mailboxList = "(?:{$mailbox}(?:,{$mailbox}))";
        $groupList = "(?:{$mailboxList}|{$CFWS})";
        $group = "(?:{$displayName}:$groupList?;{$CFWS}?)";
        $address = "(?:{$mailbox}|{$group})";
        $addressList = "(?:{$address}(?:,{$address}))";
        
        //RFC 5322 / Конец
        
        return "/{$addrSpec}/";
    }
    
    protected function normalizeInputValue($value)
    {
        return trim($value);
    }
}