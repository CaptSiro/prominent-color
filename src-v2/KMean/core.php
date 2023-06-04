<?php
  
  use KMean\Point;
  use KMean\Centroid;
  
  require_once __DIR__ . "/Point.php";
  require_once __DIR__ . "/Centroid.php";
  
  /**
   * @param Centroid[] $centroids
   * @param Point[] $points
   * @param string $centroidClass
   * @return Centroid[]
   */
  function moveCentroids(array $centroids, array $points, string $centroidClass): array {
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
    try {
      $r = new ReflectionClass($centroidClass);
      $instance = $r->newInstanceWithoutConstructor();
    } catch (ReflectionException $e) {
      var_dump($e);
      return [];
    }
    
    $count = count($centroids);
    for ($i = 0; $i < $count; $i++) {
      if (!isset($centroidsMap[$i])) {
        $new[$i] = $centroids[$i];
        continue;
      }
      
      $new[$i] = $instance::new($centroidsMap[$i]);
    }
    
    return $new;
  }
  
  
  
  /**
   * @param array $points
   * @param Centroid[] $centroids
   * @param string $centroidClass
   * @return array
   */
  function findGroups(array $points, array $centroids, string $centroidClass): array {
    $count = count($centroids);
    
    do {
      $new = moveCentroids($centroids, $points, $centroidClass);
      $distance = 0;
      
      for ($i = 0; $i < $count; $i++) {
        $distance += $new[$i]->distanceTo($centroids[$i]);
      }
      
      $centroids = $new;
    } while($distance / $count > 0.001);
    
    return $centroids;
  }