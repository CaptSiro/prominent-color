<?php
  
  use KMean\Centroid;
  use KMean\Point;
  
  require_once __DIR__ . "/../k-means/interfaces/Centroid.php";
  require_once __DIR__ . "/HSLPoint.php";
  
  class HSLCentroid implements Centroid {
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
    
    
    
    function connectedPoints(): array {
      return $this->pixels;
    }
  
    function toPoint(): Point {
      return $this->getPixel();
    }
  }