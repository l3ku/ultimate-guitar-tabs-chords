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

namespace UGTC;

defined( 'ABSPATH' ) or die( 'Access Denied!' );

require_once( plugin_dir_path( __FILE__ ) . 'includes/shortcode/ug-shortcode.php' );

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
     * All shortcode objects in an array.
     * @var array UG_Shortcode_Base
     */
    private $ug_shortcodes = array();

    /**
    * Initialize the plugin.
    *
    * @since 0.0.1
    */
    public function __construct() {
      $this->ug_shortcodes[] = new Shortcode\UG_Shortcode();

      add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
      add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
      add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
      add_action( 'init', array( $this, 'register_shortcodes' ) );
      add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_settings_action_link' ) );
    }

    /**
    * Load plugin textdomain.
    *
    * @since 0.0.1
    */
    public function load_textdomain() {
      load_plugin_textdomain( 'ug-tabs-chords', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
    * Register plugin shortcodes.
    *
    * @since 0.0.1
    */
    public function register_shortcodes() {
      if ( ! empty( $this->ug_shortcodes ) ) {

        foreach ( $this->ug_shortcodes as $shortcode ) {
          $shortcode->register_shortcode();
        }
      }
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
      add_options_page(
        __( 'Ultimate Guitar Tabs & Chords', 'ug-tabs-chords' ),
        __( 'UG Tabs & Chords', 'ug-tabs-chords' ),
        'manage_options',
        'ug_tabs_chords',
        array( $this, 'create_main_page' )
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
  }

  $ug_tabs_chords = new UG_Tabs_Chords();
}
