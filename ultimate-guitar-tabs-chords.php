<?php
/**
* Plugin Name: Ultimate Guitar Tabs & Chords
* Plugin URI: https://github.com/l3ku/ultimate-guitar-tabs-chords
* Author: Leo Toikka & Antti Kymén
* Description: Fetches tabs and chords from Ultimate Guitar
* Author URI: https://github.com/l3ku
* Version: 1.0.0
* Text Domain: ug-tabs-chords
* License: GPLv3
*
*
* Copyright 2017 Leo Toikka & Antti Kymén
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License, version 3, as
*   published by the Free Software Foundation.
*
*   This program is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
*   GNU General Public License for more details.

*   You should have received a copy of the GNU General Public License
*   along with this program; if not, write to the Free Software
*   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( 'ABSPATH' ) or die( 'Access Denied!' );

require_once( plugin_dir_path( __FILE__ ) . 'includes/ug-client.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/ug-shortcode.php' );

if ( ! class_exists( 'UGTabsChords' ) ) {

  /**
   * Class UGTabsChords
   *
   * @package ug-tabs-chords
   * @version  1.0.0
    * @since 1.0.0
    * @author Leo Toikka
   */
  class UGTabsChords {

    /* This class is used as a singleton. */
    private static $instance_;

    /* Ultimate Guitar client */
    private $ug_client;

    /**
    * Initialize the plugin.
    *
    * @since 1.0.0
    */
    public function __construct() {
      // Allow only one instance to exist at a time
      if ( isset( self::$instance_ ) ) {
        return;
      }
      self::$instance_ = $this;
      $this->ug_client = new UGClient();

      add_action( 'plugins_loaded', array( $this, 'loadTextdomain' ) );
      add_action( 'wp_enqueue_scripts', array( $this, 'enqueueScripts' ) );
      add_action( 'admin_init', array( $this, 'registerSettings' ) );
      add_action( 'admin_menu', array( $this, 'addAdminPages' ) );
      add_action( 'init', array( $this, 'registerShortcode' ) );
    }

    /**
    * Load plugin textdomain.
    *
    * @since 1.0.0
    */
    public function loadTextdomain() {
      load_plugin_textdomain( 'ug-tabs-chords', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
    * Enqueue plugin scripts and styles.
    *
    * @since 1.0.0
    */
    public function enqueueScripts() {
      // TODO: Add scripts if needed
      wp_enqueue_style( 'ug-tabs-chords', plugins_url( '/assets/css/ug-tabs-chords.css', __FILE__ ) );
    }

    /**
    * Add a settings page to WP Admin under "Settings" if the current user
    * has manage options capabilitiess.
    *
    * @since 1.0.0
    */
    public function addAdminPages() {
      add_menu_page(
        __( 'UGTC', 'ug-tabs-chords' ),
        __( 'UGTC', 'ug-tabs-chords' ),
        'manage_options',
        'ug_tabs_chords',
        array( $this, 'createInfoPage' ),
        'dashicons-album'
      );
      add_submenu_page(
        'ug_tabs_chords',
        __( 'UGTC Search Settings', 'ug-tabs-chords' ),
        __( 'Search Settings', 'ug-tabs-chords' ),
        'manage_options',
        'ug_tabs_chords_search_settings',
        array( $this, 'createSearchSettingsPage' )
      );
    }

    /**
    * Create the admin plugin info page.
    *
    * @since 1.0.0
    */
    public function createInfoPage() {
      require_once( plugin_dir_path( __FILE__ ) . 'includes/templates/info-page.php' );
    }

    /**
    * Create the admin general settings page.
    *
    * @since 1.0.0
    */
    public function createSearchSettingsPage() {
      require_once( plugin_dir_path( __FILE__ ) . 'includes/templates/search-settings-page.php' );
    }

    /**
    * Register plugin settings.
    *
    * @since 1.0.0
    */
    public function registerSettings() {
      register_setting( 'ugtc-search-settings-group', 'ugtc_search_entry_types' );
      register_setting( 'ugtc-search-settings-group', 'ugtc_search_entry_lengths' );
      register_setting( 'ugtc-search-settings-group', 'ugtc_search_sort_option' );
      register_setting( 'ugtc-search-settings-group', 'ugtc_search_ratings' );

      add_settings_section( 'ugtc-search-settings-section', __( 'Search Settings', 'ug-tabs-chords' ), array( $this, 'searchSettingsDescription' ), 'ug_tabs_chords_search_settings' );
      add_settings_field( 'search-entry-types', __( 'Entry Types', 'ug-tabs-chords' ), array( $this, 'settingsSearchEntryTypes' ), 'ug_tabs_chords_search_settings', 'ugtc-search-settings-section' );
      add_settings_field( 'search-entry-lengths', __( 'Entry Lengths', 'ug-tabs-chords' ), array( $this, 'settingsSearchEntryLengths' ), 'ug_tabs_chords_search_settings', 'ugtc-search-settings-section' );
      add_settings_field( 'search-sort-option', __( 'Sorting Method', 'ug-tabs-chords' ), array( $this, 'settingsSearchSortOption' ), 'ug_tabs_chords_search_settings', 'ugtc-search-settings-section' );
      add_settings_field( 'search-ratings', __( 'Allowed Ratings', 'ug-tabs-chords' ), array( $this, 'settingsSearchRatings' ), 'ug_tabs_chords_search_settings', 'ugtc-search-settings-section' );
    }

    /**
    * Add a description for the search settings page.
    *
    * @since 1.0.0
    */
    public function searchSettingsDescription() {
      echo '<p>' . __( 'Change the content search settings for Ultimate Guitar Tabs & Chords.', 'ug-tabs-chords' ) . '</p>';
    }

    /**
    * Create appearance for displaying the artist list setting.
    *
    * @since 1.0.0
    */
    public function settingsArtistList() {
      $artists = get_option( 'ugtc_artist_list' );
      ?>
      <div class="ugtc-artist-list">
        <input type="text" name="ugtc_artist_list" value="<?php if ( ! empty( $artists ) ): echo $artists; endif; ?>">
      </div>
      <?php
    }

    /**
    * Create settings field for search entry types.
    *
    * @since 1.0.0
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
    * @since 1.0.0
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
    * @since 1.0.0
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
    * @since 1.0.0
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

    /**
    * Register plugin shortcode.
    *
    * @since 1.0.0
    */
    public function registerShortcode() {
      add_shortcode( 'ug-tabs-chords', function( $atts = [], $content = null ) {
        echo createShortCode( $this->ug_client, $atts, $content );
      } );
    }
  }
  $ugtabschords = new UGTabsChords();
}
