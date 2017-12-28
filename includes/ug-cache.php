<?php
/**
* File for caching the data retreived by UGClient from Ultimate Guitar. Caching
* is done to minimize requests to external sites and improve load times.
*
* @package ug-tabs-chords
*/

defined( 'ABSPATH' ) or die( 'Access Denied!' );

if ( ! class_exists( 'UGCache' ) ) {

  /**
  * Class UGCache is responsible for providing an interface to WP_Transient
  * API for caching data.
  *
  * @package ug-tabs-chords
  * @version  0.0.1
  * @since 0.0.1
  * @author Leo Toikka
  */
  class UGCache {

    /**
     * Adds a cached entry by using the WP Transient API.
     * @param string $entry_name The unique name to cache this entry with
     * @param string $entry_value The content to cache
     * @param integer $time The expiration time in seconds for the cache entry,
     * default is 86400 seconds (24 hours)
     * TODO: Add an option to wp-admin for the user to specify the cache time.
     */
    public static function addToCache( $entry_name, $entry_content, $time = 86400 ) {
      // Check if the entry is already cached and remove it
      if ( self::getCached( $entry_name ) !== false ) {
        self::removeFromCache( $entry_name );
      }

      $entry_clean = self::cacheKeyFormat( $entry_name );
      set_transient( 'ugtc_artist_entries_' . $entry_clean, $entry_content, $time );
    }

    /**
     * Gets the cached key if exists,
     *
     * @param string $entry_name The name of the entry to check
     * @return boolean The cached value for $entry_name, false if the no
     * cached value was found
     */
    public static function getCached( $entry_name ) {
      $entry_clean = self::cacheKeyFormat( $entry_name );
      $cached_entries = get_transient( 'ugtc_artist_entries_' . $entry_clean );

      if ( false === $cached_entries ) return false;
      return $cached_entries;
    }

    /**
     * Removes an entry from the cache.
     * @param  string $entry_name The unique name of the entry
     * @return bool True on success, false otherwise
     */
    public static function removeFromCache( $entry_name ) {
      $entry_clean = self::cacheKeyFormat( $entry_name );
      return delete_transient( $entry_clean );
    }

    /**
     * Deletes all the cached entries added by the plugin.
     */
    public static function purgeCache() {
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
     * Strip all whitespace and transform to lowercase with underscores to
     * obtain a more cacheable format for entry keys.
     * @param  string $to_format The string to format
     * @return string $formatted The formatted string
     */
    public static function cacheKeyFormat( $to_format ) {
      $formatted = strtolower( preg_replace( '/\s+/', '_', trim( $to_format ) ) );
      return $formatted;
    }
  }
}
