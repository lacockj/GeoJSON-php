<?php

class GeoJSON_FeatureTest extends PHPUnit_Framework_TestCase {

  public $featureGood = [
    "type" => "Feature",
    "geometry" => [
      "type" => "Polygon",
      "coordinates" => [[[10,0],[50,10],[40,50],[0,40],[10,0]]]
    ]
  ];

  public $featureNotClosed = [
    "type" => "Feature",
    "geometry" => [
      "type" => "Polygon",
      "coordinates" => [[[10,0],[50,10],[40,50],[0,40]]]
    ]
  ];

  public $featureGeometryUnsupported = [
    "type" => "Feature",
    "geometry" => [
      "type" => "UnsupportedType",
      "coordinates" => []
    ]
  ];

  public function testFeaturePolygonGood() {

    $featureObject = new \GeoJSON\Feature( $this->featureGood );

    $this->assertEquals( $this->featureGood['type'], $featureObject->type );
    $this->assertEquals( $this->featureGood['type'], $featureObject['type'] );
    $this->assertEquals( $this->featureGood['geometry'], $featureObject->geometry->toArray() );
    $this->assertEquals( $this->featureGood['geometry'], $featureObject['geometry']->toArray() );
    $this->assertEquals( [0,0,50,50], $featureObject->bbox );
    $this->assertEquals(
      '{"type":"Feature","geometry":{"type":"Polygon","coordinates":[[[10,0],[50,10],[40,50],[0,40],[10,0]]]}}',
      json_encode( $featureObject )
    );

  }

  public function testFeaturePolygonSelfClosing() {

    $featureObject = new \GeoJSON\Feature( $this->featureNotClosed );

    $this->assertEquals( $this->featureGood['type'], $featureObject->type );
    $this->assertEquals( $this->featureGood['type'], $featureObject['type'] );
    $this->assertEquals( $this->featureGood['geometry'], $featureObject->geometry->toArray() );
    $this->assertEquals( $this->featureGood['geometry'], $featureObject['geometry']->toArray() );
    $this->assertEquals( [0,0,50,50], $featureObject->bbox );
    $this->assertEquals(
      '{"type":"Feature","geometry":{"type":"Polygon","coordinates":[[[10,0],[50,10],[40,50],[0,40],[10,0]]]}}',
      json_encode( $featureObject )
    );

  }

  public function testFeatureUnsupportedType() {

    $this->expectExceptionMessage( "Unsupported geometry type: UnsupportedType; Must be one of the following: Point, LineString, Polygon, MultiPolygon" );

    $featureObject = new \GeoJSON\Feature( $this->featureGeometryUnsupported );

  }

}

?>