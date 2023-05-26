<?php
  
  require_once __DIR__ . "/SortedQueueNode.php";
  
  class SortedQueue implements Iterator {
    private ?SortedQueueNode $head = null;
    public int $maxCapacity;
    private int $capacity = 0;
    private float $lowestPoints = 0;
  
    /**
     * @return int
     */
    public function getCapacity(): int {
      return $this->capacity;
    }
    
    public function __construct(int $maxCapacity) {
      $this->maxCapacity = $maxCapacity;
    }
    
    public function add(float $points, $value) {
      if ($this->capacity === $this->maxCapacity && $this->lowestPoints > $points) {
        return;
      }
      
      if ($this->head === null) {
        $this->head = new SortedQueueNode($points, $value);
        $this->capacity = 1;
        $this->lowestPoints = $points;
        return;
      }
      
      $last = null;
      $oldCap = $this->capacity;
      
      foreach ($this as $node) {
        if ($points < $node->points) {
          $last = $node;
          continue;
        }
        
        $this->capacity++;
        
        if ($last === null) {
          $this->head = new SortedQueueNode($points, $value);
          $this->head->next = $node;
          break;
        }
        
        $inserted = new SortedQueueNode($points, $value);
        $inserted->next = $node;
        $last->next = $inserted;
        break;
      }
      
      if ($oldCap === $this->capacity && $this->capacity + 1 !== $this->maxCapacity) {
        $last->next = new SortedQueueNode($points, $value);
        $this->capacity++;
        $this->lowestPoints = $points;
      }
      
      if ($this->maxCapacity < $this->capacity) {
        foreach ($this as $index => $node) {
          if ($index !== $this->capacity - 2) {
            continue;
          }
          
          $node->next = null;
          $this->lowestPoints = $node->points;
          $this->capacity--;
          break;
        }
      }
    }
    
    
    public function debug(): string {
      $buffer = "";
      foreach ($this as $node) {
        $buffer .= "[$node->points]:$node->value->";
      }
      
      return $buffer;
    }
    
    
    private ?SortedQueueNode $current = null;
    private int $index = 0;
    public function current() {
      return $this->current;
    }
    
    public function next() {
      $this->current = $this->current->next;
      $this->index++;
    }
    
    public function key() {
      return $this->index;
    }
    
    public function valid(): bool {
      return $this->current !== null;
    }
    
    public function rewind() {
      $this->current = $this->head;
      $this->index = 0;
    }
  }