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

require_once( plugin_dir_path( __FILE__ ) . '/vendor/autoload.php' );

if ( ! class_exists( 'UGTabsChords' ) ) {
  class UGTabsChords {

    /* This class is used as a singleton. */
    private static $instance_;

    /* Text-domain name */
    private static $textDomain_ = 'ug-tabs-chords';

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

      add_action( 'plugins_loaded', array( $this, 'loadTextdomain' ) );
    }

    /**
    * Load plugin textdomain.
    *
    * @since 1.0.0
    */
    public function loadTextdomain() {
      load_plugin_textdomain( self::$textDomain_, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
    * Load plugin modules
    *
    * @since 1.0.0
    */
    public function loadModules() {
      // @TODO
    }

  }

$ugtabschords = new UGTabsChords();

}
