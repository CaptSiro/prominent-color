<?php
  
  namespace SortedQueue;
  
  use Iterator;

  require_once __DIR__ . "/SortedQueueNode.php";
  
  class SortedQueue implements Iterator {
    private ?SortedQueueNode $head = null;
  
    /**
     * @return SortedQueueNode|null
     */
    public function peak(): ?SortedQueueNode {
      return $this->head;
    }
    
    public int $maxCapacity;
    private int $size = 0;
    private float $lowestPoints = 0;
    
    /**
     * @return int
     */
    public function getSize(): int {
      return $this->size;
    }
    
    public function __construct(int $maxCapacity) {
      $this->maxCapacity = $maxCapacity;
    }
    
    public function insert(float $points, $value) {
      if ($this->size === $this->maxCapacity && $this->lowestPoints > $points) {
        return;
      }
      
      if ($this->head === null) {
        $this->head = new SortedQueueNode($points, $value);
        $this->size = 1;
        $this->lowestPoints = $points;
        return;
      }
      
      $last = null;
      $oldCap = $this->size;
      
      foreach ($this as $node) {
        if ($points < $node->points) {
          $last = $node;
          continue;
        }
        
        $this->size++;
        
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
      
      if ($oldCap === $this->size && $this->size + 1 !== $this->maxCapacity) {
        $last->next = new SortedQueueNode($points, $value);
        $this->size++;
        $this->lowestPoints = $points;
      }
      
      if ($this->maxCapacity < $this->size) {
        foreach ($this as $index => $node) {
          if ($index !== $this->size - 2) {
            continue;
          }
          
          $node->next = null;
          $this->lowestPoints = $node->points;
          $this->size--;
          break;
        }
      }
    }
    
    public function debug(): string {
      $buffer = "";
      foreach ($this as $node) {
        $buffer .= "$node->points->";
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