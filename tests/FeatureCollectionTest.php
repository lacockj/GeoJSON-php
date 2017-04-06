<?php

class GeoJSON_FeatureCollectionTest extends PHPUnit_Framework_TestCase {

  public $fcGood = [
    "type" => "FeatureCollection",
    "features" => [
      [
        "type" => "Feature",
        "geometry" => [
          "type" => "Polygon",
          "coordinates" => [[[10,0],[50,10],[40,50],[0,40],[10,0]]]
        ]
      ],
      [
        "type" => "Feature",
        "geometry" => [
          "type" => "Polygon",
          "coordinates" => [[[10,0],[90,20],[70,100],[-10,80],[10,0]]]
        ]
      ]
    ]
  ];

  public function testFeatureCollectionGood() {

    $fcObj = new \GeoJSON\FeatureCollection( $this->fcGood );

    $this->assertEquals( $this->fcGood, $fcObj->toArray() );
    $this->assertEquals( $this->fcGood['type'], $fcObj->type );
    $this->assertEquals( $this->fcGood['type'], $fcObj['type'] );
    $this->assertEquals( [-10,0,90,100], $fcObj->bbox );
    $this->assertEquals(
      '{"type":"FeatureCollection","features":[{"type":"Feature","geometry":{"type":"Polygon","coordinates":[[[10,0],[50,10],[40,50],[0,40],[10,0]]]}},{"type":"Feature","geometry":{"type":"Polygon","coordinates":[[[10,0],[90,20],[70,100],[-10,80],[10,0]]]}}]}',
      json_encode( $fcObj )
    );

  }

}

?>