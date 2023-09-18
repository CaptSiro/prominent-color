<?php
  
  namespace HSL;
  
  use Exception;
  use Extractor\Image;
  use Generator;
  use HSLPoint;
  use PixelCount;

  require_once __DIR__ . "/../extractor/interfaces/Image.php";
  require_once __DIR__ . "/../createGdImage.php";

  class HSLImage implements Image {
    /**
     * @param string $path
     * @param PixelCount $pixelCount
     * @return self | false
     */
    static function createFrom(string $path, PixelCount $pixelCount): self {
      if (!file_exists($path)) {
        return false;
      }
    
      $resource = createGdImage($path);
    
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
    
    public function __construct($gdImage, int $width, int $height, float $scale) {
      $this->gdImage = $gdImage;
      $this->scale = $scale;
      $this->height = $height;
      $this->width = $width;
    }
    
  
  
    function getWidth(): int {
      return $this->width;
    }
  
    function getHeight(): int {
      return $this->height;
    }
  
    function getScale(): float {
      return $this->scale;
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
  
    /** @inheritDoc */
    function evenDistribution(int $count): array {
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
    
    function randomPixels(int $count): array {
      $xs = $this->randomSet($this->width, $count);
      $ys = $this->randomSet($this->height, $count);
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
    
    function randomSet(int $max, int $count): array {
      $set = [];
      $size = 0;
  
      while ($size !== $count) {
        try {
          $ri = @random_int(0, $max);
        } catch (Exception $never) {}
    
        if (in_array($ri, $set)) {
          continue;
        }
    
        $set[] = $ri;
        $size++;
      }
  
      return $set;
    }
  
    private bool $isReleased = false;
    function release(): void {
      if ($this->isReleased === true) {
        return;
      }
      
      $this->isReleased = true;
      imagedestroy($this->gdImage);
    }
  }