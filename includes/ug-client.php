<?php
/**
 * Ultimate Guitar client file for retreiving scraped HTML.
 *
 * @package ug-tabs-chords
 */

namespace UGTC\Client;

if ( ! defined( 'ABSPATH' ) ) {
  die( 'Access Denied!' );
}

require_once plugin_dir_path( __FILE__ ) . 'ug-api.php';
require_once plugin_dir_path( __FILE__ ) . 'ug-cache.php';
require_once plugin_dir_path( __FILE__ ) . 'ug-db.php';

use \WP_Error;
use \UGTC\DB\UG_DB;
use \UGTC\API\UG_API;
use \UGTC\Cache\UG_Cache;

if ( ! class_exists( 'UG_Client' ) ) {

  /**
   * Class UG_Client
   *
   * @package ug-tabs-chords
   * @version  0.0.1
   * @since 0.0.1
   * @author Leo Toikka
   */
  class UG_Client {

    /* UG_API provides an interface to ultimate-guitar.com */
    private $ug_api;

    /* UG_DB provides an interface to the local database. */
    private $ug_db;

    /* Content specifiers */
    private $artist;
    private $limit;
    private $type;
    private $ratings;
    private $order;

    /**
     * Initialize the class.
     *
     * @since 0.0.1
     */
    public function __construct() {
      $this->ug_api = new UG_API();
      $this->ug_db = new UG_DB();
      $this->set_default_params(); // Set defaults
    }

    /**
     * Resets the current client search parameter values to the default ones.
     * type: Tabs
     * ratings: 1-5
     * order: Title ABC
     */
    public function set_default_params() {
      $this->artist  = 'Dream Theater'; // JP is god.
      $this->type    = 'tabs';
      $this->ratings = self::get_valid_ratings();
      $this->order   = 'title_srt';
      $this->limit   = 1000;
    }

    /**
     * Set the artist to get content from.
     * @param string $artist Artist
     */
    public function set_artist( $artist ) {
      $this->artist = $artist;
    }

    /**
     * Set the limit for content results.
     * @param int $limit The limit for results
     */
    public function set_limit( $limit ) {
      $this->limit = $limit;
    }

    /**
     * Set the content type.
     *
     * @param string $type1 The type value to set.
     * --- POSSIBLE VALUES ---
     *  'tabs', chords', 'guitar_pro', 'power', 'bass', 'drums', 'ukulele',
     *  'official' or 'video'.
     * @return mixed True if the operation succeeded, or WP_ERROR object.
     */
    public function set_type( $type ) {
      if ( ! in_array( $type, array_keys( self::get_valid_types() ), true ) ) {
        return new WP_Error( 'invalid_type_value', __( 'Invalid value supplied for content type', 'ug-tabs-chords' ) );
      }
      $this->type = $type;
      return true;
    }

    /**
     * Get the type values for content results.
     *
     * @return string The content type
     */
    public function get_type() {
      return $this->type;
    }

    /**
     * Get valid content type values.
     *
     * @return array string Array with type valid slugs as keys, and name as values.
     */
    public static function get_valid_types() {
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
     * Set the order for the retreived search results.
     *
     * @param string $order The order to set.
     * --- POSSIBLE VALUES ---
     *   'title_srt' = Sort by title ABC
     *   'date_srt' = Sort by date
     * @return mixed True if the operation succeeded, or WP_ERROR object.
     */
    public function set_order( $order ) {
      if ( ! in_array( $order, array_keys( self::get_valid_orders() ), true ) ) {
        return new WP_Error( 'invalid_order_value', __( 'Invalid value supplied for order search parameter', 'ug-tabs-chords' ) );
      }
      $this->order = $order;
      return true;
    }

    /**
     * Get the order for search results.
     *
     * @return string The order for search results
     */
    public function get_order() {
      return $this->order;
    }

    /**
     * Get valid order values.
     * @return array string Array with order valid slug as key, and name as value.
     */
    public static function get_valid_orders() {
      return array(
        'title_srt' => __( 'Title ABC', 'ug-tabs-chords' ),
        'date'      => __( 'Date', 'ug-tabs-chords' ),
      );
    }

    /**
     * Set the allowed ratings for the search.
     *
     * @param array $ratings The rating values (1-5) to set.
     */
    public function set_allowed_ratings( $ratings ) {
      if ( array_intersect( $ratings, self::get_valid_ratings() ) !== $ratings ) {
        return new WP_Error( 'invalid_rating_value', __( 'Invalid value supplied for ratings search parameter', 'ug-tabs-chords' ) );
      }
      $this->ratings = $ratings;
      return true;
    }

    /**
     * Get the allowed ratings for the search
     *
     * @return string The ratings for search results
     */
    public function get_allowed_ratings() {
      return $this->ratings;
    }

    /**
     * Get valid rating values (1-5).
     *
     * @return array int Valid rating values
     */
    public static function get_valid_ratings() {
      return array( 1, 2, 3, 4, 5 );
    }

    /**
     * Get artist content with the predefined parameters type, order, rating.
     *
     * @return array (mixed) An array containing the results.
     * Array keys: 'name', 'type', 'link', 'rating'
     */
    public function get_content() {
      $content_params = array(
        'artist' => $this->artist,
        'type'   => $this->type,
        'order'  => $this->order,
        'rating' => $this->ratings,
      );

      // Try to fetch from our cache first
      $cache_result = UG_Cache::get_cached($content_params);
      if ( $cache_result && ! empty( $cache_result) ) {
        return $cache_result;
      }

      // If the cache was missed, attempt to get the content from the local database
      $db_result = $this->ug_db->get_artist_entries($this->artist, $this->type, $this->order, $this->ratings);
      if ( ! empty($db_result) ) {
        return $db_result;
      }

      // Finally, resort to the www.ultimate-guitar.com API class that will scrape HTML to retreive
      // the tabs.
      $api_result = $this->ug_api->get_tabs_archive( $content_params );
      if ( empty($api_result) ) {
        throw New Exception(__('Could not find any entries for the provided artist.', 'ug-tabs-chords'));
      }

      // Add the content to the local databse and cache it
      $added_to_db = $this->ug_db->add_entries($api_result);
      if ( ! $added_to_db ) {
        throw new Execption(__('Could not save artist entries to local database. Please check that the WordPress database is up and running...', 'ug-tabs-chords'));
      }

      $added_to_cache = UG_Cache::add_to_cache($api_result);
      return $api_result;
    }
  }
}
