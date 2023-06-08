<?php
  
  namespace KMean;

  trait PointClosestCentroids {
    /**
     * @param Centroid[] $points
     * @return int
     */
    function closest(array $points): int {
      $smallestDistance = PHP_INT_MAX;
      $pointIndex = -1;
    
      for ($i = 0; $i < count($points); $i++) {
        $distance = $this->distanceTo($points[$i]->intoPoint());
      
        if ($distance < $smallestDistance) {
          $smallestDistance = $distance;
          $pointIndex = $i;
        }
      }
    
      return $pointIndex;
    }
  }