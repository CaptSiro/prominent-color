<?php
  
  use KMean\CentroidFactory;
  use KMean\Point;
  use KMean\Centroid;
  
  require_once __DIR__ . "/interfaces/Point.php";
  require_once __DIR__ . "/interfaces/Centroid.php";
  
  /**
   * @param Centroid[] $centroids
   * @param Point[] $points
   * @param CentroidFactory $factory
   * @return Centroid[]
   */
  function moveCentroids(array $centroids, array $points, CentroidFactory $factory): array {
    $centroidsMap = [];
    
    $count = count($points);
    for ($i = 0; $i < $count; $i++) {
      $centroidIndex = $points[$i]->closest($centroids);
      
      if (!isset($centroidsMap[$centroidIndex])) {
        $centroidsMap[$centroidIndex] = [$points[$i]];
        continue;
      }
      
      $centroidsMap[$centroidIndex][] = $points[$i];
    }
    
    $new = [];
    
    $count = count($centroids);
    for ($i = 0; $i < $count; $i++) {
      if (!isset($centroidsMap[$i])) {
        $new[$i] = $centroids[$i];
        continue;
      }
      
      $new[$i] = $factory->create($centroidsMap[$i]);
    }
    
    return $new;
  }
  
  
  
  /**
   * @param array $points
   * @param Centroid[] $centroids
   * @param CentroidFactory $factory
   * @return array
   */
  function findGroups(array $points, array $centroids, CentroidFactory $factory): array {
    $count = count($centroids);
    
    do {
      $new = moveCentroids($centroids, $points, $factory);
      $distance = 0;
      
      for ($i = 0; $i < $count; $i++) {
        $distance += $new[$i]->distanceTo($centroids[$i]->intoPoint());
      }
      
      $centroids = $new;
    } while($distance / $count > 0.001);
    
    return $centroids;
  }