<?php
  
  use KMean\Centroid;
  use KMean\CentroidFactory;
  
  require_once __DIR__ . "/../KMean/interfaces/CentroidFactory.php";
  require_once __DIR__ . "/HSLCentroid.php";
  
  class HSLCentroidFactory implements CentroidFactory {
    function create(array $points): Centroid {
      return HSLCentroid::new($points);
    }
  }