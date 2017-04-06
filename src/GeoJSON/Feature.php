<?php

namespace GeoJSON;

class Feature implements \ArrayAccess, \JsonSerializable {

  protected $type = "Feature";
  protected $geometry = array();
  protected $properties = array();

  /**
   * @param {string} $type        - Geometry type of feature ("Point", "Line", "Polygon", etc.)
   * @param {array}  $coordinates - Geometry coordinates, dimensionality depends on geometry type.
   * @param {array}  $properties  - Array of key/value pairs of feature properties.
   */
  public function __construct( $input ) {

    # Validate input format. #
    if ( !( is_array( $input ) && array_key_exists( 'type', $input ) && array_key_exists( 'geometry', $input ) ) ) {
      throw new \Exception( "Invalid Feature format: Input must be an associative array including 'type' and 'geometry' keys." );
    }

    $this->geometry = new \GeoJSON\Geometry( $input['geometry'] );

    # Feature Properties #
    if ( array_key_exists( 'properties', $input ) && is_array( $input['properties'] ) ) {
      $this->properties = $input['properties'];
    }

  }

  public function __get ( $name ) {

    switch ( $name ) {

      case "type":
        return "Feature";

      case "geometry":
        return $this->geometry;

      case "properties":
        return $this->properties;

      case "bbox":
        return $this->geometry->bbox;

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
    if (count($this->properties)) {
      return array(
        'type' => $this->type,
        'geometry' => $this->geometry->toArray(),
        'properties' => $this->properties
      );
    } else {
      return array(
        'type' => $this->type,
        'geometry' => $this->geometry->toArray()
      );
    }
  }

  public function property($key, $value=null) {
    if ($value === null) {
      return $this->properties[$key];
    } else {
      return $this->properties[$key] = $value;
    }
  }

}

?>
