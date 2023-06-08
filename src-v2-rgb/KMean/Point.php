<?php
  
  namespace KMean;
  
  interface Point {
    function distanceTo($point): float;
  
    /**
     * @param self[] $points
     * @return int
     */
    function closest(array $points): int;
  }