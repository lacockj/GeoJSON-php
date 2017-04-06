# GeoJSON-php

Use GeoJSON data as a PHP Class Object, including handy object methods.

GeoJSON is a great format for sharing mapping data across different systems and
programming languages, especially JavaScript. But on its own, GeoJSON is just a
block of data. This `GeoJSON` Object adds handy reference and utility functions
to the data, without changing how you would access the data were it an ordinary
JavaScript Object.

## A Couple Quick Examples

### Example GeoJSON Feature
```php
$featureRaw = [
  "type" => "Feature",
  "geometry" => [
    "type" => "Polygon",
    "coordinates" => [[[10,0],[50,10],[40,50],[0,40]]]
  ]
];

$featureObject = new \GeoJSON\Feature( $featureRaw );

echo json_encode( $featureObject ), PHP_EOL;
// {"type":"Feature","geometry":{"type":"Polygon","coordinates":[[[10,0],[50,10],[40,50],[0,40],[10,0]]]}}
// Note that the polygon coordinate ring was automatically "closed" so the first and last points are the same.

echo json_encode( $featureObject->bbox ), PHP_EOL;
// [0,0,50,50]
```

### Example GeoJSON FeatureCollection
```php
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

$myBases = new \GeoJSON\FeatureCollection( $fcRaw );

echo json_encode( $fcObj->bbox ), PHP_EOL;
// [40,10,50,50]
```

## Installation

1. [Download](https://github.com/lacockj/GeoJSON-php/archive/master.zip) or clone this repo.
2. Copy the `src/GeoJSON` folder into the directory you keep your PHP classes.
3. `include` the GeoJSON class files, or use the autoloader of your choice.

## Reference

There are currently four object classes at your disposal in this script, each
starts with the standard GeoJSON data format, then adds a few convenience methods.

- FeatureCollection
- Feature
- Geometry
- Bbox

### Methods Common to All GeoJSON-php Classes

#### Construction:

All class object constructors in GeoJSON-php expect input in the the standard
GeoJSON format. See [RFC7946](https://tools.ietf.org/html/rfc7946) for details.

```php
$fc = new \GeoJSON\FeatureCollection( $featureCollectionData );
$f = new \GeoJSON\Feature( $featureData );
$g = new \GeoJSON\Geometry( $featureGeometryData );
```

#### Getting the Bounding-Box

For all classes, the `bbox` property is automatically calculated when accessed.

```php
$bbox = $g->bbox;                      // Object property access,
$southernmostLatitude = $f['bbox'][0]; // or array access.
```

#### Converting to JSON string:

Class methods and housekeeping properties are automatically stripped out by the
`jsonSerialize()` method and when using `json_encode( $myGeoJsonObj )`.

```php
$jsonString = json_encode( $myFeatureClassObject );
```

#### Converting back to an associative array:

You can turn any GeoJSON Class Object back into a simple associative array with
the `toArray()` method.

```php
$plainObject = $myGeoJsonClassObject->toArray();
```
