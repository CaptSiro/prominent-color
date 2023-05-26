<?php

  class Retriever {
    static int $totalTestedPixels = 100_000;
    static int $colorChannelWidth = 16;
    
    const FOREGROUND = 0;
    const BACKGROUND = 1;
    const MEAN_LIGHTNESS = 2;
  
    /**
     * @param string $imagePath
     * @return array | false Returns [FOREGROUND => RGB, BACKGROUND => RGB, MEAN_LIGHTNESS => float<0; 1>] and false on failure
     */
    static function parse(string $imagePath) {
      if (!file_exists($imagePath)) {
        return false;
      }
      
      ini_set("memory_limit","512M");
      
      $resource = self::imageResource($imagePath);
      
      $width = imagesx($resource);
      $height = imagesy($resource);
      
      $scale = 1;
      
      if ($width * $height > self::$totalTestedPixels) {
        $scale = sqrt($width * $height / self::$totalTestedPixels);
      }
  
      $base = ceil(255 / self::$colorChannelWidth);
      $pixels = new SplFixedArray($base ** 3);
      $mean = new SplFixedArray(101);
      
      $pixelCount = 0;
      
      $foreground = null;
      $foregroundPoints = PHP_INT_MIN;
      $background = null;
      $backgroundPoints = PHP_INT_MIN;
  
      $guidsX = [$width / 3, $width / 2, $width * 2/3];
      $guidsY = [$height / 3, $height / 2, $height * 2/3];
      
      for ($x = 0; $x < $width; $x += $scale) {
        $rx = round($x);
        for ($y = 0; $y < $height; $y += $scale) {
          $pixelCount++;
          $ry = round($y);
          
          $diffX = min(abs($guidsX[0] - $x), abs($guidsX[0] - $x), abs($guidsX[0]) - $x);
          $diffY = min(abs($guidsY[0] - $y), abs($guidsY[0] - $y), abs($guidsY[0]) - $y);
          $locationPoints = (($guidsX[0] - $diffX) / $guidsX[0]) * (($guidsY[0] - $diffY) / $diffY);
          
          $rgb = RGB::fromInt(imagecolorat($resource, $rx, $ry));
          $hash = $rgb->hash(self::$colorChannelWidth, $base);
          
          $points = $pixels->offsetGet($hash) ?? [self::FOREGROUND => 0, self::BACKGROUND => 0];
          $hsl = $rgb->toHSL();
          
          $lightnessBezier = self::bezierCurve(($hsl->lightness > 0.5 ? 1 - $hsl->lightness : $hsl->lightness) * 2);
          $saturationBezier = self::bezierCurve($hsl->saturation);
          
          $points[self::FOREGROUND] += ($lightnessBezier * $saturationBezier * $locationPoints);
//          $points[self::FOREGROUND] += ($lightnessBezier * $saturationBezier);
          $points[self::BACKGROUND] += (1 - $lightnessBezier) * (1 - $saturationBezier) * (1 - $locationPoints);
//          $points[self::BACKGROUND] += (1 - $lightnessBezier) * (1 - $saturationBezier);
          
          $pixels->offsetSet($hash, $points);
          
          $bucket = round($hsl->lightness * 100);
          $mean->offsetSet($bucket, $mean->offsetGet($bucket) + 1);
  
          if ($foregroundPoints < $points[0]) {
            $foregroundPoints = $points[0];
            $foreground = $hash;
          }
  
          if ($backgroundPoints < $points[1]) {
            $backgroundPoints = $points[1];
            $background = $hash;
          }
        }
      }
      
      $half = $pixelCount / 2;
      $lightness = 100;
      for ($i = 0; $i < 101; $i++) {
        $pixelCount -= $mean[$i];
        
        if ($pixelCount === $half) {
          $lightness = $i + 0.5;
          break;
        }
        
        if ($pixelCount < $half) {
          $lightness = $i;
          break;
        }
      }
      
      return [
        self::FOREGROUND => $foreground === null ? new RGB(255, 255, 255) : RGB::unhash($foreground, self::$colorChannelWidth, $base),
        self::BACKGROUND => $background === null ? new RGB(0, 0, 0) : RGB::unhash($background, self::$colorChannelWidth, $base),
        self::MEAN_LIGHTNESS => $lightness / 100
      ];
    }
    
    static function imageResource(string $path) {
      $type = mime_content_type($path);
  
      switch ($type) {
        case "image/png": return imagecreatefrompng($path);
        case "image/jpg": return imagecreatefromjpeg($path);
        case "image/gif": return imagecreatefromgif($path);
        default: return imagecreatefromstring(file_get_contents($path));
      }
    }
  
    static function bezierCurve($parameter, $a = 3.0, $b = 2.0): float {
      return $parameter * $parameter * ($a - ($b * $parameter));
    }
  }
  
  
  
  class RGB {
    public int $red;
    public int $green;
    public int $blue;
    
    public function __construct(int $red, int $green, int $blue) {
      $this->red = $red;
      $this->green = $green;
      $this->blue = $blue;
    }
  
    static function fromInt(int $color): RGB {
      return new self(
        ($color >> 16) & 0xFF,
        ($color >> 8) & 0xFF,
        $color & 0xFF
      );
    }
    
    function hash(int $boundary, int $base): int {
      return floor($this->red / $boundary)
        + floor($this->green / $boundary) * $base
        + floor($this->blue / $boundary) * $base * $base;
    }
    
    static function unhash(int $hash, int $boundary, int $base): RGB {
      $pixel = new SplFixedArray(3);
  
      for ($i = 0; $i < 3; $i++) {
        $pixel[$i] = (($hash % $base) * $boundary) + ($boundary / 2);
        $hash /= $base;
      }
      
      return new RGB($pixel[0], $pixel[1], $pixel[2]);
    }
    
    function toHSL(): HSL {
      $red = (float)($this->red / 255);
      $green = (float)($this->green / 255);
      $blue = (float)($this->blue / 255);
  
      $max = max($red, $green, $blue);
      $min = min($red, $green, $blue);
  
      $delta = $max - $min;
      $lightness = ($min + $max) / 2;
  
      if ($max === $min) {
        return new HSL(0, 0, $lightness);
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
  
      return new HSL(
        360 * (($hue ?? 0) / 6), 
        $saturation, 
        $lightness
      );
    }
    
    
  
    function display(): string {
      $hsl = $this->toHSL();
      
      $text = round($hsl->hue) . ", " . round($hsl->saturation * 100) . "%, " . round($hsl->lightness * 100) . "%";
      return "<div style=\"
          background: hsl($text);
          display: flex;
          justify-content: center;
          align-items: center;
        \"
      >
        <span style=\"color: " . ($hsl->lightness > 0.5 ? "black" : "white") . ";\">$text</span>
      </div>";
    }
  }
  
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