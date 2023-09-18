<?php

namespace ProminentColor;



use Exception;
use GdImage;
use ProminentColor\PixelCount\PixelCount;
use ProminentColor\PixelCount\TotalPixelCount;
use ProminentColor\Plugin\ColorPlugin;

require_once __DIR__ . "/create-gd-image.php";



class Image {
    /**
     * @throws Exception
     */
    static function create(string $path, PixelCount|null $pixel_count = null): self {
        $resource = create_gd_image($path);

        if ($resource === false) {
            throw new Exception("Image does not exist. Path: '$path'");
        }

        $width = imagesx($resource);
        $height = imagesy($resource);

        if ($pixel_count === null) {
            $pixel_count = new TotalPixelCount(500);
        }

        $scale = 1;
        $total = $pixel_count->count($width, $height);

        if ($width * $height > $total) {
            $scale = sqrt($width * $height / $total);
        }

        return new self($resource, $width, $height, $scale, $pixel_count);
    }



    public function __construct(
        public GdImage $resource,
        public int $width,
        public int $height,
        public float $scale,
        public PixelCount $pixel_count,
    ) {}



    function pixels(ColorPlugin $color_plugin): array {
        $pixels = [];

        $ex = 0;
        for ($x = 0; $x < $this->width; $x += $this->scale) {
            $rx = floor($x);
            $ey = 0;

            for ($y = 0; $y < $this->height; $y += $this->scale) {
                $ry = floor($y);
                $pixels[] = $color_plugin->from_int(imagecolorat($this->resource, $rx, $ry), $ex, $ey);
                $ey++;
            }

            $ex++;
        }

        return $pixels;
    }



    function even_distribution(int $count, ColorPlugin $color_plugin): array {
        if ($count <= 0) {
            return [];
        }

        $total_area = $this->width * $this->height;
        $point_area = $total_area / $count;
        $length = sqrt($point_area);
        $pixels = [];

        for($i = $length / 2; $i < $this->width; $i += $length) {
            for($j = $length / 2; $j < $this->height; $j += $length) {
                $pixels[] = $color_plugin->from_int(
                    imagecolorat($this->resource, floor($i), floor($j)),
                    floor($i / $this->scale),
                    floor($j / $this->scale)
                );
            }
        }

        $pixel_count = count($pixels);

        if ($pixel_count > $count) {
            $pixels = array_slice($pixels, 0, $count);
        }

        if ($pixel_count < $count) {
            $pixels = array_merge($pixels, $this->random_pixels($count - $pixel_count, $color_plugin));
        }

        return $pixels;
    }



    function random_pixels(int $count, ColorPlugin $color_plugin): array {
        $xs = $this->random_set($this->width, $count);
        $ys = $this->random_set($this->height, $count);
        $pixels = [];

        for ($i = 0; $i < $count; $i++) {
            $pixels[] = $color_plugin->from_int(
                imagecolorat($this->resource, $xs[$i], $ys[$i]),
                floor($xs[$i] / $this->scale),
                floor($ys[$i] / $this->scale)
            );
        }

        return $pixels;
    }



    function random_set(int $max, int $count): array {
        $set = [];
        $size = 0;

        while ($size !== $count) {
            try {
                $ri = @random_int(0, $max);
            } catch (Exception) {}

            if (in_array($ri, $set)) {
                continue;
            }

            $set[] = $ri;
            $size++;
        }

        return $set;
    }



    private bool $is_released = false;

    function release(): void {
        if ($this->is_released === true) {
            return;
        }

        $this->is_released = true;
        imagedestroy($this->resource);
    }
}