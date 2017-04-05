<?php

namespace GeoJSON;

class Feature implements \JsonSerializable {
  protected $type = "Feature";
  protected $geometry = array();
  protected $properties = array();

  /**
   * @param {string} $type        - Geometry type of feature ("Point", "Line", "Polygon", etc.)
   * @param {array}  $coordinates - Geometry coordinates, dimensionality depends on geometry type.
   * @param {array}  $properties  - Array of key/value pairs of feature properties.
   */
  public function __construct( $type, $coordinates, $properties=null ) {

    $this->geometry = new \GeoJSON\Geometry( $type, $coordinates );

    # Feature Properties #
    if (is_array($properties)) {
      $this->properties = $properties;
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

  public function property($key, $value=null) {
    if ($value === null) {
      return $this->properties[$key];
    } else {
      return $this->properties[$key] = $value;
    }
  }

  public function jsonSerialize() {
    if (count($this->properties)) {
      return array(
        'type' => $this->type,
        'geometry' => $this->geometry,
        'properties' => $this->properties
      );
    } else {
      return array(
        'type' => $this->type,
        'geometry' => $this->geometry
      );
    }
  }

  public function JSON() {
    return json_encode(array(
      'type' => $this->type,
      'geometry' => $this->geometry,
      'properties' => $this->properties
    ));
  }
}

?>
