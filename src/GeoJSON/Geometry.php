<?php

namespace GeoJSON;

class Geometry implements \ArrayAccess, \JsonSerializable {

  private $supportedGeometryTypes = array("Point", "LineString", "Polygon", "MultiPolygon");
  # Eventually "Point", "MultiPoint", "LineString", "MultiLineString", "Polygon", "MultiPolygon", "GeometryCollection"

  protected $type;
  protected $coordinates;
  protected $bbox;

  public function __construct( $input ) {

    # Handle Well-Known Text (WKT) input string. #
    if ( is_string( $input ) ) {
      $input = $this->parseWKT( $input );
    }

    # Validate input format. #
    if ( !( is_array( $input ) && array_key_exists( 'type', $input ) && array_key_exists( 'coordinates', $input ) ) ) {
      throw new \Exception( "Invalid geometry format: Input must be an associative array including 'type' and 'coordinates' keys." );
    }

    # Validate supported geometry type. #
    if ( ! in_array( $input['type'], $this->supportedGeometryTypes ) ) {
      throw new \Exception( "Unsupported geometry type: " . $input['type'] . "; Must be one of the following: " . implode( ", ", $this->supportedGeometryTypes ) );
    }

    $this->type = $input['type'];

    switch( $input['type'] ) {

      case "Point":
        if (is_array($input['coordinates'])) {
            if (count($input['coordinates']) == 3) {
              $x = floatval($input['coordinates'][0]);
              $y = floatval($input['coordinates'][1]);
              $z = floatval($input['coordinates'][2]);
              $this->coordinates = array($x,$y,$z);
            } elseif (count($input['coordinates']) == 2) {
              $x = floatval($input['coordinates'][0]);
              $y = floatval($input['coordinates'][1]);
              $this->coordinates = array($x,$y);
            } else {
              throw new \Exception('Coordinates must be a two-or-three-element array in X,Y,Z* order. *(Z ordinate is optional.)');
            }
        } else {
          throw new \Exception('Coordinates must be a two-or-three-element array in X,Y,Z* order. *(Z ordinate is optional.)');
        }
        break;

      case "LineString":
        if (is_array($input['coordinates'][0])) {
          $this->coordinates = $input['coordinates'];
        } else {
          throw new \Exception('Coordinates must be a two-dimentional array of Point coordinates.');
        }
        break;

      case "Polygon":
        if (is_scalar($input['coordinates'][0][0])) {
          $this->coordinates = array($input['coordinates']);
        } elseif (is_array($input['coordinates'][0][0])) {
          $this->coordinates = $input['coordinates'];
        } else {
          throw new \Exception('Coordinates must be a three-dimentional array of LineStrings coordinate arrays. The first is the outer LineString; the second is the inner, cut-out LineString.');
        }
        # Ensure polygon closure.
        foreach( $this->coordinates as &$linestring ) {
          $last = count($linestring) - 1;
          if ( $linestring[0][0] !== $linestring[$last][0] || $linestring[0][1] !== $linestring[$last][1] ) {
            $linestring[] = $linestring[0];
          }
        }
        break;

      case "MultiPolygon":
        # Parts: entire geometry,            each polygon,          polygon's outline and cutout,    and the point coordinate set
        if ( is_array($input['coordinates']) && is_array($input['coordinates'][0]) && is_array($input['coordinates'][0][0]) && is_array($input['coordinates'][0][0][0] ) ) {
          $this->coordinates = $input['coordinates'];
        } else {
          throw new \Exception('MultiPolygon coordinates must be a four-dimentional array.');
        }
        # Ensure polygon closure.
        foreach ( $this->coordinates as &$poly ) {
          foreach( $poly as &$linestring ) {
            $last = count($linestring) - 1;
            if ( $linestring[0][0] !== $linestring[$last][0] || $linestring[0][1] !== $linestring[$last][1] ) {
              $linestring[] = $linestring[0];
            }
          }
        }
        break;

    } # End switch( $input['type'] ) #
  } # End __construct #

  public function __get ( $name ) {

    switch ( $name ) {

      case "type":
        return $this->type;

      case "coordinates":
        return $this->coordinates;

      case "bbox":
        return ( $this->bbox !== null ) ? $this->bbox : $this->bbox();

    }

  }

  # ArrayAccess Implementaion #

  public function offsetExists( $offset ) {
    return (bool)( in_array( $offset, array( "type", "geometry", "properties", "bbox" ) ) );
  }

  public function offsetGet($offset) {
    return $this->{$offset};
  }

  public function offsetSet( $offset, $value ) {
    # Direct setting not supported. #
  }

  public function offsetUnset( $offset ) {
    $this->{$offset} = null;
  }

  # JsonSerializable Implementation #

  public function jsonSerialize() {
    return $this->toArray();
  }

  # Class Methods #

  public function toArray() {
    return array(
      'type' => $this->type,
      'coordinates' => $this->coordinates
    );
  }

  public function bbox() {
    $bb = new \GeoJSON\Bbox();
    $bb->expand( $this->coordinates );
    $this->bbox = $bb->toArray();
    return $this->bbox;
  }

  /**
   * Convert to Well-Known Text format
   * Perfect for inserting geometry into a database.
   * @param {int} $d The number of dimensions.
   * @return {string} Geometry in Well-Known Text format.
   */
  public function wkt( $d=2 ) {
    switch ( $this->type ) {

      case "Point":
        return $this->wktOfPoint( $d );

      case "LineString":
        return $this->wktOfLineString( $d );

      case "Polygon":
        return $this->wktOfPolygon( $d );

      case "MultiPolygon":
        return $this->wktOfMultiPolygon( $d );

      default:
        return null;
    }
  }

  protected function wktOfPoint( $d=2 ) {
    # Example: 'POINT(1 1)'
    return "POINT(" . $this->coordinates[0] . " " . $this->coordinates[1] . ")";
  }

  protected function wktOfLineString( $d=2 ) {
    # Example: 'LINESTRING(0 0,1 1,2 2)'
    $lineString = array();
    foreach ( $this->coordinates as $pt ) {
      $lineString[] = $pt[0]." ".$pt[1];
    }
    return "LINESTRING(" . implode( ",", $lineString ) . ")";
  }

  protected function wktOfPolygon( $d=2 ) {
    # Example: 'POLYGON((0 0,10 0,10 10,0 10,0 0),(5 5,7 5,7 7,5 7, 5 5))'
    $rings = array();
    foreach( $this->coordinates as $linestring ) {
      $ring = array();
      foreach ( $linestring as $pt ) {
        //$ring[] = implode( " ", $pt );
        $ring[] = $pt[0]." ".$pt[1];
      }
      $rings[] = implode( ",", $ring );
    }
    return "POLYGON((" . implode( "),(", $rings ) . "))";
  }

  protected function wktOfMultiPolygon( $d=2 ) {
    # Example: 'MULTIPOLYGON(((0 0,10 0,10 10,0 10,0 0)),((5 5,7 5,7 7,5 7, 5 5)))'
    $polys = array();
    foreach( $this->coordinates as $poly ) {
      $rings = array();
      foreach( $poly as $linestring ) {
        $ring = array();
        foreach ( $linestring as $pt ) {
          //$ring[] = implode( " ", $pt );
          $ring[] = $pt[0]." ".$pt[1];
        }
        $rings[] = implode( ",", $ring );
      }
      $polys[] = "((" . implode( "),(", $rings ) . "))";
    }
    return "MULTIPOLYGON(" . implode( "),(", $polys ) . ")";
  }

  public function wktOfBbox() {
    if ( $this->bbox === null ) $this->bbox();
    if ( is_array( $this->bbox ) ) {
      if ( count( $this->bbox ) === 4 ) {
        $w = $this->bbox[0];
        $s = $this->bbox[1];
        $e = $this->bbox[2];
        $n = $this->bbox[3];
      } elseif ( count( $this->bbox === 6 ) ) {
        $w = $this->bbox[0];
        $s = $this->bbox[1];
        $e = $this->bbox[3];
        $n = $this->bbox[4];
      }
      return "POLYGON(($w $s,$e $s,$e $n,$w $n,$w $s))";
    }
    return null;
  }

  public static function parseWkt ( $wkt ) {
    $geometry = array();
    $pos = strpos( $wkt, "(" );
    $geometry['type'] = strtoupper( substr( $wkt, 0, $pos ) );
    switch ( $geometry['type'] ) {

      case "POINT":
        $geometry['type'] = "Point";
        # "POINT(1 1)"
        $geometry['coordinates'] = trim( substr( $wkt, $pos ), "()" );
        # "1 1"
        $geometry['coordinates'] = explode( " ", $geometry['coordinates'] );
        # [ "1", "1" ]
        foreach ( $geometry['coordinates'] as &$num ) {
          $num = floatval( $num );
        }
        # [ 1, 1 ]
        break;

      case "LINESTRING":
        $geometry['type'] = "LineString";
        # "LINESTRING(0 0,1 1,2 2)"
        $geometry['coordinates'] = trim( substr( $wkt, $pos ), "()" );
        # "0 0,1 1,2 2"
        $geometry['coordinates'] = explode( ",", $geometry['coordinates'] );
        # [ "0 0", "1 1", "2 2" ]
        foreach ( $geometry['coordinates'] as &$pt ) {
          $pt = explode( " ", $pt );
          # [ [ "0", "0" ], [ "1", "1" ], [ "2", "2" ] ]
          foreach ( $pt as &$num ) {
            $num = floatval( $num );
          }
          # [ [ 0, 0 ], [ 1, 1 ], [ 2, 2 ] ]
        }
        break;

      case "POLYGON":
        $geometry['type'] = "Polygon";
        $geometry['coordinates'] = trim( substr( $wkt, $pos ), "()" );
        $geometry['coordinates'] = explode( "),(", $geometry['coordinates'] );
        foreach ( $geometry['coordinates'] as &$ring ) {
          $ring = explode( ",", $ring );
          foreach ( $ring as &$pt ) {
            $pt = explode( " ", $pt );
            foreach ( $pt as &$num ) {
              $num = floatval( $num );
            }
          }
        }
        break;

      case "MULTIPOLYGON":
        $geometry['type'] = "MultiPolygon";
        $geometry['coordinates'] = trim( substr( $wkt, $pos ), "()" );
        $geometry['coordinates'] = explode( ")),((", $geometry['coordinates'] );
        foreach ( $geometry['coordinates'] as &$polygon ) {
          $polygon = explode( "),(", $polygon );
          foreach ( $polygon as &$linestring ) {
            $linestring = explode( ",", $linestring );
            foreach ( $linestring as &$point ) {
              $point = explode( " ", $point );
              foreach ( $point as &$num ) {
                $num = floatval( $num );
              }
            }
          }
        }
        break;

      default:
        $geometry = null;
    }
    return $geometry;
  }

}

?>
