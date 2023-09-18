import {HSLPoint} from "./HSLPoint";

export const MAX_TOTAL_TESTED_PIXELS = 100_000;

export class Picture {
  static load(src: string, totalPixels: number = MAX_TOTAL_TESTED_PIXELS): Promise<Picture> {
    return new Promise((resolve, reject) => {
      const image = new Image();
      image.addEventListener("load", () => {
        if (image.naturalWidth * image.naturalHeight <= totalPixels) {
          resolve(new Picture(image, 1));
          return;
        }
        
        resolve(new Picture(
          image,
          Math.sqrt(image.naturalWidth * image.naturalHeight / totalPixels)
        ));
      });
      image.addEventListener("error", reject);
  
      image.src = src;
    });
  }
  
  image: HTMLImageElement;
  scale: number;
  #pixels: ImageData;
  
  constructor(image: HTMLImageElement, scale: number) {
    this.image = image;
    this.scale = scale;
  
    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d");
    canvas.height = this.image.naturalHeight;
    canvas.width = this.image.naturalWidth;
  
    ctx.drawImage(this.image, 0, 0);
  
    this.#pixels = ctx.getImageData(0, 0, canvas.width, canvas.height);
  }
  
  *pixels(): Generator<HSLPoint> {
    let ex = 0;
    for (let x = 0; x < this.image.naturalWidth; x += this.scale) {
      const rx = Math.floor(x);
      let ey = 0;
  
      for (let y = 0; y < this.image.naturalHeight; y += this.scale) {
        const ry = Math.floor(y);
        const cord = Picture.#cord(rx, ry, this.image.naturalWidth);
        
        yield HSLPoint.fromRGB(this.#pixels.data[cord], this.#pixels.data[cord + 1], this.#pixels.data[cord + 2], ex, ey);
      }
    }
  }
  
  pixel(x: number, y: number): HSLPoint {
    const cord = Picture.#cord(x, y, this.image.naturalWidth);
    return HSLPoint.fromRGB(this.#pixels.data[cord], this.#pixels.data[cord + 1], this.#pixels.data[cord + 2], x, y);
  }
  
  static #cord(x: number, y: number, width: number) {
    return y * (width * 4) + x * 4;
  }
}