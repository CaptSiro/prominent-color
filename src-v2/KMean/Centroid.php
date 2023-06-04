<?php

  namespace KMean;

  require_once __DIR__ . "/Point.php";

  interface Centroid {
    /**
     * @param Point[] $points
     * @return self
     */
    static function new(array $points): self;
    
    
    
    function connectedPoints(): array;
    
    function intoPoint(): Point;
  }