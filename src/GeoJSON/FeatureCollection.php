<?php

namespace GeoJSON;

class FeatureCollection implements \ArrayAccess, \JsonSerializable {

  private $type = "FeatureCollection";
  private $features = array();
  private $properties = array();
  private $bbox = null;

  public function __construct( $input=null ) {

    # New Empty Feature Collection? #
    if ( $input === null ) return $this;

    # Validate input format. #
    if ( !( is_array( $input ) && array_key_exists( 'type', $input ) && array_key_exists( 'features', $input ) ) ) {
      throw new \Exception( "Invalid Feature format: Input must be an associative array including 'type' and 'geometry' keys." );
    }

    if ( is_array( $input['features'] ) ) {
      foreach ( $input['features'] as $feature ) {
        $this->features[] = new \GeoJSON\Feature( $feature );
      }
    }

    # Feature Properties #
    if ( array_key_exists( 'properties', $input ) && is_array( $input['properties'] ) ) {
      $this->properties = $properties;
    }

  }

  public function __get ( $name ) {

    switch ( $name ) {

      case "type":
        return $this->type;

      case "features":
        return $this->features;

      case "properties":
        return $this->properties;

      case "bbox":
        return $this->bbox();

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

  public function jsonSerialize() {
    return $this->toArray();
  }

  # Class Methods #

  public function toArray() {
    if (count($this->properties)) {
      return array(
        'type' => $this->type,
        'features' => array_map( function($f){return $f->toArray();}, $this->features ),
        'properties' => $this->properties
      );
    } else {
      return array(
        'type' => $this->type,
        'features' => array_map( function($f){return $f->toArray();}, $this->features ),
      );
    }
  }

  public function add($feature) {
        if (get_class($feature)=="GeoJSON\Feature") {
            $this->features[] = $feature;
        } else {
            throw new \Exception('Invalid addition to FeatureCollection. Must be a "Feature" class object.');
        }
  }

  public function features() {
    return $this->features;
  }

  public function properties() {
    return $this->properties;
  }

  public function property($key, $value=null) {
    if ($value === null) {
      return $this->properties[$key];
    } else {
      return $this->properties[$key] = $value;
    }
  }

  public function bbox( $recalc=false ){
    if ( $recalc || $this->bbox === null ) {
      $bb = new \GeoJSON\Bbox();
      foreach ( $this->features as $feature ) {
        $bb->merge( $feature->bbox );
      }
      $this->bbox = $bb->toArray();
    }
    return $this->bbox;
  }

}

?>
