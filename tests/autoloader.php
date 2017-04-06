<?php
$classesDir = "./src";
spl_autoload_register(function($className){
  global $classesDir;
  $classFile = "/" . str_replace("\\", "/", $className) . ".php";
  if ( __NAMESPACE__ ) $classFile = "/" . str_replace("\\", "/", __NAMESPACE__) . $classFile;
  $source = $classesDir . $classFile;
  if ( file_exists( $source ) ) {
    include_once( $source );
    return;
  }
});
?>