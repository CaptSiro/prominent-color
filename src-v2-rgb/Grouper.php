<?php
  
  require_once __DIR__ . "/structs/HSLPoint.php";
  require_once __DIR__ . "/structs/Image.php";
  require_once __DIR__ . "/structs/HSLCentroid.php";
  
  require_once __DIR__ . "/KMean/core.php";
  
  const TOTAL_PIXELS_ALL = -1;
  
  class Grouper {
    /**
     * @param string $path
     * @param int $groupsCount
     * @param PixelCount $pixelCount
     * @return HSLCentroid[]
     */
    static function groupImagePixels(string $path, int $groupsCount, PixelCount $pixelCount): array {
      $image = Image::createFrom($path, $pixelCount);
    
      /** @var HSLPoint[] $points */
      $points = iterator_to_array($image->pixels());
      $centroids = [];
      
      foreach ($image->distributePoints($groupsCount) as $pixel) {
        $centroids[] = HSLCentroid::default($pixel);
      }
    
      $groups = findGroups($points, $centroids, new HSLCentroidFactory());
      
      $image->free();
    
      usort($groups, function (HSLCentroid $a, HSLCentroid $b) {
        if ($a->weight === $b->weight) {
          return 0;
        }
      
        if ($a->weight > $b->weight) {
          return -1;
        }
      
        return 1;
      });
    
      return $groups;
    }
  }