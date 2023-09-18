<?php
  
  use SortedQueue\SortedQueue;
  
  require_once __DIR__ . "/Image.php";
  require_once __DIR__ . "/SortedQueue/SortedQueue.php";
  require_once __DIR__ . "/RGB.php";

  class Retriever {
    static int $totalTestedPixels = 100_000;
    static int $colorChannelWidth = 4;
    static int $recursionStackSize = 3;
    static int $colorQueueSize = 10;
    
    const FOREGROUND = 0;
    const BACKGROUND = 1;
    const MEAN_LIGHTNESS = 2;
    const CONTRAST = 3;
  
    /**
     * @param string $imagePath
     * @return array | false Returns [FOREGROUND => RGB, BACKGROUND => RGB, MEAN_LIGHTNESS => float<0; 1>] and false on failure
     */
    static function parse(string $imagePath) {
      if (!file_exists($imagePath)) {
        return false;
      }
  
      return self::parseImage(Image::createFrom(
        $imagePath,
        self::$totalTestedPixels,
        self::$colorChannelWidth,
      ));
    }
  
    /**
     * @param Image $image
     * @param int $index
     * @return array | false
     */
    private static function parseImage(Image $image, int $index = 0) {
      if (self::$recursionStackSize === $index) {
        return false;
      }
      
      $base = ceil(256 / $image->colorChannelWidth);
      $pixels = new SplFixedArray($base ** 3);
      
      $mean = new SplFixedArray(101);
  
      $pixelCount = 0;
  
      $foregroundQueue = new SortedQueue(self::$colorQueueSize);
      $backgroundQueue = new SortedQueue(self::$colorQueueSize);
  
      for ($x = 0; $x < $image->width; $x += $image->scale) {
        $rx = floor($x);
        for ($y = 0; $y < $image->height; $y += $image->scale) {
          $pixelCount++;
          $ry = floor($y);
      
          $ax = abs($x - $image->width / 2);
          $ay = abs($x - $image->height / 2);
          
          $locationPoints = self::map($ax, $image->width / 5, $image->width / 2, 1, 0)
            * self::map($ay, $image->height / 5, $image->height / 2, 1, 0);
      
          $rgb = $image->pixel($rx, $ry);
          $hash = $rgb->hash($image->colorChannelWidth, $base);
      
          $points = $pixels->offsetGet($hash) ?? [self::FOREGROUND => 0, self::BACKGROUND => 0];
          $hsl = $rgb->toHSL();
      
          $saturationBezier = self::bezierCurve($hsl->saturation);
          $lightnessBezier = self::bezierCurve(($hsl->lightness > 0.5 ? 1 - $hsl->lightness : $hsl->lightness) * 2);
      
          $points[self::FOREGROUND] += $lightnessBezier * $saturationBezier * $locationPoints;
//          $points[self::FOREGROUND] += ($lightnessBezier * $saturationBezier);
          
          if ($locationPoints > 1) {
            $locationPoints = 1;
          }
          
          $points[self::BACKGROUND] += (1 - $lightnessBezier) * (1 - $saturationBezier) * (1 - $locationPoints);
//          $points[self::BACKGROUND] += (1 - $lightnessBezier) * (1 - $saturationBezier);
      
          $pixels->offsetSet($hash, $points);
      
          $bucket = round($hsl->lightness * 100);
          $mean->offsetSet($bucket, $mean->offsetGet($bucket) + 1);
      
          $foregroundQueue->insert($points[0], $rgb);
          $backgroundQueue->insert($points[1], $rgb);
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
      
      $contrastQueue = new SortedQueue(self::$colorQueueSize ** 2);
  
      foreach ($foregroundQueue as $foreground) {
        foreach ($backgroundQueue as $background) {
          $contrastQueue->insert(
            RGB::contrast($foreground->value, $background->value),
            [
              self::FOREGROUND => $foreground->value,
              self::BACKGROUND => $background->value
            ]);
        }
      }
      
//      var_dump($contrastQueue->debug());
  
      if ($contrastQueue->getSize() === 0) {
        return [
          self::FOREGROUND => $lightness < 50 ? new RGB(255, 255, 255) : new RGB(0, 0, 0),
          self::BACKGROUND => $lightness < 50 ? new RGB(0, 0, 0) : new RGB(255, 255, 255),
          self::MEAN_LIGHTNESS => $lightness / 100
        ];
      }
  
      if ($contrastQueue->peak()->points < RGB::MIN_CONTRAST) {
        $image->colorChannelWidth *= 2;
        $result = self::parseImage($image, $index + 1);

        $succeededAndHasBetterContrast = $result !== false && $result[self::CONTRAST] > $contrastQueue->peak()->points;
        if ($succeededAndHasBetterContrast) {
          $image->free();
          return $result;
        }
      }
  
      $image->free();
  
      return [
        self::FOREGROUND => $contrastQueue->peak()->value[self::FOREGROUND],
        self::BACKGROUND => $contrastQueue->peak()->value[self::BACKGROUND],
        self::MEAN_LIGHTNESS => $lightness / 100,
        self::CONTRAST => $contrastQueue->peak()->points,
      ];
    }
  
    static function bezierCurve($parameter, $a = 3, $b = 2): float {
      return $parameter * $parameter * ($a - ($b * $parameter));
    }
    
    static function map($value, $fromStart, $fromEnd, $toStart, $toEnd): float {
      return (($value - $fromStart) / ($fromEnd - $fromStart)) * ($toEnd - $toStart) + $toStart;
    }
  }