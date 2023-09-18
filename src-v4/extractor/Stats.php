<?php
  
  namespace Extractor;
  
  class Stats {
    public array $groups;
    public float $imageLightness;
  
    public function __construct(array $groups, float $imageLightness) {
      $this->imageLightness = $imageLightness;
      $this->groups = $groups;
    }
  }