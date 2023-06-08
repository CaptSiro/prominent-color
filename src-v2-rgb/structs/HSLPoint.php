<?php
  
  use KMean\Point;
  use KMean\PointClosestCentroids;
  
  require_once __DIR__ . "/../KMean/interfaces/Point.php";
  require_once __DIR__ . "/../KMean/traits/PointClosestCentroids.php";
  
  class HSLPoint implements Point, JsonSerializable {
    static function fromInt(int $color, int $x, int $y): self {
      $red = (($color >> 16) & 0xFF);
      $green = (($color >> 8) & 0xFF);
      $blue = ($color & 0xFF);
      
      return new self($red, $green, $blue, $x, $y);
    }

    
    
    public float $h, $s, $l;
    public int $x, $y;
    
    public function __construct(float $h, float $s, float $l, int $x, int $y) {
      $this->h = $h;
      $this->s = $s;
      $this->l = $l;
      $this->x = $x;
      $this->y = $y;
    }
    
    public function __toString(): string {
      $red = floor($this->h);
      $green = floor($this->s);
      $blue = floor($this->l);
      return "rgb($red, $green, $blue)";
    }
  
    /**
     * https://www.30secondsofcode.org/js/s/hsl-to-rgb/
     * @return float[]
     */
    function toRGB(): array {
      return [floor($this->h), floor($this->s), floor($this->l)];
    }
    
    private function k($n): float {
      return ($n + $this->h / 30) % 12;
    }
    
    private function f($a, $n): float {
      return 255 * ($this->l - $a * max(-1, min(
        $this->k($n) - 3,
        9 - $this->k($n),
        1
      )));
    }
  
    
    
    /**
     * @param HSLPoint $point
     * @return float
     */
    function distanceTo($point): float {
      return abs($point->h - $this->h) + abs($point->s - $this->s) + abs($point->l - $this->l);
    }
  
    use PointClosestCentroids;
  
    
    
    public function jsonSerialize(): stdClass {
      $obj = new stdClass();
      $obj->r = $this->h;
      $obj->g = $this->s;
      $obj->b = $this->l;
      
      return $obj;
    }
  }