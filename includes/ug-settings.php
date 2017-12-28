<?php
/**
* File for registering settings.
*
* @package ug-tabs-chords
*/

defined( 'ABSPATH' ) or die( 'Access Denied!' );

require_once( plugin_dir_path( __FILE__ ) . '/ug-client.php' );

if ( ! class_exists( 'UGSettings' ) ) {

  /**
  * Class UGSettings
  *
  * @package ug-tabs-chords
  * @version  0.0.1
  * @since 0.0.1
  * @author Leo Toikka
  */
  class UGSettings {

    /**
     * Ultimate Guitar HTML client.
     * @var UGClient
     */
    private $ug_client;

    /**
     * Initialize the class, create a UGClient instance to get possible settings
     * values.
     */
    public function __construct() {
      $this->ug_client = new UGClient();
    }

    /**
    * Register plugin settings.
    *
    * @since 0.0.1
    */
    public function registerSettings() {
      register_setting( 'ugtc-search-settings-group', 'ugtc_search_entry_types' );
      register_setting( 'ugtc-search-settings-group', 'ugtc_search_entry_lengths' );
      register_setting( 'ugtc-search-settings-group', 'ugtc_search_sort_option' );
      register_setting( 'ugtc-search-settings-group', 'ugtc_search_ratings' );

      add_settings_section(
        'ugtc-search-settings-section',
        __( 'Search Settings', 'ug-tabs-chords' ),
        array( $this, 'searchSettingsDescription' ),
        'ug_tabs_chords_search_settings'
      );
      add_settings_field(
        'search-entry-types',
        __( 'Entry Types', 'ug-tabs-chords' ),
        array( $this, 'settingsSearchEntryTypes' ),
        'ug_tabs_chords_search_settings',
        'ugtc-search-settings-section'
      );
      add_settings_field(
        'search-entry-lengths',
        __( 'Entry Lengths', 'ug-tabs-chords' ),
        array( $this, 'settingsSearchEntryLengths' ),
        'ug_tabs_chords_search_settings',
        'ugtc-search-settings-section'
      );
      add_settings_field(
        'search-sort-option',
        __( 'Sorting Method', 'ug-tabs-chords' ),
        array( $this, 'settingsSearchSortOption' ),
        'ug_tabs_chords_search_settings',
        'ugtc-search-settings-section'
      );
      add_settings_field(
        'search-ratings',
        __( 'Allowed Ratings', 'ug-tabs-chords' ),
        array( $this, 'settingsSearchRatings' ),
        'ug_tabs_chords_search_settings',
        'ugtc-search-settings-section'
      );
    }

    /**
    * Add a description for the search settings page.
    *
    * @since 0.0.1
    */
    public function searchSettingsDescription() {
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
    public function settingsSearchEntryTypes() {
      $entry_types = get_option( 'ugtc_search_entry_types' );
      $possible_entry_types = $this->ug_client->getPossibleType1Values();

      // Use defaults if settings are empty
      if ( empty( $entry_types ) || false === $entry_types ) {
        $entry_types = $this->ug_client->getType1();
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
    public function settingsSearchEntryLengths() {
      $entry_lengths = get_option( 'ugtc_search_entry_lengths' );
      $possible_entry_lengths = $this->ug_client->getPossibleType2Values();

      // Use defaults if settings are empty
      if ( empty( $entry_lengths ) || false === $entry_lengths ) {
        $entry_lengths = $this->ug_client->getType2();
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
    public function settingsSearchSortOption() {
      $sort_option = get_option( 'ugtc_search_sort_option' );
      $possible_sort_options = $this->ug_client->getPossibleOrderValues();

      // Use defaults if settings are empty
      if ( empty( $sort_option ) || false === $sort_option ) {
        $sort_option = $this->ug_client->getOrder();
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
    public function settingsSearchRatings() {
      $ratings = get_option( 'ugtc_search_ratings' );
      $possible_ratings = $this->ug_client->getPossibleRatingValues();

      // Use defaults if settings are empty
      if ( empty( $ratings ) || false === $ratings ) {
        $ratings = $this->ug_client->getAllowedRatings();
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
