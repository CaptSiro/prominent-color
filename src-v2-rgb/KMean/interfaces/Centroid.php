<?php

  namespace KMean;

  require_once __DIR__ . "/Point.php";

  interface Centroid {
    function connectedPoints(): array;
    
    function intoPoint(): Point;
  }