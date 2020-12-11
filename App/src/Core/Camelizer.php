<?php namespace Rushcon\Core;

class Camelizer {
    public static function camelize($scored) {
    return lcfirst(
      implode(
        '',
        array_map(
          'ucfirst',
          array_map(
            'strtolower',
            explode(
              '_', $scored)))));
  }
  /**
  * Transforms a camelCasedString to an under_scored_one
  */
  public static function decamelize($cameled) {
    return implode(
      '_',
      array_map(
        'strtolower',
        preg_split('/([A-Z]{1}[^A-Z]*)/', $cameled, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY))
        );
  }
}



