# k-means

[Absol package](https://github.com/CaptSiro/absol) for finding prominent colors in images

## Installation

```shell
absol pickup https://github.com/CaptSiro/prominent-color.git
```

## Import

```php
// in a main file
require_once __DIR__ . "/absol/import.php";
import("prominent-color");

// ...
```

## Usage

Examples of usage can be found in the `example` directory. 
The general pipeline of a library can be summarized in such way:

1. Get a path to image
2. Define how many pixels should be used for given image
3. Create Image object
4. Define a number of colors that should be extracted from image
5. (Optional) Define which color space to use

Pipeline shown on simplified example using build-in library objects

```php
// 1.
$src = "image.png";

// 2. all images will be scaled to 100 pixels in width
$pixels = new \ProminentColor\PixelCount\WidthPixelCount(100);

// 3.
$image = Image::create($image_source, $pixel_count);

// 4. The number of colors may differ.
// If a monochromatic image is provided, the number of colors would be 2, but this is automatic
$colors = 8;

// 5. (optional)
$color_space = "rgb";

$extracted = \ProminentColor\ProminentColor::extract($image, $colors, $color_space);
```

Export function returns array of `Centroid` objects.

```php
foreach ($extracted as $centroid) {
    // returns array of floats
    $color = $centroid->point;
    
    // convert to an array of integers
    $rgb = array_map(fn($channel) => (int)round($channel), $color);
    
    $number_of_connections = count($centroid->connections);
    
    // process individual pixels connected to centroid
    foreach ($centroid->connections as $pixel) {
        $x = $pixel->x();
        $y = $pixel->y();
        
        // To get underlying data of used color space
        // In this example, default "rgb" was used.
        // So, data will be an array of integers
        $color_space_data = $pixel->data();
    }
}
```