<?php
  require_once __DIR__ . "/Grouper.php";
  require_once __DIR__ . "/structs/WidthPixelCount.php";
  require_once __DIR__ . "/structs/TotalPixelCount.php";
  
  $start = time();
  
  $src = "../test-images/1.jpg";
  $pixelCount = new WidthPixelCount(64);
  $groups = Grouper::groupImagePixels($src, 5, $pixelCount);
  
  $image = Image::createFrom($src, $pixelCount);
  $canvas = imagecreatetruecolor(ceil($image->width / $image->scale), ceil($image->height / $image->scale));
  
  $end = time();
  
  foreach ($groups as $c) {
    $rgb = $c->getPixel()->toRGB();
    $color = imagecolorallocate($canvas, $rgb[0], $rgb[1], $rgb[2]);
    
    foreach ($c->connectedPoints() as $pixel) {
      /** @var HSLPoint $pixel */
      imagesetpixel($canvas, $pixel->x, $pixel->y, $color);
    }
  }
  
  imagepng($canvas, __DIR__ . "/image.png");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
  
  <style>
      * {
          padding: 0;
          margin: 0;
          box-sizing: border-box;
      }

      body {
          width: 100vw;
          height: 100vh;
          overflow: hidden;
          display: grid;
          grid-template-rows: 1fr 80px;
          background-color: black;
      }

      img {
          width: 100vw;
          height: calc(100vh - 80px);
          object-fit: contain;
          image-rendering: pixelated;
      }

      .row {
          width: 100vw;
          height: 80px;
          display: grid;
          grid-template-columns: repeat(<?= count($groups) + 1 ?>, 1fr);
      }
  </style>
</head>
<body>
  <img src="./image.png" alt="pog">
  <div class="row">
    <?php
      foreach ($groups as $c) {
        $hsl = $c->getPixel();
        echo "
          <span
            style='
              background-color: $hsl;
              color: ". ($hsl->l > 0.5 ? "black" : "white") ."
            '
            >$hsl</span>
        ";
      }
    ?>
    <span style="color: white; background-color: black">Time to generate: <?= $end - $start ?> seconds</span>
  </div>
</body>
</html>