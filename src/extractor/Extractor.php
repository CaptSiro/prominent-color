<?php
  
  namespace Extractor;
  
  use KMean\Centroid;
  use KMean\CentroidFactory;
  use PixelCount;
  
  require_once __DIR__ . "/interfaces/Image.php";
  require_once __DIR__ . "/interfaces/PixelCentroid.php";
  require_once __DIR__ . "/../k-means/core.php";
  require_once __DIR__ . "/../k-means/interfaces/Centroid.php";

  class Extractor {
    function parseImage(Image $image, int $groupsCount, PixelCount $pixelCount, CentroidFactory $factory): Stats {
      /** @var Pixel[] $points */
      $points = iterator_to_array($image->pixels());
      $centroids = $image->evenDistribution($groupsCount);
  
      for ($i = 0; $i < $groupsCount; $i++) {
        $centroids[$i] = $centroids[$i]->toCentroid();
      }
      
      /** @var Centroid[] $centroids */
  
      $groups = findGroups($points, $centroids, $factory);
      
      $image->release();
  
      usort($groups, function (PixelCentroid $a, PixelCentroid $b) {
        if ($a->getWeight() === $b->getWeight()) {
          return 0;
        }
    
        if ($a->getWeight() > $b->getWeight()) {
          return -1;
        }
    
        return 1;
      });
  
      return new Stats($groups, -1);
    }
  }