import {Picture} from "./structs/Picture";
import {findGroups} from "./KMean/core";
import {builder, HSLCentroid} from "./structs/HSLCentroid";

export async function groupImagePixels(src: string, groupsCount: number, totalPixels: number = 100_000) {
  const pic = await Picture.load(src, totalPixels);
  
  const points = Array.from(pic.pixels());
  const centroids = new Array(groupsCount);
  
  for (let i = 0; i < centroids.length; i++) {
    centroids[i] = pic.pixel(
      Math.floor(Math.random() * pic.image.naturalWidth),
      Math.floor(Math.random() * pic.image.naturalHeight)
    );
  }
  
  console.log(centroids);
  
  const groups = findGroups(points, centroids, builder) as HSLCentroid[];
  
  return groups.sort((a, b) => {
    if (a.connectedPoints().length === b.connectedPoints().length) {
      return 0;
    }
  
    if (a.connectedPoints().length > b.connectedPoints().length) {
      return -1;
    }
    
    return 1;
  });
}