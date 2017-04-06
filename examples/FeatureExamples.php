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


#testFeaturePolygonGood

$featureRaw = [
  "type" => "Feature",
  "geometry" => [
    "type" => "Polygon",
    "coordinates" => [[[10,0],[50,10],[40,50],[0,40],[10,0]]]
  ]
];

$featureObject = new \GeoJSON\Feature( $featureRaw );

echo json_encode( $featureObject->type ), PHP_EOL;
echo json_encode( $featureObject['type'] ), PHP_EOL;
echo json_encode( $featureObject->geometry ), PHP_EOL;
echo json_encode( $featureObject['geometry'] ), PHP_EOL;
echo json_encode( $featureObject->bbox ), PHP_EOL;
echo json_encode( $featureObject ), PHP_EOL;

?>