<?php
/**
 * Ultimate Guitar client valid values.
 *
 * @package ug-tabs-chords
 */

namespace UGTC\Client;

if ( ! defined( 'ABSPATH' ) ) {
  die( 'Access Denied!' );
}

/**
 * Get valid content type values.
 *
 * @return array string Array with type valid slugs as keys, and name as values.
 */
function ugtc_get_valid_types() {
  return array(
    'tabs'       => __( 'Tabs', 'ug-tabs-chords' ),
    'chords'     => __( 'Chords', 'ug-tabs-chords' ),
    'guitar_pro' => __( 'Guitar Pro', 'ug-tabs-chords' ),
    'power'      => __( 'Power', 'ug-tabs-chords' ),
    'bass'       => __( 'Bass', 'ug-tabs-chords' ),
    'drums'      => __( 'Drums', 'ug-tabs-chords' ),
    'ukulele'    => __( 'Ukulele', 'ug-tabs-chords' ),
    'official'   => __( 'Official', 'ug-tabs-chords' ),
    'video'      => __( 'Video', 'ug-tabs-chords' ),
    'all'        => __( 'All', 'ug-tabs-chords' ),
  );
}

/**
 * Get valid rating values (1-5).
 *
 * @return array int Valid rating values
 */
function ugtc_get_valid_ratings() {
  return array( 1, 2, 3, 4, 5 );
}

/**
 * Get valid order values.
 * @return array string Array with order valid slug as key, and name as value.
 */
function ugtc_get_valid_orders() {
  return array(
    'title_srt' => __( 'Title ABC', 'ug-tabs-chords' ),
    'date'      => __( 'Date', 'ug-tabs-chords' ),
  );
}
