- on the map, in a marker popup, we render: 
  OC18BB5 Monstercam
  by: 170300
  Published:	2026-06-01
  Finds:	0   Fav: 0
  Webcam Cache / no container / 3 / 1.5

- the "by: 170300" is the problem, we are referncing the wrong property, it should be "hxdimpf"
  opencaching has per cache, an id and also some other property some name. We must us the name here
  for sure. This is a regression caused by constructing a uniCache object server side I believe

- fix this, system wide, commit and push this fix into the repo

- deploy the branch into ocde using the established process

- also deploy the branch into the new testsysten on oc3.baiti.net per established procedure


