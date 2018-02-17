<?php

use UGTC\Cache\UG_Cache;

/**
 * Class Test_UG_Cache
 *
 * @package ug-tabs-chords
 * @version  0.0.1
 * @since 0.0.1
 * @author Leo Toikka
 */
class Test_UG_Cache extends WP_UnitTestCase {

  /**
   * Init test case, return test attributes.
   */
  public function test_ug_cache_init() {
    return array(
      'artist' => 'Crap Artist',
      'type'   => 'tabs',
      'order'  => 'title_srt',
      'limit'  => '20',
    );
  }

  /**
   * Test that the cache entries are added succesfully.
   *
   * @depends test_ug_cache_init
   */
  public function test_ug_cache_add_to_cache( $attributes ) {
    $cache_key = UG_Cache::create_cache_key( $attributes );

    $data = 'This is some test content.';
    UG_Cache::add_to_cache( $attributes, $data, 20 );

    $this->assertTrue( get_transient( $cache_key ) === $data );
  }

  /**
   * Test that the cache entries are removed succesfully.
   *
   * @depends test_ug_cache_init
   */
  public function test_ug_cache_remove_from_cache( $attributes ) {
    $data = 'This is some test content.';
    UG_Cache::add_to_cache( $attributes, $data, 20 );

    $this->assertTrue( UG_Cache::remove_from_cache( $attributes ) );
  }

  /**
   * Test that the cache is purged succesfully.
   *
   * @depends test_ug_cache_init
   */
  public function test_ug_cache_purge_cache( $attributes ) {
    global $wpdb;
    $data = 'This is some test content.';
    UG_Cache::add_to_cache( $attributes, $data, 20 );

    UG_Cache::purge_cache();
    $ug_transients = $wpdb->get_results(
        'SELECT option_name FROM ' . $wpdb->options .
      " WHERE option_name LIKE '_transient_%ugtc_%'"
    );

    $this->assertTrue( empty( $ug_transients ) );
  }
}
