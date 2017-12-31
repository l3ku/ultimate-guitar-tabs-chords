<?php
/**
* Shortcode file for displaying Ultimate Guitar Tabs & Chords frontend data and
* generating the shortcode in WP-Admin.
*
* @package ug-tabs-chords
*/

defined( 'ABSPATH' ) or die( 'Access Denied!' );

require_once( plugin_dir_path( __FILE__ ) . '/ug-cache.php' );

if ( ! class_exists( 'UGShortcode' ) ) {

  /**
  * Class UGShortcode
  *
  * @package ug-tabs-chords
  * @version  0.0.1
  * @since 0.0.1
  * @author Leo Toikka
  */
  class UGShortcode {

    /**
     * The name of the plugin shortcode.
     * @var string
     */
    private static $ug_shortcode_str = 'ug-tabs-chords';

    /**
     * Ultimate Guitar HTML client.
     * @var UGClient
     */
    private $ug_client;

    /**
     * Initialize the class.
     */
    public function __construct() {
      $this->ug_client = new UGClient();
    }

    /**
    * Register plugin shortcode.
    *
    * @since 0.0.1
    */
    public function registerShortcode() {
      add_shortcode(
        self::$ug_shortcode_str,
        function( $atts = [], $content = null ) {
          echo $this->createShortcode( $atts, $content );
        }
      );
    }

    /**
     * Generate the required shortcode string from parameters.
     * @param string $artist The artist to include in the shortcode, can not
     * be empty string
     * @param mixed $limit The entry limit for artist results
     * @return mixed Shortcode string on success, WP_Error object in case of an
     * error.
     */
    public static function generateShortcode( $artist, $limit ) {
      $errors = array();

      // Artist is required
      if ( empty( $artist ) ) {

        return new WP_Error(
          'no-artist',
          __( 'No artist specified.', 'ug-tabs-chords' )
        );

      }

      $shortcode_artist = sprintf( 'artist="%s"', trim( wp_kses_data( $artist ) ) );

      // Check if the limit is set (not required)
      $shortcode_limit = '';
      if ( ! empty( $limit ) ) {
        $shortcode_limit = sprintf( ' limit="%s"', trim( wp_kses_data( $limit ) ) );
      }

      return sprintf(
        '[%s %s%s]',
        self::$ug_shortcode_str,
        $shortcode_artist,
        $shortcode_limit
      );

    }

    /**
     * Display shortcode functionality in the frontend side.
     * @param array $atts Shortcode attributes
     * @param string $content Shortcode content
     * @return string HTML content from Ultimate Guitar or WP_Error if the
     * shortcode has not been registered
     */
    public function createShortcode( $atts, $content ) {
      // Check that the shortcode exists
      if ( ! shortcode_exists( 'ug-tabs-chords' ) ) {

        return new WP_Error(
          'ugtc_shortcode_not_registered',
          sprintf(
            __( 'Ultimate-Guitar Tabs & Chords: error with registering shortcode %s' ),
            self::$ug_shortcode_str
          )
        );

      }

      // Normalize attribute keys to lowercase and search artists
      $atts = array_change_key_case( ( array ) $atts, CASE_LOWER );
      if ( ! isset( $atts['artist'] ) || empty( $atts['artist'] ) ) {
        return;
      }
      $artist = trim( $atts['artist'] );

      // Attempt to get artist entries from cache, use UGClient if necessary.
      $results = UGCache::getCached( $artist );
      if ( false === $results ) {
        $this->setSearchSettings( $this->ug_client );
        $results = $this->ug_client->search( $artist );

        // Only add to cache if any data is retreived
        if ( ! empty( $results ) ) UGCache::addToCache( $artist, $results );
      }

      // Get limit attribute if it is set
      if ( isset( $atts['limit'] ) && ! empty( $atts['limit'] ) ) {
        if ( is_numeric( $atts['limit'] ) ) {
          $limit = intval( wp_kses_data( $atts['limit'] ) );
        }
      }

      // Create a display div from artists' data
      $artist_div = '<h2 class="ugtc-single-artist-name">' . $artist . '</h2>';
      $artist_div .= '<div class="ugtc-single-artist-entries">';

      // Create HTML table containing the results
      // TODO: Provide functionality to choose display layout type
      $artist_table_html = $this->shortCodeHtmlTable( $results, $limit );
      $artist_div .= $artist_table_html . '</div>';

      return $artist_div;
    }

    /**
    * Set search settings for Ultimate Guitar Client based on the user specified
    * settings.
    */
    private function setSearchSettings() {
      $search_entry_types = get_option( 'ugtc_search_entry_types' );
      if ( ! empty( $search_entry_types ) ) {
        $this->ug_client->setType1( $search_entry_types );
      }

      $search_entry_lengths = get_option( 'ugtc_search_entry_lengths' );
      if ( ! empty( $search_entry_lengths ) ) {
        $this->ug_client->setType2( $search_entry_lengths );
      }

      $search_sort_option = get_option( 'ugtc_search_sort_option' );
      if ( ! empty( $search_sort_option ) ) {
        $this->ug_client->setOrder( $search_sort_option );
      }

      $search_ratings = get_option( 'ugtc_search_ratings' );
      if ( ! empty( $search_ratings ) ) {
        $this->ug_client->setAllowedRatings( $search_ratings );
      }
    }

    /**
     * Create a HTML table from artist search results.
     * @param  array $results_array Contains the artsit search results as array
     * @param int $limit The limit of results to show
     * @return string The HTML table equivalent of the artist search results
     */
    private function shortCodeHtmlTable( $results_array, $limit = 1000 ) {
      $artist_table = '<table>';

      // Include table head for labels
      $artist_table .= '<thead><tr>';
      $artist_table .= '<th>' . __( 'Name', 'ug-tabs-chords' ) . '</th>';
      $artist_table .= '<th>' . __( 'Type', 'ug-tabs-chords' ) . '</th>';
      $artist_table .= '<th>' . __( 'Rating', 'ug-tabs-chords' ) . '</th>';
      $artist_table .= '</tr></thead><tbody>';

      if ( ! empty( $results_array ) ) {

        for ( $i = 0; $i < sizeof( $results_array ); $i++ ) {
          if ( $i >= $limit ) {
            break;
          }
          $result = $results_array[$i];

          $single_entry_row = '<tr class="ugtc-single-entry-row">';
          $entry_name = '<td class="ugtc-single-entry-name"><a class="ugtc-single-entry ugtc-single-entry-link" href="' . $result['link'] . '">' . $result['name'] . '</a></td>';
          $entry_type = '<td class="ugtc-single-entry-type">' . $result['type'] . '</td>';
          $entry_rating = '<td class="ugtc-single-entry-rating">' . str_repeat( '&#9733;', $result['rating'] ) . '</td>';

          $single_entry_row .= $entry_name;
          $single_entry_row .= $entry_type;
          $single_entry_row .= $entry_rating;
          $single_entry_row .= '</tr>';

          $artist_table .= $single_entry_row;
        }
      }

      $artist_table .= '</tbody></table>';
      return $artist_table;
    }
  }
}
