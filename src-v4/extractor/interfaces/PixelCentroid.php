<?php
  
  namespace Extractor;
  
  require_once __DIR__ . "/Pixel.php";
  
  interface PixelCentroid {
    function getPixel(): Pixel;
    
    function getWeight();
  }