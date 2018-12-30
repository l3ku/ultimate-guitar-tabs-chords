<?php
/**
 * File for entering the ultimate-guitar.com tab results to the local database to avoid lookups.
 *
 * @package ug-tabs-chords
 */

namespace UGTC\DB;

if ( ! defined( 'ABSPATH' ) ) {
  die( 'Access Denied!' );
}

if ( ! class_exists( 'UG_DB' ) ) {

  /**
   * Class UG_DB is responsible for providing an interface to the WP database.
   *
   * @package ug-tabs-chords
   * @version  0.0.1
   * @since 0.0.1
   * @author Leo Toikka
   */
  class UG_DB {

    /**
     * The name of the artists table. The artist table contains information about all the artists
     * that are in use with the plugin.
     * @var string
     */
    private $artists_table = 'ugtc_artists';
    private $artists_schema = '
      id bigint(20) unsigned NOT NULL auto_increment,
      name varchar(255) NOT NULL UNIQUE,
      ug_id bigint(20) unsigned default NULL,
      link varchar(255) NOT NULL,
      PRIMARY KEY (id)
    ';

    /**
     * The name of the entries table. The entries table contains all entries of all artists.
     * @TODO: check that this table does not become too bloated due to the large amount of entries.
     * @var string
     */
    private $entries_table = 'ugtc_entries';
    private $entries_schema = '
      id bigint(20) unsigned NOT NULL auto_increment,
      artist_id bigint(20) unsigned NOT NULL,
      type varchar(255) NOT NULL,
      name varchar(255) NOT NULL,
      link varchar(255) NOT NULL,
      date bigint(20) NOT NULL,
      rating int default NULL,
      tuning varchar(255) default NULL,
      PRIMARY KEY (id),
      FOREIGN KEY (artist_id) REFERENCES ugtc_artists(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE
    ';


    /**
     * Class constructor. Creates the tables if not present.
     */
    public function __construct() {
      $this->create_tables();
    }


    public function add_artist( $name, $link, $ug_id = NULL ) {
      global $wpdb;
      $data = array(
        'name'  => $name,
        'link'  => $link,
        'ug_id' => $ug_id,
      );
      return $wpdb->insert($this->artists_table, $data);
    }


    public function get_artist_info( $artist_name ) {
      $get_artist_info = "SELECT * FROM %1$s WHERE name='%2$s'";
      $atts = array( $this->entries_table, $artist_name );
      return $this->esc_get_row($get_artist_info, $atts);
    }


    public function remove_artist( $artist_name ) {
      $remove_artist = "DELETE FROM %1$s WHERE name='%2$s';";
      return $this->esc_query($remove_artist, $artist_name);
    }


    public function get_artist_entries( $artist_name, $type, $order, $ratings = array() ) {
      // An artist has to be provided
      if ( ! isset($artist_name) || empty($artist_name) ) {
        throw new Exception(__('Artist name must be provided', 'ug-tabs-chords'));
      }

      // Initialize the query with default contents.
      $query = "
        SELECT $this->entries_table.* FROM $this->artists_table
        LEFT JOIN $this->entries_table
          ON $this->entries_table.artist_id = $this->artists_table.id
        WHERE $this->entries_table.name='%s'
      ";

      // Query attributes are populated when there are additional placeholders added to the query
      $query_atts = array( $artist_name );

      // Include the type in the query if one was specified
      if ( isset($type) && ! empty($type) ) {
        $query .= " AND $this->entries_table.type=%s";
        $query_atts[] = $type;
      }

      // Include only a subset of ratings
      if ( isset($ratings) && ! empty($ratings) ) {
        if ( is_array($ratings) ) {
          $query .= " AND $this->entries_table.rating IN (";
          $placeholder_arr = array_fill(0, count($ratings), '%d');
          $query .= join(',', $placeholder_arr);
          $query .= ")";
          $query_atts = array_merge($query_atts, $ratings);
        } else {
          $query .= " AND $this->entries_table.rating='%d'";
          $query_atts[] = $ratings;
        }
      }

      // Order the content if an order was specified.
      if ( $order === 'date' ) {
        $query .= " ORDER BY $this->entries_table.date ASC";
      } else {
        $query .= " ORDER BY $this->entries_table.name ASC";
      }
      $query .= ";";
      return $this->esc_get_row($query, $query_atts);
    }


    public function add_entries( $artist, $entries ) {

    }


    private function esc_query($query_str, $atts = array()) {
      global $wpdb;
      $escaped_query = $wpdb->prepare($query_str, $atts);
      return $wpdb->query($escaped_query);
    }


    private function esc_get_row($query_str, $atts = array()) {
      global $wpdb;
      $escaped_query = $wpdb->prepare($query_str, $atts);
      return $wpdb->get_row($escaped_query, ARRAY_A);
    }

    /**
     * Creates the DB tables used by the plugin. Does nothing if the tables have already been created.
     */
    public function create_tables() {
      global $wpdb;
      $artists_create_success = $wpdb->query("CREATE TABLE IF NOT EXISTS $this->artists_table ($this->artists_schema);");
      $entries_create_success = $wpdb->query("CREATE TABLE IF NOT EXISTS $this->entries_table ($this->entries_schema);");
      return $artists_create_success && $entries_create_success;
    }

    /**
     * Delete the tables created by this plugin. Use this from uninstall.php.
     */
    public function delete_tables() {
      global $wpdb;
      $artists_delete_success = $wpdb->query("DROP TABLE $this->artists_table;");
      $entries_delete_success = $wpdb->query("DROP TABLE $this->entries_table;");
      return $artists_delete_success && $entries_delete_success;
    }

    /**
     * Deletes all artists that are not anymore in use in any shortcodes to prevent unnecessary
     * bloating of the database tables due to old artists' entries. @TODO: invoke from WP_Cron daily?
     */
    public function clear_unused_artists() {
      // @TODO: loop through all artists in the database, scan tbrough all posts for each artist and
      // check for any shortcodes that are in use for that artist.
    }
  }
}
