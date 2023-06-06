<?php
  
  use KMean\Point;
  use KMean\PointClosestCentroids;
  
  require_once __DIR__ . "/../KMean/Point.php";
  require_once __DIR__ . "/../KMean/PointClosestCentroids.php";
  
  class HSLPoint implements Point {
    static function random(): self {
      return new self(mt_rand() / mt_getrandmax() * 360, mt_rand() / mt_getrandmax(), mt_rand() / mt_getrandmax(), 0, 0);
    }
    
    static function fromInt(int $color, int $x, int $y): self {
      $red = (($color >> 16) & 0xFF) / 255.0;
      $green = (($color >> 8) & 0xFF) / 255.0;
      $blue = ($color & 0xFF) / 255.0;
    
      $max = max($red, $green, $blue);
      $min = min($red, $green, $blue);
    
      $delta = $max - $min;
      $lightness = ($min + $max) / 2;
    
      if ($max === $min) {
        return new self(0, 0, $lightness, $x, $y);
      }
    
      $saturation = $lightness > 0.5
        ? $delta / (2 - $max - $min)
        : $delta / ($max + $min);
    
      switch ($max) {
        case $red:
          $hue = ($green - $blue) / $delta + ($green < $blue ? 6 : 0);
          break;
        case $green:
          $hue = ($blue - $red) / $delta + 2;
          break;
        case $blue:
          $hue = ($red - $green) / $delta + 4;
          break;
      };
    
      return new self(
        360 * (($hue ?? 0) / 6),
        $saturation,
        $lightness,
        $x,
        $y
      );
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
      $h = round($this->h * 100) / 100;
      $s = round($this->s * 100) / 100;
      $l = round($this->l * 100) / 100;
      
      return "hsl($h, ". $s * 100 ."%, ". $l * 100 ."%)";
    }
  
    /**
     * https://www.30secondsofcode.org/js/s/hsl-to-rgb/
     * @return float[]
     */
    function toRGB(): array {
      $a = $this->s * min($this->l, 1 - $this->l);
      return [$this->f($a, 0), $this->f($a, 8), $this->f($a, 4)];
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
  }