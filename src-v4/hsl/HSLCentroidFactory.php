<?php
  
  use KMean\Centroid;
  use KMean\CentroidFactory;
  
  require_once __DIR__ . "/../k-means/interfaces/CentroidFactory.php";
  require_once __DIR__ . "/HSLCentroid.php";
  
  class HSLCentroidFactory implements CentroidFactory {
    function create(array $points): Centroid {
      $sumH = $sumS = $sumL = 0;
      $count = count($points);
  
      for ($i = 0; $i < $count; $i++) {
        $sumH += $points[$i]->h;
        $sumS += $points[$i]->s;
        $sumL += $points[$i]->l;
      }
  
      $new = new HSLCentroid(
        new HSLPoint($sumH / $count, $sumS / $count, $sumL / $count, 0, 0),
        $points
      );
      $new->weight = $count;
  
      return $new;
    }
  }