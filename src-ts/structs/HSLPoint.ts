import {Point} from "../KMean/Point";

export class HSLPoint implements Point {
  static fromRGB(r: number, g: number, b: number, x: number, y: number): HSLPoint {
    let red = r / 255;
    let green = g / 255;
    let blue = b / 255;
    
    const max = Math.max(red, green, blue);
    const min = Math.min(red, green, blue);
    
    const d = max - min;
    const l = (min + max) / 2;
    
    if (max === min) {
      return new this(0, 0, l, x, y);
    }
    
    const s = l > 0.5
      ? d / (2 - max - min)
      : d / (max - min);
    
    let h: number;
    switch (max) {
      case red:
        h = (green - blue) / d + (green < blue ? 6 : 0);
        break;
      case green:
        h = (red - green) / d + 2;
        break;
      case blue:
        h = (red - green) / d + 4;
        break;
    }
    
    return new this(360 * ((h ?? 0) / 6), s, l, x, y);
  }
  
  h: number;
  s: number;
  l: number;
  x: number;
  y: number;
  
  constructor(h: number, s: number, l: number, x: number, y: number) {
    this.h = h;
    this.s = s;
    this.l = l;
    this.x = x;
    this.y = y;
  }
  
  closest(points: HSLPoint[]): number {
    let smallest = Number.MAX_SAFE_INTEGER;
    let pointIndex = -1;
  
    for (let i = 0; i < points.length; i++) {
      const dist = this.distanceTo(points[i]);
      
      if (dist > smallest) {
        continue;
      }
      
      smallest = dist;
      pointIndex = i;
    }
    
    return pointIndex;
  }
  
  distanceTo(point: HSLPoint): number {
    return Math.abs(this.h - point.h) + Math.abs(this.s - point.s) + Math.abs(this.l - point.l);
  }
  
  toString(): string {
    return `hsl(${Math.floor(this.h * 100) / 100}, ${Math.floor(this.s * 100) / 100}%, ${Math.floor(this.l * 100) / 100}%)`;
  }
}