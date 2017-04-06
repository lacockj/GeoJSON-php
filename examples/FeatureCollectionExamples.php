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


#testFeatureCollectionGood

$fcRaw = [
  "type" => "FeatureCollection",
  "features" => [
    [
      "type" => "Feature",
      "geometry" => [
        "type" => "Point",
        "coordinates" => [50,10]
      ],
      "properties" => [
        "id" => 1,
        "label" => "First Base"
      ]
    ],
    [
      "type" => "Feature",
      "geometry" => [
        "type" => "Point",
        "coordinates" => [40,50]
      ],
      "properties" => [
        "id" => 2,
        "label" => "Second Base"
      ]
    ],
  ]
];

$fcObj = new \GeoJSON\FeatureCollection( $fcRaw );

echo json_encode( $fcObj ), PHP_EOL;
echo json_encode( $fcObj->type ), PHP_EOL;
echo json_encode( $fcObj['type'] ), PHP_EOL;
//echo json_encode( $fcObj->geometry ), PHP_EOL;
//echo json_encode( $fcObj['geometry'] ), PHP_EOL;
echo json_encode( $fcObj->bbox ), PHP_EOL;

?>