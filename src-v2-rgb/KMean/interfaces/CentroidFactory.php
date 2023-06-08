<?php
  
  namespace KMean;
  
  use KMean\Centroid;
  
  interface CentroidFactory {
    function create(array $points): Centroid;
  }