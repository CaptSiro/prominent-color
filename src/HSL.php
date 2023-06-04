<?php
  
  class HSL {
    public float $hue;
    public float $saturation;
    public float $lightness;
    
    public function __construct(float $hue, float $saturation, float $lightness) {
      $this->hue = $hue;
      $this->saturation = $saturation;
      $this->lightness = $lightness;
    }
  }