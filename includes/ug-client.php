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

require_once plugin_dir_path( __FILE__ ) . 'ug-client-values.php';
require_once plugin_dir_path( __FILE__ ) . 'ug-api.php';

use \WP_Error;

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
      $this->ug_api = new API\UG_API();
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
      $this->ratings = array( 1, 2, 3, 4, 5 );
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
      if ( ! in_array( $type, array_keys( ugtc_get_valid_types() ), true ) ) {
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
     * Set the order for the retreived search results.
     *
     * @param string $order The order to set.
     * --- POSSIBLE VALUES ---
     *   'title_srt' = Sort by title ABC
     *   'date_srt' = Sort by date
     * @return mixed True if the operation succeeded, or WP_ERROR object.
     */
    public function set_order( $order ) {
      if ( ! in_array( $order, array_keys( ugtc_get_valid_orders() ), true ) ) {
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
     * Set the allowed ratings for the search.
     *
     * @param array $ratings The rating values (1-5) to set.
     */
    public function set_allowed_ratings( $ratings ) {
      if ( array_intersect( $ratings, ugtc_get_valid_ratings() ) !== $ratings ) {
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

      return $this->ug_api->get_tabs_archive( $content_params, $this->limit );
    }
  }
}
