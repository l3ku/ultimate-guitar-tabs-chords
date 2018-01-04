<?php
/**
* File for registering settings.
*
* @package ug-tabs-chords
*/

defined( 'ABSPATH' ) or die( 'Access Denied!' );

require_once( plugin_dir_path( __FILE__ ) . '/ug-client.php' );

if ( ! class_exists( 'UG_Settings' ) ) {

  /**
  * Class UG_Settings
  *
  * @package ug-tabs-chords
  * @version  0.0.1
  * @since 0.0.1
  * @author Leo Toikka
  */
  class UG_Settings {

    /**
     * Ultimate Guitar HTML client.
     * @var UG_Client
     */
    private $ug_client;

    /**
     * Initialize the class, create a UG_Client instance to get possible settings
     * values.
     */
    public function __construct() {
      $this->ug_client = new UG_Client();
    }

    /**
    * Register plugin settings.
    *
    * @since 0.0.1
    */
    public function register_settings() {
      register_setting( 'ugtc-search-settings-group', 'ugtc_search_entry_types' );
      register_setting( 'ugtc-search-settings-group', 'ugtc_search_entry_lengths' );
      register_setting( 'ugtc-search-settings-group', 'ugtc_search_sort_option' );
      register_setting( 'ugtc-search-settings-group', 'ugtc_search_ratings' );
    }

    /**
     * Add the plugin settings sections along with their corresponding settings
     * fields.
     *
     * @since 0.0.1
     */
    public function add_settings_sections() {
      add_settings_section(
        'ugtc-search-settings-section',
        __( 'Search Settings', 'ug-tabs-chords' ),
        array( $this, 'search_settings_description' ),
        'ug_tabs_chords_search_settings'
      );
      add_settings_field(
        'search-entry-types',
        __( 'Entry Types', 'ug-tabs-chords' ),
        array( $this, 'settings_search_entry_types' ),
        'ug_tabs_chords_search_settings',
        'ugtc-search-settings-section'
      );
      add_settings_field(
        'search-entry-lengths',
        __( 'Entry Lengths', 'ug-tabs-chords' ),
        array( $this, 'settings_search_entry_lengths' ),
        'ug_tabs_chords_search_settings',
        'ugtc-search-settings-section'
      );
      add_settings_field(
        'search-sort-option',
        __( 'Sorting Method', 'ug-tabs-chords' ),
        array( $this, 'settings_search_sort_option' ),
        'ug_tabs_chords_search_settings',
        'ugtc-search-settings-section'
      );
      add_settings_field(
        'search-ratings',
        __( 'Allowed Ratings', 'ug-tabs-chords' ),
        array( $this, 'settings_search_ratings' ),
        'ug_tabs_chords_search_settings',
        'ugtc-search-settings-section'
      );
    }

    /**
    * Add a description for the search settings page.
    *
    * @since 0.0.1
    */
    public function search_settings_description() {
      echo wp_sprintf(
        __(
          '%sChange the content search settings for Ultimate Guitar Tabs & Chords. %sHint: press ctrl/cmd to select multiple values.%s',
          'ug-tabs-chords'
        ),
        '<p>',
        '<br><small>',
        '</small></p>'
      );
    }

    /**
    * Create settings field for search entry types.
    *
    * @since 0.0.1
    */
    public function settings_search_entry_types() {
      $entry_types = get_option( 'ugtc_search_entry_types' );
      $possible_entry_types = $this->ug_client->get_possible_type_1_values();

      // Use defaults if settings are empty
      if ( empty( $entry_types ) || false === $entry_types ) {
        $entry_types = $this->ug_client->get_type_1();
      }

      echo '<select name="ugtc_search_entry_types[]" multiple>';
      foreach ( $possible_entry_types as $key => $value ) {
        echo '<option value="' . $key . '" ';
        if ( ! empty( $entry_types ) && in_array( $key, $entry_types ) ) {
          echo 'selected';
        }
        echo '>' . $value . '</option>';
      }
      echo '</select>';
    }

    /**
    * Create settings field for search entry lengths.
    *
    * @since 0.0.1
    */
    public function settings_search_entry_lengths() {
      $entry_lengths = get_option( 'ugtc_search_entry_lengths' );
      $possible_entry_lengths = $this->ug_client->get_possible_type_2_values();

      // Use defaults if settings are empty
      if ( empty( $entry_lengths ) || false === $entry_lengths ) {
        $entry_lengths = $this->ug_client->get_type_2();
      }

      echo '<select name="ugtc_search_entry_lengths[]" multiple>';
      foreach ( $possible_entry_lengths as $key => $value ) {
        echo '<option value="' . $key . '" ';
        if ( ! empty( $entry_lengths ) && in_array( $key, $entry_lengths ) ) {
          echo 'selected';
        }
        echo '>' . $value . '</option>';
      }
      echo '</select>';
    }

    /**
    * Create settings field for search sort option.
    *
    * @since 0.0.1
    */
    public function settings_search_sort_option() {
      $sort_option = get_option( 'ugtc_search_sort_option' );
      $possible_sort_options = $this->ug_client->get_possible_order_values();

      // Use defaults if settings are empty
      if ( empty( $sort_option ) || false === $sort_option ) {
        $sort_option = $this->ug_client->get_order();
      }

      echo '<select name="ugtc_search_sort_option">';
      foreach ( $possible_sort_options as $key => $value ) {
        echo '<option value="' . $key . '" ';
        if ( ! empty( $sort_option ) && $key === $sort_option ) {
          echo 'selected';
        }
        echo '>' . $value . '</option>';
      }
      echo '</select>';
    }

    /**
    * Create settings field for search ratings.
    *
    * @since 0.0.1
    */
    public function settings_search_ratings() {
      $ratings = get_option( 'ugtc_search_ratings' );
      $possible_ratings = $this->ug_client->get_possible_rating_values();

      // Use defaults if settings are empty
      if ( empty( $ratings ) || false === $ratings ) {
        $ratings = $this->ug_client->get_allowed_ratings();
      }

      echo '<select name="ugtc_search_ratings[]" multiple>';
      foreach ( $possible_ratings as $rating ) {
        echo '<option value="' . $rating . '" ';
        if ( ! empty( $ratings ) && in_array( $rating, $ratings ) ) {
          echo 'selected';
        }
        echo '>' . str_repeat( '&#9733;', $rating ) . '</option>';
      }
      echo '</select>';
    }
  }
}
