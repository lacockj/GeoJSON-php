<?php

# Setup

header('Content-type: text/plain');

# Class Autoloader
spl_autoload_register(function($className){
  $classFile = "/" . str_replace("\\", "/", $className) . ".php";
  if ( __NAMESPACE__ ) $classFile = "/" . str_replace("\\", "/", __NAMESPACE__) . $classFile;
  $source = "./src" . $classFile;
  if ( file_exists( $source ) ) {
    include_once( $source );
    return;
  }
});


# testGeometryPolygonGood

$geometryRaw = [
  "type" => "Polygon",
  "coordinates" => [[[10,0],[50,10],[40,50],[0,40],[10,0]]]
];

$geometryObject = new \GeoJSON\Geometry( $geometryRaw );

echo json_encode( $geometryObject->type ), PHP_EOL;
echo json_encode( $geometryObject['type'] ), PHP_EOL;
echo json_encode( $geometryObject->coordinates ), PHP_EOL;
echo json_encode( $geometryObject['coordinates'] ), PHP_EOL;
echo json_encode( $geometryObject->bbox ), PHP_EOL;
echo json_encode( $geometryObject ), PHP_EOL;
echo json_encode( $geometryObject->toArray() ), PHP_EOL;
echo json_encode( $geometryObject->wkt() ), PHP_EOL;
echo json_encode( $geometryObject->wktOfBbox() ), PHP_EOL;


#testGeometryPolygonSelfClosing

$geometryRaw = [
  "type" => "Polygon",
  "coordinates" => [[[10,0],[50,10],[40,50],[0,40]]]
];

$geometryObject = new \GeoJSON\Geometry( $geometryRaw );

echo json_encode( $geometryObject->type ), PHP_EOL;
echo json_encode( $geometryObject['type'] ), PHP_EOL;
echo json_encode( $geometryObject->coordinates ), PHP_EOL;
echo json_encode( $geometryObject['coordinates'] ), PHP_EOL;
echo json_encode( $geometryObject->bbox ), PHP_EOL;
echo json_encode( $geometryObject ), PHP_EOL;
echo json_encode( $geometryObject->toArray() ), PHP_EOL;
echo json_encode( $geometryObject->wkt() ), PHP_EOL;
echo json_encode( $geometryObject->wktOfBbox() ), PHP_EOL;

?>