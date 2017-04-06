<?php

class GeoJSON_GeometryTest extends PHPUnit_Framework_TestCase {

  public $geometryGood = [
    "type" => "Polygon",
    "coordinates" => [[[10,0],[50,10],[40,50],[0,40],[10,0]]]
  ];

  public $geometryNotClosed = [
    "type" => "Polygon",
    "coordinates" => [[[10,0],[50,10],[40,50],[0,40]]]
  ];

  public $geometryUnsupported = [
    "type" => "UnsupportedType",
    "coordinates" => []
  ];

  public function testGeometryPolygonGood() {

    $geometryObject = new \GeoJSON\Geometry( $this->geometryGood );

    $this->assertEquals( $this->geometryGood['type'], $geometryObject->type );
    $this->assertEquals( $this->geometryGood['type'], $geometryObject['type'] );
    $this->assertEquals( $this->geometryGood['coordinates'], $geometryObject->coordinates );
    $this->assertEquals( $this->geometryGood['coordinates'], $geometryObject['coordinates'] );
    $this->assertEquals( [0,0,50,50], $geometryObject->bbox );
    $this->assertEquals( $this->geometryGood, $geometryObject->toArray() );
    $this->assertEquals(
      '{"type":"Polygon","coordinates":[[[10,0],[50,10],[40,50],[0,40],[10,0]]]}',
      json_encode( $geometryObject )
    );
    $this->assertEquals( "POLYGON((10 0,50 10,40 50,0 40,10 0))", $geometryObject->wkt() );
    $this->assertEquals( "POLYGON((0 0,50 0,50 50,0 50,0 0))", $geometryObject->wktOfBbox() );

  }

  public function testGeometryPolygonSelfClosing() {

    $geometryObject = new \GeoJSON\Geometry( $this->geometryNotClosed );

    $this->assertEquals( $this->geometryGood['type'], $geometryObject->type );
    $this->assertEquals( $this->geometryGood['type'], $geometryObject['type'] );
    $this->assertEquals( $this->geometryGood['coordinates'], $geometryObject->coordinates );
    $this->assertEquals( $this->geometryGood['coordinates'], $geometryObject['coordinates'] );
    $this->assertEquals( [0,0,50,50], $geometryObject->bbox );
    $this->assertEquals( $this->geometryGood, $geometryObject->toArray() );
    $this->assertEquals(
      '{"type":"Polygon","coordinates":[[[10,0],[50,10],[40,50],[0,40],[10,0]]]}',
      json_encode( $geometryObject )
    );
    $this->assertEquals( "POLYGON((10 0,50 10,40 50,0 40,10 0))", $geometryObject->wkt() );
    $this->assertEquals( "POLYGON((0 0,50 0,50 50,0 50,0 0))", $geometryObject->wktOfBbox() );

  }

  public function testGeometryUnsupportedType() {

    $this->expectExceptionMessage( "Unsupported geometry type: UnsupportedType; Must be one of the following: Point, LineString, Polygon, MultiPolygon" );

    $geometryObject = new \GeoJSON\Geometry( $this->geometryUnsupported );

  }

}

?>