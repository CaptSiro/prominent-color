<?php

  namespace KMean;

  require_once __DIR__ . "/Point.php";

  interface Centroid extends Point {
    function connectedPoints(): array;
  
    /**
     * @param Point[] $points
     * @return self
     */
    static function new(array $points): self;
  }