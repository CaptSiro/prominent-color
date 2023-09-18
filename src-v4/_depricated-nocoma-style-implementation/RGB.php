<?php
  
  require_once __DIR__ . "/HSL.php";
  
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
    
    const MIN_CONTRAST = 3;
    private const RED = 0.2126;
    private const GREEN = 0.7152;
    private const BLUE = 0.0722;
    /**
     * *Source:* https://stackoverflow.com/questions/9733288/how-to-programmatically-calculate-the-contrast-ratio-between-two-colors
     * @param RGB $color1
     * @param RGB $color2
     * @return float
     */
    static function contrast(RGB $color1, RGB $color2): float {
      $luminance1 = self::luminanceChannel($color1->red) * self::RED
        + self::luminanceChannel($color1->green) * self::GREEN
        + self::luminanceChannel($color1->blue) * self::BLUE;
      
      $luminance2 = self::luminanceChannel($color2->red) * self::RED
        + self::luminanceChannel($color2->green) * self::GREEN
        + self::luminanceChannel($color2->blue) * self::BLUE;
      
      $darker = min($luminance1, $luminance2);
      $lighter = max($luminance1, $luminance2);
      
      return ($lighter + 0.05) / ($darker + 0.05);
    }
    
    static function hasGoodContrast(?RGB $color1, ?RGB $color2): bool {
      if (!isset($color1) || !isset($color2)) {
        return true;
      }
      
      return self::contrast($color1, $color2) >= self::MIN_CONTRAST;
    }
    
    private const GAMMA = 2.4;
    private static function luminanceChannel(int $rgbChannel): float {
      $rgbChannel /= 255;
      return $rgbChannel <= 0.03928
        ? $rgbChannel / 12.92
        : pow(($rgbChannel + 0.055) / 1.055, self::GAMMA);
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
    
    public function __toString(): string {
      return "{{$this->red}, $this->green, $this->blue}";
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