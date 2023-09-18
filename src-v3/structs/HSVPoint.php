<?php
  
  use KMean\Point;
  use KMean\PointClosestPoints;
  
  require_once __DIR__ . "/../KMean/Point.php";
  require_once __DIR__ . "/../KMean/PointClosestPoints.php";
  
  class HSVPoint implements Point {
    static function random(): self {
      return new self(mt_rand() / mt_getrandmax(), mt_rand() / mt_getrandmax(), mt_rand() / mt_getrandmax(), 0, 0);
    }
    
    static function fromInt(int $color, int $x, int $y): self {
      $red = (($color >> 16) & 0xFF) / 255.0;
      $green = (($color >> 8) & 0xFF) / 255.0;
      $blue = ($color & 0xFF) / 255.0;
      
      $v = max($red, $green, $blue);
      $diff = $v - min($red, $green, $blue);
      
      $h = $s = 0;
      
      if ($diff != 0) {
        $diffC = function ($c) use ($v, $diff) {
          return ($v - $c) / 6.0 / $diff + (1 / 2);
        };
        
        $s = $diff / $v;
        $r = $diffC($red);
        $g = $diffC($green);
        $b = $diffC($blue);
        
        switch ($v) {
          case $red:
            $h = $b - $g;
            break;
          case $green:
            $h = (1 / 3) + $r - $b;
            break;
          case $blue:
            $h = (2 / 3) + $g - $r;
            break;
        }
        
        if ($h < 0) {
          $h += 1;
        } else if ($h > 1) {
          $h -= 1;
        }
      }
      
      if ($h > 1) {
        var_dump($color);
      }
      
      return new self($h, $s, $v, $x, $y);
    }
  
  
    
    /**
     * @var float $h Uses range 0-1. To get it in range 0-360 use getH() method
     */
    public float $h;
    /**
     * Returns in 0-360 range
     * @return float
     */
    public function getH(): float {
      return $this->h;
    }
    
    public float $s, $v;
    public int $x, $y;
  
    /**
     * @param float $h Use normalized scale 0-1
     * @param float $s
     * @param float $v
     * @param int $x
     * @param int $y
     */
    public function __construct(float $h, float $s, float $v, int $x, int $y) {
      $this->h = $h;
      $this->s = $s;
      $this->v = $v;
      $this->x = $x;
      $this->y = $y;
    }
    
    public function __toString(): string {
      $rgb = $this->toRGB();
      return "rgb($rgb[0], $rgb[1], $rgb[2])";
    }
    
    
    
    /**
     * @param HSVPoint $point
     * @return float
     */
    function distanceTo($point): float {
      return sqrt(($point->h - $this->h) ** 2 + ($point->s - $this->s) ** 2 + ($point->v - $this->v) ** 2);
    }
  
    use PointClosestPoints;
    
    
  
    /**
     * @return float[]
     */
    function toRGB(): array {
      return [floor($this->f(5) * 255), floor($this->f(3) * 255), floor($this->f(1) * 255)];
    }
    
    private function f($n): float {
      $k = ($n + $this->h * 6) % 6;
      
      return $this->v - ($this->v * $this->s * max(
        min($k, 4 - $k, 1),
        0
      ));
    }
  }