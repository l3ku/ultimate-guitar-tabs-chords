<?php
/**
* Plugin Name: Ultimate Guitar Tabs & Chords
* Plugin URI: https://github.com/l3ku/ultimate-guitar-tabs-chords
* Author: Leo Toikka & Antti Kymén
* Description: Fetches tabs and chords from Ultimate Guitar
* Author URI: https://github.com/l3ku
* Version: 0.0.1
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

require_once( plugin_dir_path( __FILE__ ) . 'includes/ug-shortcode.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/ug-settings.php' );

if ( ! class_exists( 'UG_Tabs_Chords' ) ) {

  /**
   * Class UG_Tabs_Chords
   *
   * @package ug-tabs-chords
   * @version  0.0.1
   * @since 0.0.1
   * @author Leo Toikka
   */
  class UG_Tabs_Chords {

    /**
     * Settings handler for registering and providing functionality to settings.
     * @var UG_Settings
     */
    private $ug_settings;

    /**
     * Shortcode registration and functionality.
     * @var UG_Shortcode
     */
    private $ug_shortcode;

    /**
    * Initialize the plugin.
    *
    * @since 0.0.1
    */
    public function __construct() {
      $this->ug_settings = new UG_Settings();
      $this->ug_shortcode = new UG_Shortcode();

      add_action( 'plugins_loaded', array( $this, 'load_text_domain' ) );
      add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
      add_action( 'admin_init', array( $this, 'register_settings' ) );
      add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
      add_action( 'init', array( $this, 'register_shortcode' ) );
      add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_settings_action_link' ) );
    }

    /**
    * Load plugin textdomain.
    *
    * @since 0.0.1
    */
    public function load_text_domain() {
      load_plugin_textdomain( 'ug-tabs-chords', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Register plugin settings.
     *
     * @since 0.0.1
     */
    public function register_settings() {
      $this->ug_settings->register_settings();
      $this->ug_settings->add_settings_sections();
    }

    /**
    * Register plugin shortcode.
    *
    * @since 0.0.1
    */
    public function register_shortcode() {
      $this->ug_shortcode->register_shortcode();
    }

    /**
     * Add settings link to plugin page meta box.
     *
     * @param array $links Already existing links
     * @return array Links including the added settings links
     * @since 0.0.1
     */
    public function add_settings_action_link( $links ) {
      $admin_link = admin_url( 'admin.php?page=ug_tabs_chords' );

      // Check if WordPress is a network installation
      if ( is_network_admin() ) {
        // TODO: Check that this actually works in WP Network Admin
        $admin_link = network_admin_url( 'admin.php?page=ug_tabs_chords' );
      }

      $settings_link = array( '<a href="' . $admin_link . '"">' . __( 'Settings' ) . '</a>' );
      $new_links = array_merge( $links, $settings_link );

      return $new_links;
    }

    /**
    * Enqueue plugin scripts and styles.
    *
    * @since 0.0.1
    */
    public function enqueue_scripts() {
      wp_enqueue_style( 'ug-tabs-chords', plugins_url( '/assets/css/ug-tabs-chords.css', __FILE__ ) );
    }

    /**
    * Add a settings page to WP Admin under "Settings" if the current user
    * has manage options capabilitiess.
    *
    * @since 0.0.1
    */
    public function add_admin_pages() {
      add_menu_page(
        __( 'UGTC', 'ug-tabs-chords' ),
        __( 'UGTC', 'ug-tabs-chords' ),
        'manage_options',
        'ug_tabs_chords',
        array( $this, 'create_main_page' ),
        'dashicons-album'
      );

      add_submenu_page(
        'ug_tabs_chords',
        __( 'UGTC Search Settings', 'ug-tabs-chords' ),
        __( 'Search Settings', 'ug-tabs-chords' ),
        'manage_options',
        'ug_tabs_chords_search_settings',
        array( $this, 'create_search_settings_page' )
      );
    }

    /**
    * Create the admin plugin main page.
    *
    * @since 0.0.1
    */
    public function create_main_page() {
      require_once( plugin_dir_path( __FILE__ ) . 'includes/templates/main-page.php' );
    }

    /**
    * Create the admin general settings page.
    *
    * @since 0.0.1
    */
    public function create_search_settings_page() {
      require_once( plugin_dir_path( __FILE__ ) . 'includes/templates/search-settings-page.php' );
    }
  }

  $ug_tabs_chords = new UG_Tabs_Chords();
}
