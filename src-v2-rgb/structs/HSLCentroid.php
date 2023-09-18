<?php
  
  use KMean\Centroid;
  use KMean\CentroidFactory;
  use KMean\Point;
  
  require_once __DIR__ . "/../KMean/interfaces/Centroid.php";
  require_once __DIR__ . "/../KMean/interfaces/CentroidFactory.php";
  require_once __DIR__ . "/HSLPoint.php";
  
  class HSLCentroid implements Centroid {
    /**
     * @param HSLPoint[] $points
     * @return Centroid
     */
    static function new(array $points): Centroid {
      $sumH = $sumS = $sumL = 0;
      $count = count($points);
    
      for ($i = 0; $i < $count; $i++) {
        $sumH += $points[$i]->h;
        $sumS += $points[$i]->s;
        $sumL += $points[$i]->l;
      }
    
      $new = new self(
        new HSLPoint($sumH / $count, $sumS / $count, $sumL / $count, 0, 0),
        $points
      );
      $new->weight = $count;
    
      return $new;
    }
  
    static function default(HSLPoint $pixel): self {
      $default = new self($pixel, []);
      $default->weight = 0;
      return $default;
    }
    
    
    
    private HSLPoint $pixel;
  
    /**
     * @return HSLPoint
     */
    public function getPixel(): HSLPoint {
      return $this->pixel;
    }
    
    private array $pixels;
    public int $weight;
    
    public function __construct(HSLPoint $pixel, array $pixels) {
      $this->pixel = $pixel;
      $this->pixels = $pixels;
    }
  
    function distanceTo($point): float {
      return $this->pixel->distanceTo($point);
    }
    
    function connectedPoints(): array {
      return $this->pixels;
    }
  
    function toPoint(): Point {
      return $this->getPixel();
    }
  }