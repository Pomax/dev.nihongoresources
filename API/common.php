<?php

	// check an array for the element position
	function array_pos($element, $array) {
		for($np=0; $np<count($array); $np++) {
			if($array[$np]==$element) { return $np; } }
		return false; }

	// replace wildcard for SQL 'RLIKE' searching
	function wildcard_for_SQL_RLIKE($string) {
		return str_replace(array("*","＊"), ".*", $string); }

	// replace wildcard for SQL 'LIKE' searching
	function wildcard_for_SQL_LIKE($string) {
		return str_replace(array("*","＊"), "%", $string); }

	// replace wildcard for SQL 'LIKE' searching to the one for 'RLIKE' searching
	function wildcard_for_SQL_LIKE_to_RLIKE($string) {
		return str_replace("%", ".*", $string); }

	// quote replacement for web->SQL
	function web_to_SQL_quotes($string) {
		return str_replace(array('"',"'",";","　"),array("ʹ","ʺ","",""),$string); }

	// quote replacement for SQL->web
	function SQL_to_web_quotes($string) {
		return str_replace(array("ʹ","ʺ"),array('"',"'"),$string); }

	// unicode version of chr() function
	function unichr($u) { return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES'); }

	// links any kanji in the given string to the kanji lookup page
	function link_kanji($string) {
		return preg_replace("/([\x{3400}-\x{4DBF}\x{4E00}-\x{9FFF}\x{F900}-\x{FAFF}])/u","<a class='klink' href='/kanji/lookup.php?kanji=$1'>$1</a>",$string); }

	// extensible function for checking HTTP GET arguments
	function has_GET_argument($name) {
		return isset($_GET[$name]); }

	// method for safely getting HTTP GET arguments
	function get_GET_argument($name, &$error='') {
		if(isset($_GET[$name])) {
			$temp = $_GET[$name];
			$new = str_replace(array(';','+'),array('',' '),$temp);
			if($new != $temp) { $error = 'illegal characters were removed'; }
			return $new; }
		return false; }

	// string reverse for non-ascii strings.
	function mbstrrev($string) {
		$split = preg_split("//u",$string);
		return implode('',array_reverse($split)); }

	// http://www.php.net/manual/en/ref.mbstring.php#86120
	function mb_str_replace($needle, $replacement, $haystack) {
		$needle_len = mb_strlen($needle);
		$replacement_len = mb_strlen($replacement);
		$pos = mb_strpos($haystack, $needle);
		while ($pos !== false) {
			$haystack = mb_substr($haystack, 0, $pos) . $replacement . mb_substr($haystack, $pos + $needle_len);
			$pos = mb_strpos($haystack, $needle, $pos + $replacement_len); }
		return $haystack; }

	// replaces all instances of katakana with hiragana - only returns first two characters for filtering purposes
	function as_hira($string) {
		global $hira, $kata, $kananr;
		// since the above mb_str_replace does not take arrays into accounts,
		// we must run this for every replacement separately.
		for($i=0;$i<count($kata);$i++) { $string = mb_str_replace($kata[$i],$hira[$i],$string); }
		return substr($string, 0, 2); }

	function as_hira_string($string) {
		global $hira, $kata, $kananr;
		// since the above mb_str_replace does not take arrays into accounts,
		// we must run this for every replacement separately.
		for($i=0;$i<count($kata);$i++) { $string = mb_str_replace($kata[$i],$hira[$i],$string); }
		return $string; }

	// replaces all instances of katakana with hiragana
	function as_index_number($string) {
		global $hira, $kata, $kananr;
		for($i=0;$i<count($kata);$i++) { $string = mb_str_replace($kata[$i],$kananr[$i],$string); }
		for($i=0;$i<count($hira);$i++) { $string = mb_str_replace($hira[$i],$kananr[$i],$string); }
		return $string; }

	// replace prime quotes with real quotes (prime quotes are used in the db to ensure mysql doesn't fall on its face)
	function requote($string) {
		$string = mb_str_replace("ʹ","'",$string);
		$string = mb_str_replace("ʺ",'"',$string);
		return $string; }

	// global comparator function for comparing entries
	function compare_kana($a, $b) { return $a->compareOnKanaform($b); }
	function compare_nonkana($a, $b) { return $a->compareOnNonKanaform($b); }

  // simple content hashing simply to prevent .settings tampering
  function hash_pref_string($string) { return crc32($string. "a 32 bit CRC hash is nice and short."); }

  // remove an element from an associative array by value
  function array_remove_value () { $args = func_get_args(); return array_diff($args[0],array_slice($args,1)); }

  // multibyte string reverse
  function mb_strrev($text) { return join('', array_reverse( preg_split('~~u', $text, -1, PREG_SPLIT_NO_EMPTY))); }
?>