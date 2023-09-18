<?php
  
  namespace SortedQueueV2;
  
  class SortedQueueNode {
    public float $points;
    public $value;
    public ?SortedQueueNode $next = null;
    
    public function __construct(float $points, $value) {
      $this->points = $points;
      $this->value = $value;
    }
  }