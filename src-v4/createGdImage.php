<?php
  
  /**
   * @param string $path
   * @return false|GdImage|resource
   */
  function createGdImage(string $path) {
    if (!file_exists($path)) {
      return false;
    }
    
    $type = mime_content_type($path);
    
    ini_set("memory_limit","512M");
    
    switch ($type) {
      case "image/png": return imagecreatefrompng($path);
      case "image/jpg": return imagecreatefromjpeg($path);
      case "image/gif": return imagecreatefromgif($path);
      default: return imagecreatefromstring(file_get_contents($path));
    }
  }