<?php
  
  interface PixelCount {
    function count(int $width, int $height): int;
  }