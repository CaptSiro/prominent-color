import {Centroid, CentroidBuilder} from "../KMean/Centroid";
import {Point} from "../KMean/Point";
import {HSLPoint} from "./HSLPoint";

export class HSLCentroid implements Centroid {
  static default() {
    //todo
  }
  
  
  
  points: HSLPoint[];
  point: HSLPoint;
  
  constructor(point: HSLPoint, points: HSLPoint[]) {
    this.point = point;
    this.points = points;
  }
  
  connectedPoints(): HSLPoint[] {
    return this.points;
  }
  
  intoPoint(): HSLPoint {
    return this.point;
  }
  
}

export const builder: CentroidBuilder = (points: HSLPoint[]): HSLCentroid => {
  let sumH = 0;
  let sumS = 0;
  let sumL = 0;
  
  for (let i = 0; i < points.length; i++) {
    sumH += points[i].h;
    sumS += points[i].s;
    sumL += points[i].l;
  }
  
  return new HSLCentroid(
    new HSLPoint(sumH / points.length, sumS / points.length, sumL / points.length, 0, 0),
    points
  );
}