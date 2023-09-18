<?php
  
  require_once __DIR__ . "/RGB.php";
  
  class Image {
    public $gdImage;
    public int $width;
    public int $height;
    public float $scale;
    public int $colorChannelWidth;
    public array $guidsX;
    public float $guidSizeX;
    public array $guidsY;
    public float $guidSizeY;
    
    function __construct($gdImage, int $width, int $height, float $scale, int $colorChannelWidth) {
      $this->gdImage = $gdImage;
      $this->width = $width;
      $this->height = $height;
      $this->scale = $scale;
      $this->colorChannelWidth = $colorChannelWidth;
    }
    
    function pixel(int $x, int $y): RGB {
      return RGB::fromInt(imagecolorat($this->gdImage, $x, $y));
    }
    
    private bool $isDestroyed = false;
    function free(): void {
      if ($this->isDestroyed) {
        return;
      }
      
      $this->isDestroyed = true;
      imagedestroy($this->gdImage);
    }
  
    /**
     * @param string $path
     * @param int $totalTestedPixels
     * @param int $colorChannelWidth
     * @return self | false
     */
    static function createFrom(string $path, int $totalTestedPixels, int $colorChannelWidth): self {
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
      
      return new self($resource, $width, $height, $scale, $colorChannelWidth);
    }
  
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
  }