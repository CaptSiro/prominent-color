<?php
  
  require_once __DIR__ . "/HSLPoint.php";
  require_once __DIR__ . "/PixelCount.php";
  
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
     * @param PixelCount $pixelCount
     * @return self | false
     */
    static function createFrom(string $path, PixelCount $pixelCount): self {
      if (!file_exists($path)) {
        return false;
      }
    
      $resource = self::imageResource($path);
    
      $width = imagesx($resource);
      $height = imagesy($resource);
    
      $scale = 1;
    
      $totalPixelCount = $pixelCount->count($width, $height);
      
      if ($width * $height > $totalPixelCount) {
        $scale = sqrt($width * $height / $totalPixelCount);
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
    
    private function randomSet(int $start, int $end, int $count): array {
      $set = [];
      $size = 0;
      
      while ($size !== $count) {
        try {
          $ri = random_int($start, $end);
        } catch (Exception $never) {}
        
        if (in_array($ri, $set)) {
          continue;
        }
        
        $set[] = $ri;
        $size++;
      }
      
      return $set;
    }
    
    function randomPixels(int $count): array {
      $xs = $this->randomSet(0, $this->width, $count);
      $ys = $this->randomSet(0, $this->height, $count);
      $pixels = [];
      
      for ($i = 0; $i < $count; $i++) {
        $pixels[] = HSLPoint::fromInt(
          imagecolorat($this->gdImage, $xs[$i], $ys[$i]),
          floor($xs[$i] / $this->scale),
          floor($ys[$i] / $this->scale)
        );
      }
      
      return $pixels;
    }
    
    function distributePoints(int $count): array {
      if ($count <= 0) {
        return [];
      }
      
      $totalArea = $this->width * $this->height;
      $pointArea = $totalArea / $count;
      $length = sqrt($pointArea);
      $pixels = [];
  
      for($i = $length / 2; $i < $this->width; $i += $length) {
        for($j = $length / 2; $j < $this->height; $j += $length) {
          $pixels[] = HSLPoint::fromInt(
            imagecolorat($this->gdImage, floor($i), floor($j)),
            floor($i / $this->scale),
            floor($j / $this->scale)
          );
        }
      }
      
      $pixelCount = count($pixels);
      
      if ($pixelCount > $count) {
        $pixels = array_slice($pixels, 0, $count);
      }
      
      if ($pixelCount < $count) {
        $pixels = array_merge($pixels, $this->randomPixels($count - $pixelCount));
      }
      
      return $pixels;
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