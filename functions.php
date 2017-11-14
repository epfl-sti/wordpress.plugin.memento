<?php
function trim_br_inifinite($string){
  while (preg_match('/^\s*(?:<br\s*\/?>\s*)*/i', $string) !== false) {
    $string = preg_replace('/^\s*(?:<br\s*\/?>\s*)*/i', '', $string);
  }
  while (preg_match('/\s*(?:<br\s*\/?>\s*)*$/i', $string) !== false) {
    $string = preg_replace('/\s*(?:<br\s*\/?>\s*)*$/i', '', $string);
  }
  return	$string;
}

function trim_br($string){
  $string = preg_replace('/^\s*(?:<br\s*\/?>\s*)*/im', '', $string);
  return	$string;
}
 ?>
