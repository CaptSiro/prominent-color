<?php
  
  namespace Extractor;
  
  require_once __DIR__ . "/Pixel.php";
  
  interface PixelFactory {
    function create(int $color): Pixel;
  }