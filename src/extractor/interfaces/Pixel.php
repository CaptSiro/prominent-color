<?php
  
  namespace Extractor;
  
  use KMean\Centroid;

  require_once __DIR__ . "/PixelCentroid.php";
  
  interface Pixel {
    function __toString(): string;
    
    function toCentroid(): Centroid;
    
    function toRGB(): RGB;
  }