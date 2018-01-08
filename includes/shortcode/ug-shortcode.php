<?php
/**
* Shortcode file for displaying Ultimate Guitar Tabs & Chords frontend data and
* generating the shortcode in WP-Admin.
*
* @package ug-tabs-chords
*/

namespace UGTC\Shortcode;

defined( 'ABSPATH' ) or die( 'Access Denied!' ); // Prevent direct access

require_once( plugin_dir_path( __FILE__ ) . 'ug-shortcode-base.php' );
require_once( plugin_dir_path( __FILE__ ) . '../cache/ug-cache.php' );
require_once( plugin_dir_path( __FILE__ ) . '../client/ug-client.php' );

use \UGTC\Cache\UG_Cache;
use \WP_Error;

if ( ! class_exists( 'UG_Shortcode' ) ) {

  /**
  * Class UG_Shortcode
  *
  * @package ug-tabs-chords
  * @version  0.0.1
  * @since 0.0.1
  * @author Leo Toikka
  */
  class UG_Shortcode implements UG_Shortcode_Base {

    /**
     * The name of the plugin shortcode.
     * @var string
     */
    private static $ug_shortcode_str = 'ug-tabs-chords';

    /**
     * Used to check that the provided shortcode values are correct.
     * @var array string
     */
    private static $shortcode_whitelist = array( 'artist', 'type', 'order', 'limit' );

    /**
     * Ultimate Guitar HTML client.
     * @var UG_Client
     */
    private $ug_client;

    /**
     * Initialize the class.
     */
    public function __construct() {
      $this->ug_client = new \UGTC\Client\UG_Client();
    }

    /**
    * Register plugin shortcode.
    *
    * @since 0.0.1
    */
    public function register_shortcode() {
      add_shortcode(
        self::$ug_shortcode_str,
        function( $atts = [], $content = null ) {
          echo $this->create_shortcode( $atts, $content );
        }
      );
    }

    /**
     * Return the valid shortcode attributes/keys in a whitelist array.
     *
     * @return array string The whitelist of valid shortcode attributes/keys.
     */
    public static function get_shortcode_whitelist() {
      return self::$shortcode_whitelist;
    }

    /**
     * Generate the required shortcode string from parameters.
     *
     * @param array mixed $attributes Array of attributes to specify content:
     * 'artist' (required), 'type' (required), 'order' (optional), 'limit' (optional)
     * @return mixed Shortcode string on success, WP_Error object in case of an
     * error.
     */
    public static function generate_shortcode( $attributes ) {
      // Artist and content type are required
      if ( ! isset( $attributes['artist'] ) || empty( $attributes['artist'] ) ) {
        return new WP_Error(
          'no-artist',
          __( 'No artist specified.', 'ug-tabs-chords' )
        );
      }
      if ( ! isset( $attributes['type'] ) || empty( $attributes['type'] ) ) {
        return new WP_Error(
          'no-type',
          __( 'No type specified.', 'ug-tabs-chords' )
        );
      }

      // Construct shortcode based on provided attributes, check from whitelist
      // that the values are valid
      $shortcode_body = '[ug-tabs-chords';
      if ( ! empty( $attributes ) ) {

        foreach ( $attributes as $key => $val ) {
          $sanitized_key = trim( wp_kses_data( $key ) );
          $sanitized_value = trim( wp_kses_data( $val ) );

          if ( in_array( $sanitized_key, self::$shortcode_whitelist ) && ! empty( $sanitized_value ) ) {
            $shortcode_body .= sprintf( ' %s="%s"', $sanitized_key, $sanitized_value );
          }
        }
      }
      $shortcode_body .= ']';
      return $shortcode_body;
    }

    /**
     * Display shortcode functionality in the frontend side.
     *
     * @param array $atts Shortcode attributes
     * @param string $content Shortcode content
     * @param string $html_strategy The output strategy used to generate HTML. Default
     * value is 'table', currently no other format is supported
     * @return string HTML content from Ultimate Guitar or WP_Error in case of
     * an error
     */
    public function create_shortcode( $atts, $content, $html_strategy = 'table' ) {
      // Check that the shortcode exists
      if ( ! shortcode_exists( 'ug-tabs-chords' ) ) {

        return new WP_Error(
          'ugtc_shortcode_not_registered',
          sprintf(
            __( 'Ultimate-Guitar Tabs & Chords: error with registering shortcode "%s"', 'ug-tabs-chords' ),
            self::$ug_shortcode_str
          )
        );

      }

      // Normalize attribute keys to lowercase
      $atts = array_change_key_case( (array) $atts, CASE_LOWER );
      if ( ! ( isset( $atts['artist'] ) && isset( $atts['type'] ) ) ) {
        return;
      }

      // Use only whitelisted values
      foreach ( $atts as $key => $val ) {
        if ( ! in_array( $key, self::$shortcode_whitelist ) ) {
          unset( $atts[$key] );
        }
      }

      // Attempt to get artist entries from cache, use UG_Client if necessary.
      $results = UG_Cache::get_cached( $atts );
      if ( false === $results ) {
        $this->set_client_settings( $atts );
        $results = $this->ug_client->get_content();

        // Only add to cache if any data is retreived
        if ( ! empty( $results ) ) {
          UG_Cache::add_to_cache( $atts, $results );
        }
      }

      // Create a display div from artists' data
      $artist_div = '<h2 class="ugtc-single-artist-name">' . $atts['artist'] . '</h2>';
      $artist_div .= '<div class="ugtc-single-artist-entries">';

      // Create HTML table containing the results
      // TODO: Provide functionality to choose display layout type
      if ( 'table' ===  $html_strategy ) {
        $artist_table_html = $this->shortcode_html_table( $results );

      } else {
        // Return WP_Error on unknow strategy
        return new WP_Error(
          'ugtc_shortcode_unknown_html_strategy',
          sprintf(
            __( 'Ultimate-Guitar Tabs & Chords: unknown shortcode HTML strategy "%s"', 'ug-tabs-chords' ),
            $strategy
          )
        );
      }

      $artist_div .= $artist_table_html . '</div>';
      return $artist_div;
    }

    /**
    * Set settings for Ultimate Guitar Client based on the user specified
    * settings.
    */
    private function set_client_settings( $atts ) {
      if ( isset( $atts['artist'] ) && ! empty( $atts['artist'] ) ) {
        $this->ug_client->set_artist( $atts['artist'] );
      }

      if ( isset( $atts['type'] ) && ! empty( $atts['type'] ) ) {
        $this->ug_client->set_type( $atts['type'] );
      }

      if ( isset( $atts['order'] ) && ! empty( $atts['order'] ) ) {
        $this->ug_client->set_order( $atts['order'] );
      }

      // Get limit attribute if it is set
      if ( isset( $atts['limit'] ) && ! empty( $atts['limit'] ) ) {
        if ( is_numeric( $atts['limit'] ) ) {
          $limit = intval( wp_kses_data( $atts['limit'] ) );
          $this->ug_client->set_limit( $limit );
        }
      }
    }

    /**
     * Create a HTML table from artist search results.
     * @param  array $results_array Contains the artsit search results as array
     * @return string The HTML table equivalent of the artist search results
     */
    private function shortcode_html_table( $results_array ) {
      $artist_table = '<table>';

      // Include table head for labels
      $artist_table .= '<thead><tr>';
      $artist_table .= '<th>' . __( 'Name', 'ug-tabs-chords' ) . '</th>';
      $artist_table .= '<th>' . __( 'Type', 'ug-tabs-chords' ) . '</th>';
      $artist_table .= '<th>' . __( 'Rating', 'ug-tabs-chords' ) . '</th>';
      $artist_table .= '</tr></thead><tbody>';

      if ( ! empty( $results_array ) ) {

        foreach ( $results_array as $result ) {

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
