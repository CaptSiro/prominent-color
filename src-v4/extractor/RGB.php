<?php
  
  namespace Extractor;
  
  class RGB {
    public int $red;
    public int $green;
    public int $blue;
  
    public function __construct(int $red, int $green, int $blue) {
      $this->blue = $blue;
      $this->green = $green;
      $this->red = $red;
    }
  }