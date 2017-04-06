<?php

namespace GeoJSON;

class Bbox implements \JsonSerializable {
  protected $min = array();
  protected $max = array();

  public function __construct( $box=null ) {
    if ( is_array( $box ) ) {
      if ( count($box) === 4 ) {
        $this->min[0] = $box[0];
        $this->min[1] = $box[1];
        $this->max[0] = $box[2];
        $this->max[1] = $box[3];
      } else if ( count($box) === 6 ) {
        $this->min[0] = $box[0];
        $this->min[1] = $box[1];
        $this->min[2] = $box[2];
        $this->max[0] = $box[3];
        $this->max[1] = $box[4];
        $this->max[2] = $box[5];
      }
    }
  }

  public function __get ( $name ) {
    switch ( $name ) {
      case "min":
        return $this->min;
        break;
      case "max":
        return $this->max;
        break;
    }
  }

  public function jsonSerialize() {
    return array_merge( $this->min, $this->max );
  }

  public function toArray() {
    return array_merge( $this->min, $this->max );
  }

  public function expand ( $coords ){
    if ( is_array( $coords ) ) {
      if ( is_array( $coords[0] ) ) {
        foreach ( $coords as $c ) {
          $this->expand( $c );
        }
      } else {
        if ( count($this->min) === 0 ) {
          $this->min[0] = $coords[0];
          $this->min[1] = $coords[1];
          if ( count($coords) > 2 ) $this->min[2] = $coords[2];
        } else {
          if ( $coords[0] < $this->min[0] ) $this->min[0] = $coords[0];
          if ( $coords[1] < $this->min[1] ) $this->min[1] = $coords[1];
          if ( count($coords) > 2 && $coords[2] < $this->min[2] ) $this->min[2] = $coords[2];
        }
        if ( count($this->max) === 0 ) {
          $this->max[0] = $coords[0];
          $this->max[1] = $coords[1];
          if ( count($coords) > 2 ) $this->max[2] = $coords[2];
        } else {
          if ( $coords[0] > $this->max[0] ) $this->max[0] = $coords[0];
          if ( $coords[1] > $this->max[1] ) $this->max[1] = $coords[1];
          if ( count($coords) > 2 && $coords[2] > $this->max[2] ) $this->max[2] = $coords[2];
        }
      }
    }
    return $this;
  }

  public function merge ( $box ){
    $bb2 = new \GeoJSON\Bbox( $box );
    $this->expand( $bb2->min );
    $this->expand( $bb2->max );
    return $this;
  }

}

?>
