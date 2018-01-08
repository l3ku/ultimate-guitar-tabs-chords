<?php
/**
* File for caching the data retreived by UG_Client from Ultimate Guitar. Caching
* is done to minimize requests to external sites and improve load times.
*
* @package ug-tabs-chords
*/

namespace UGTC\Cache;

defined( 'ABSPATH' ) or die( 'Access Denied!' ); // Prevent direct access

if ( ! class_exists( 'UG_Cache' ) ) {

  /**
  * Class UG_Cache is responsible for providing an interface to WP_Transient
  * API for caching data.
  *
  * @package ug-tabs-chords
  * @version  0.0.1
  * @since 0.0.1
  * @author Leo Toikka
  */
  class UG_Cache {

    /**
     * Adds a cached entry by using the WP Transient API.
     *
     * @param array $entry_atts The unique content attributes to base the cache
     * key on
     * @param string $entry_value The content to cache
     * @param integer $time The expiration time in seconds for the cache entry,
     * default is 86400 seconds (24 hours)
     * TODO: Add an option to wp-admin for the user to specify the cache time.
     */
    public static function add_to_cache( $entry_atts, $entry_content, $time = 86400 ) {
      // Check if the entry is already cached and remove it
      if ( false !== self::get_cached( $entry_atts ) ) {
        self::remove_from_cache( $entry_atts );
      }

      $entry_hash = self::create_cache_key( $entry_atts );
      set_transient( 'ugtc_artist_entries_' . $entry_hash, $entry_content, $time );
    }

    /**
     * Gets the cached entries by attributes if exists.
     *
     * @param array $entry_atts The unique content cache key
     * @return boolean The cached value for $entry_name, false if the no
     * cached value was found
     */
    public static function get_cached( $entry_atts ) {
      $entry_hash = self::create_cache_key( $entry_atts );
      $cached_entries = get_transient( 'ugtc_artist_entries_' . $entry_hash );

      if ( false === $cached_entries ) {
        return false;
      }
      return $cached_entries;
    }

    /**
     * Removes an entry from the cache.
     *
     * @param array $entry_atts The unique content cache key
     * @return bool True on success, false otherwise
     */
    public static function remove_from_cache( $entry_atts ) {
      $entry_hash = self::create_cache_key( $entry_atts );
      return delete_transient( $entry_hash );
    }

    /**
     * Deletes all the cached entries added by the plugin.
     */
    public static function purge_cache() {
      global $wpdb;

      // NOTE: Check if this is a potential SQL injection hole?
      $ug_transients = $wpdb->get_results(
        "SELECT option_name FROM ". $wpdb->options ." WHERE option_name LIKE '_transient_%ugtc_%'"
      );

      // Remove all found transients
      if ( ! empty( $ug_transients ) ) {
        foreach ( $ug_transients as $ug_transient ) {
          delete_option( $ug_transient->option_name );
        }
      }
    }

    /**
     * Create a cache key (md5 hash) based on the attributes provided.
     *
     * @param  string $atts The string to create the cache key from
     * @return $string The cache key
     */
    public static function create_cache_key( $atts ) {
      return md5( json_encode( $atts ) );
    }
  }
}
