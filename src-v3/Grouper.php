<?php
  
  use KMean\Centroid;
  
  require_once __DIR__ . "/structs/HSVPoint.php";
  require_once __DIR__ . "/structs/Image.php";
  require_once __DIR__ . "/structs/HSVCentroid.php";
  
  require_once __DIR__ . "/KMean/core.php";
  
  const TOTAL_PIXELS_ALL = -1;
  
  class Grouper {
    /**
     * @param string $path
     * @param int $groupsCount
     * @param PixelCount $pixelCount
     * @return HSVCentroid[]
     */
    static function groupImagePixels(string $path, int $groupsCount, PixelCount $pixelCount): array {
      $image = Image::createFrom($path, $pixelCount);
    
      /** @var HSVPoint[] $points */
      $points = iterator_to_array($image->pixels());
      $centroids = [];
    
      for ($i = 0; $i < $groupsCount; $i++) {
        $centroids[] = HSVCentroid::default(HSVPoint::random());
      }
      
      $groups = findGroups($points, $centroids, HSVCentroid::class);
      
      usort($groups, function (HSVCentroid $a, HSVCentroid $b) {
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