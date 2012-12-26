<?php

class UTF8Functions
{
	// UTF8 string length checker
	function utf8_strlen($str) {
		$i = 0; $count = 0; $len = strlen ($str);
		while ($i < $len) {
			$chr = ord ($str[$i]); $count++; $i++;
		   	if ($i >= $len) break;
			if ($chr & 0x80) { $chr <<= 1; while ($chr & 0x80) { $i++; $chr <<= 1; } } }
		return $count; }


	// UTF8 substring
	function utf8_substr($str,$from,$len) {
		return preg_replace('/^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*/s',"$1",$str);
	}


	// UTF8 string reverse
	function utf8_strrev($str){
		 preg_match_all('/./us', $str, $ar);
		 return join('',array_reverse($ar[0]));
	}


	// UTF8 str_replace
	function utf8_str_replace($search, $replace, $string) {
		$nstring = "";
		for($i=0; $i < utf8_strlen($string); $i++) {
			$chr = utf8_substr($string,$i,1);
			$nstring .= str_replace($search,$replace,$chr);
		}
		return $nstring;
	}


	// UTF8 ord
	function utf8_ord($c) {
		$ud = 0;
		if (ord($c{0})>=0 && ord($c{0})<=127)
			$ud = ord($c{0});
		if (ord($c{0})>=192 && ord($c{0})<=223)
			$ud = (ord($c{0})-192)*64 + (ord($c{1})-128);
		if (ord($c{0})>=224 && ord($c{0})<=239)
			$ud = (ord($c{0})-224)*4096 + (ord($c{1})-128)*64 + (ord($c{2})-128);
		if (ord($c{0})>=240 && ord($c{0})<=247)
			$ud = (ord($c{0})-240)*262144 + (ord($c{1})-128)*4096 + (ord($c{2})-128)*64 + (ord($c{3})-128);
		if (ord($c{0})>=248 && ord($c{0})<=251)
			$ud = (ord($c{0})-248)*16777216 + (ord($c{1})-128)*262144 + (ord($c{2})-128)*4096 + (ord($c{3})-128)*64 + (ord($c{4})-128);
		if (ord($c{0})>=252 && ord($c{0})<=253)
			$ud = (ord($c{0})-252)*1073741824 + (ord($c{1})-128)*16777216 + (ord($c{2})-128)*262144 + (ord($c{3})-128)*4096 + (ord($c{4})-128)*64 + (ord($c{5})-128);
		if (ord($c{0})>=254 && ord($c{0})<=255) //error
			$ud = false;
		return $ud;
	}


	// UTF8 chr
	function utf8_chr($dec) {
		if ($dec < 128) {
			$utf = chr($dec);
		} else if ($dec < 2048) {
			$utf = chr(192 + (($dec - ($dec % 64)) / 64));
			$utf .= chr(128 + ($dec % 64));
		} else {
			$utf = chr(224 + (($dec - ($dec % 4096)) / 4096));
			$utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
			$utf .= chr(128 + ($dec % 64));
		}
		return $utf;
	}

	// UTF8 urldecode
	function utf8_urldecode($url)
	{
		preg_match_all('/%u([[:alnum:]]{4})/', $url, $a);
		foreach ($a[1] as $uniord) {
			$dec = hexdec($uniord);
			$utf = '';

			if ($dec < 128) { $utf = chr($dec); }
			else if ($dec < 2048) {
				$utf = chr(192 + (($dec - ($dec % 64)) / 64));
				$utf .= chr(128 + ($dec % 64)); }
			else {
				$utf = chr(224 + (($dec - ($dec % 4096)) / 4096));
				$utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
				$utf .= chr(128 + ($dec % 64)); }
			$url = str_replace('%u'.$uniord, $utf, $url); }
		return urldecode($url);
	}

	// UTF8 rawurldecode
	function utf8_rawurldecode($source)
	{
		$decodedStr = "";
		$pos = 0;
		$len = strlen ($source);
		while ($pos < $len)
		{
			$charAt = substr ($source, $pos, 1);
			if ($charAt == '%')
			{
				$pos++;
				$charAt = substr ($source, $pos, 1);
				if ($charAt == 'u')
				{
					// we got a unicode character
					$pos++;
					$unicodeHexVal = substr ($source, $pos, 4);
					$unicode = hexdec ($unicodeHexVal);
					$entity = "&#". $unicode . ';';
					$decodedStr .= utf8_encode ($entity);
					$pos += 4;
				} else {
					// we have an escaped ascii character
					$hexVal = substr ($source, $pos, 2);
					$decodedStr .= chr (hexdec ($hexVal));
					$pos += 2;
				}
			} else {
				$decodedStr .= $charAt;
				$pos++;
			}
		}
		return $decodedStr;
	}
}

$UTF8Functions = new UTF8Functions();
?>