<?php
  
  use KMean\Centroid;
  use KMean\Point;
  
  require_once __DIR__ . "/../KMean/Centroid.php";
  require_once __DIR__ . "/HSVPoint.php";
  
  class HSVCentroid implements Centroid {
    /**
     * @param HSVPoint[] $points
     * @return Centroid
     */
    static function new(array $points): Centroid {
      $sumH = $sumS = $sumV = 0;
      $count = count($points);
  
      for ($i = 0; $i < $count; $i++) {
        $sumH += $points[$i]->h;
        $sumS += $points[$i]->s;
        $sumV += $points[$i]->v;
      }
      
      $new = new self(
        new HSVPoint($sumH / $count, $sumS / $count, $sumV / $count, 0, 0),
        $points
      );
      $new->weight = count($points);
    
      return $new;
    }
  
    static function default(HSVPoint $point): self {
      $default = new self($point, []);
      $default->weight = 0;
      return $default;
    }
    
    
    
    private HSVPoint $pixel;
  
    /**
     * @return HSVPoint
     */
    public function getPixel(): HSVPoint {
      return $this->pixel;
    }
    
    private array $pixels;
    public int $weight;
    
    public function __construct(HSVPoint $pixel, array $pixels) {
      $this->pixel = $pixel;
      $this->pixels = $pixels;
    }
  
    function connectedPoints(): array {
      return $this->pixels;
    }
  
    function intoPoint(): Point {
      return $this->getPixel();
    }
  }