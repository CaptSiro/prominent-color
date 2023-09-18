<?php
  
  require_once __DIR__ . "/structs/HSVPoint.php";
  
  function rgbToInt($r, $g, $b): int {
    return ($r << 16) + ($g << 8) + $b;
  }
  
  var_dump(HSVPoint::fromInt(rgbToInt(255, 0, 64), 0, 0));