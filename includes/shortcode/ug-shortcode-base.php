<?php
/**
* Interface for plugin shortcode files.
*
* @package ug-tabs-chords
*/

namespace UGTC\Shortcode;

defined( 'ABSPATH' ) or die( 'Access Denied!' ); // Prevent direct access

if ( ! class_exists( 'UG_Shortcode_Base' ) ) {

  /**
  * Interface UG_Shortcode_Base Defines the interface for all plugin shortcodes.
  *
  * @package ug-tabs-chords
  * @version  0.0.1
  * @since 0.0.1
  * @author Leo Toikka
  */
  interface UG_Shortcode_Base {

    /**
    * Register plugin shortcode.
    *
    * @since 0.0.1
    */
    public function register_shortcode();

    /**
     * Return the valid shortcode attributes/keys in a whitelist array.
     *
     * @return array string The whitelist of valid shortcode attributes/keys.
     */
    public static function get_shortcode_whitelist();

    /**
     * Generate the required shortcode string from parameters.
     *
     * @param array mixed $attributes Array of attributes to specify content:
     * 'artist' (required), 'type' (required), 'order' (optional), 'limit' (optional)
     * @return mixed Shortcode string on success, WP_Error object in case of an
     * error.
     */
    public static function generate_shortcode( $attributes );

    /**
     * Display shortcode functionality in the frontend side.
     *
     * @param array $atts Shortcode attributes
     * @param string $content Shortcode content
     * @param string $html_strategy The output strategy used to generate HTML. Default
     * value is 'table', currently no other format is supported.
     * @return string HTML content from Ultimate Guitar or WP_Error if the
     * shortcode has not been registered
     */
    public function create_shortcode( $atts, $content, $html_strategy = 'table' );
  }
}
