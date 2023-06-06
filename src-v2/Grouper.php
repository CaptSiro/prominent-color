<?php
  
  use KMean\Centroid;
  
  require_once __DIR__ . "/structs/HSLPoint.php";
  require_once __DIR__ . "/structs/Image.php";
  require_once __DIR__ . "/structs/HSLCentroid.php";
  
  require_once __DIR__ . "/KMean/core.php";
  
  const TOTAL_PIXELS_ALL = -1;
  
  class Grouper {
    /**
     * @param string $path
     * @param int $groupsCount
     * @param int $totalPixels
     * @return HSLCentroid[]
     */
    static function groupImagePixels(string $path, int $groupsCount, PixelCount $pixelCount): array {
      $image = Image::createFrom($path, $pixelCount);
    
      /** @var HSLPoint[] $points */
      $points = iterator_to_array($image->pixels());
      $centroids = [];
    
      for ($i = 0; $i < $groupsCount; $i++) {
        $centroids[] = HSLCentroid::default();
      }
    
    
      $groups = findGroups($points, $centroids, HSLCentroid::class);
    
    
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