<?php

namespace GeoJSON;

class FeatureCollection implements \JsonSerializable {
    private $type = "FeatureCollection";
    private $features = array();
    private $properties = array();

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

    public function jsonSerialize() {
        if (count($this->properties)) {
            return array(
                'type' => $this->type,
                'features' => $this->features,
                'properties' => $this->properties
            );
        } else {
            return array(
                'type' => $this->type,
                'features' => $this->features
            );
        }
    }

    public function JSON() {
        return json_encode(array(
            'type' => $this->type,
            'features' => $this->features,
            'properties' => $this->properties
        ));
    }
}

?>
