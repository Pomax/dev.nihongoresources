<?php

  /**
   * Quicksort of an array of the form:
   *
   *  Array (
   *    "id" => <number>
   *    "keb" => array([0]=>...[1]=>...)
   *    "reb" => array([0]=>...[1]=>...)
   *    "eng" => array([0]=>...[1]=>...)
   *    "pos" => array([0]=>...[1]=>...)
   *  )
   */

  function quicksort($input, $index)
  {
    $count = count($input);

    // Termination clause: a 1 or 0 term
    // array is already sorted.
    if($count < 2) { return $input; }

    // Pick a pivot. Since we have nothing to go on,
    // we pick the middle element.
    $pos = intval($count/2)-1;
    $pivot_value = "";
    do {
      $pos ++;
      $pivot = $input[$pos];
      $elements = $pivot[$index];
      if(isset($elements[0])) {
        $pivot_value = $elements[0]; }
    } while($pos<$count-1 && $pivot_value=="");

    // "we can't sort" panic option
    if($pos==$count) { return $input; }

    $left = array();
    $right = array();

    // Categorise all elements left of the pivot
    for($i=0; $i<$pos; $i++) {
      $element = $input[$i];
      if(isset($element[$index][0])) {
        $element_value = $element[$index][0];
        ($element_value < $pivot_value) ? $left[] = $element : $right[] = $element; }
      else { $left[] = $element; }}

    // Categorise all elements right of the pivot
    for($i=$pos+1; $i<$count; $i++) {
      $element = $input[$i];
      if(isset($element[$index][0])) {
        $element_value = $element[$index][0];
        ($element_value < $pivot_value) ? $left[] = $element : $right[] = $element; }
      else { $left[] = $element; }}

    // merge the quicksorted sublists.
    return array_merge(quicksort($left, $index), array($pivot), quicksort($right, $index));
  }
?>