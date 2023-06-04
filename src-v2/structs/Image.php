<?php
  
  class Image {
    static function imageResource(string $path) {
      $type = mime_content_type($path);
    
      ini_set("memory_limit","512M");
    
      switch ($type) {
        case "image/png": return imagecreatefrompng($path);
        case "image/jpg": return imagecreatefromjpeg($path);
        case "image/gif": return imagecreatefromgif($path);
        default: return imagecreatefromstring(file_get_contents($path));
      }
    }
  
    /**
     * @param string $path
     * @param int $totalTestedPixels
     * @return self | false
     */
    static function createFrom(string $path, int $totalTestedPixels): self {
      if (!file_exists($path)) {
        return false;
      }
    
      $resource = self::imageResource($path);
    
      $width = imagesx($resource);
      $height = imagesy($resource);
    
      $scale = 1;
    
      if ($width * $height > $totalTestedPixels) {
        $scale = sqrt($width * $height / $totalTestedPixels);
      }
    
      return new self($resource, $width, $height, $scale);
    }
    
    public $gdImage;
    public int $width;
    public int $height;
    public float $scale;
    
    public function __construct($gdImage, $width, $height, $scale) {
      $this->gdImage = $gdImage;
      $this->width = $width;
      $this->height = $height;
      $this->scale = $scale;
    }
    
    function pixels(): Generator {
      $ex = 0;
      for ($x = 0; $x < $this->width; $x += $this->scale) {
        $rx = floor($x);
        $ey = 0;
        
        for ($y = 0; $y < $this->height; $y += $this->scale) {
          $ry = floor($y);
          yield HSLPoint::fromInt(imagecolorat($this->gdImage, $rx, $ry), $ex, $ey);
          $ey++;
        }
        
        $ex++;
      }
    }
  
    private bool $isDestroyed = false;
    function free(): void {
      if ($this->isDestroyed) {
        return;
      }
    
      $this->isDestroyed = true;
      imagedestroy($this->gdImage);
    }
  }