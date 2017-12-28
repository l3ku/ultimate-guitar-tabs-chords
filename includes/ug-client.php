<?php
/**
* Ultimate Guitar client file for retreiving scraped HTML.
*
* @package ug-tabs-chords
*/

defined( 'ABSPATH' ) or die( 'Access Denied!' );

require_once( plugin_dir_path( __FILE__ ) . '../vendor/autoload.php' );

use simplehtmldom_1_5\simple_html_dom as HtmlDom;

if ( ! class_exists( 'UGClient' ) ) {

  /**
  * Class UGClient
  *
  * @package ug-tabs-chords
  * @version  0.0.1
  * @since 0.0.1
  * @author Leo Toikka
  */
  class UGClient {

    /* Basic search URL for Ultimate Guitar */
    private $query_string_ug_search_url;

    /* Type 1 value, describes whether to search for chords (200) or tabs (300) */
    private $type_1;
    private $possible_type_1_values;

    /* Type 2 value, describes whether to search for full songs or (40000) or other */
    private $type_2;
    private $possible_type_2_values;

    /* Tab/Chord ratings values to retreive */
    private $ratings;
    private $possible_rating_values;

    /* The order in which to retreive the results */
    private $order;
    private $possible_order_values;

    /**
    * Initialize the class. Possible values for search parameters are also
    * set here.
    *
    * @since 0.0.1
    */
    public function __construct() {
      $this->possible_type_1_values = array(
        100 => __( 'Video', 'ug-tabs-chords' ),
        200 => __( 'Tab', 'ug-tabs-chords' ),
        300 => __( 'Chords', 'ug-tabs-chords' ),
        400 => __( 'Bass', 'ug-tabs-chords' ),
        500 => __( 'Guitar Pro', 'ug-tabs-chords' ),
        600 => __( 'Power', 'ug-tabs-chords' ),
        700 => __( 'Drums', 'ug-tabs-chords' ),
        800 => __( 'Ukulele', 'ug-tabs-chords' ),
        900 => __( 'Official', 'ug-tabs-chords' )
      );
      $this->possible_type_2_values = array(
        10000 => __( 'Album', 'ug-tabs-chords' ),
        20000 => __( 'Intro', 'ug-tabs-chords' ),
        30000 => __( 'Solo', 'ug-tabs-chords' ),
        40000 => __( 'Whole song', 'ug-tabs-chords' )
      );
      $this->possible_rating_values = array( 1, 2, 3, 4, 5 );
      $this->possible_order_values = array(
        'title_srt' => __( 'Title ABC', 'ug-tabs-chords' ),
        'myweight' => __( 'Relevance', 'ug-tabs-chords' )
      );

      // Set defaults
      $this->query_string_ug_search_url = 'https://www.ultimate-guitar.com/search.php?';
      $this->setDefaultParams();
    }
    /**
     * Resets the current client search parameter values to the default ones:
     * type_1: Tabs (200) & Chords (300)
     * type_2: Whole Song (40000)
     * ratings: 1-5
     * order: relevancy
     */
    public function setDefaultParams() {
      // Return both chords and tabs by default
      $this->type_1 = array( 200, 300 );
      // Return full songs by default
      $this->type_2 = array( 40000 );
      // Return all ratings on default
      $this->ratings = array( 1, 2, 3, 4, 5 );
      // Return based on relevance
      $this->order = 'myweight';
    }

    /**
    * Set the type 1 for the search.
    *
    * @param array $type1 The type 1 values (int) to set.
    * --- POSSIBLE VALUES ---
    *   100 = video
    *   200 = tab
    *   300 = chords
    *   400 = bass
    *   500 = guitar pro
    *   600 = power
    *   700 = drums
    *   800 = ukulele
    *   900 = official
    * @return mixed True if the operation succeeded, or WP_ERROR object.
    */
    public function setType1( $type_1 ) {
      if ( array_intersect( $type_1, array_keys( $this->possible_type_1_values ) ) != $type_1 ) {
        return new WP_Error( 'invalid_type_1_value', __( 'Invalid value supplied for type 1 search parameter', 'ug-tabs-chords' ) );
      }
      $this->type_1 = $type_1;

      return true;
    }

    /**
    * Get the type 1 values for search results.
    *
    * @return array (int) The type 1 for search results
    */
    public function getType1() {
      return $this->type_1;
    }

    /**
    * Get the possible type 1 values for search results.
    *
    * @return array (int) The possible type 1 values for search results
    */
    public function getPossibleType1Values() {
      return $this->possible_type_1_values;
    }

    /**
    * Set the type 2 for search results.
    *
    * @param array $type2 The type 2 values (int) to set.
    * --- POSSIBLE VALUES ---
    *   10000 = album
    *   20000 = intro
    *   30000 = solo
    *   40000 = whole song
    * @return mixed True if the operation succeeded, or WP_ERROR object.
    */
    public function setType2( $type_2 ) {
      if ( array_intersect( $type_2, array_keys( $this->possible_type_2_values ) ) != $type_2 ) {
        return new WP_Error( 'invalid_type_2_value', __( 'Invalid value supplied for type 2 search parameter', 'ug-tabs-chords' ) );
      }
      $this->type_2 = $type_2;

      return true;
    }

    /**
    * Get the type 2 values for search results.
    *
    * @return string The type 2 for search results
    */
    public function getType2() {
      return $this->type_2;
    }

    /**
    * Get the possible type 2 values for search results.
    *
    * @return array (int) The possible type 2 values for search results
    */
    public function getPossibleType2Values() {
      return $this->possible_type_2_values;
    }

    /**
    * Set the order for the retreived search results.
    *
    * @param string $order The order to set.
    * --- POSSIBLE VALUES ---
    *   'title_srt' = Sort by title ABC
    *   'myweight' = Sort by relevancy
    * @return mixed True if the operation succeeded, or WP_ERROR object.
    */
    public function setOrder( $order ) {
      if ( ! in_array( $order, array_keys( $this->possible_order_values ) ) ) {
        return new WP_Error( 'invalid_order_value', __( 'Invalid value supplied for order search parameter', 'ug-tabs-chords' ) );
      }
      $this->order = $order;

      return true;
    }

    /**
    * Get the order for search results.
    *
    * @return string The order for search results
    */
    public function getOrder() {
      return $this->order;
    }

    /**
    * Get the possible order values for search results.
    *
    * @return array (int) The possible order values for search results
    */
    public function getPossibleOrderValues() {
      return $this->possible_order_values;
    }

    /**
    * Set the allowed ratings for the search.
    *
    * @param array $ratings The rating values (1-5) to set.
    */
    public function setAllowedRatings( $ratings ) {
      if ( array_intersect( $ratings, $this->possible_rating_values ) != $ratings ) {
        return new WP_Error( 'invalid_rating_value', __( 'Invalid value supplied for ratings search parameter', 'ug-tabs-chords' ) );
      }
      $this->ratings = $ratings;

      return true;
    }

    /**
    * Get the allowed ratings for the search
    *
    * @return string The ratings for search results
    */
    public function getAllowedRatings() {
      return $this->ratings;
    }

    /**
    * Get the possible rating values for search results.
    *
    * @return array (int) The possible rating values for search results
    */
    public function getPossibleRatingValues() {
      return $this->possible_rating_values;
    }

    /**
    * Search artist entries with the possibly predefined search parameters type 1, type 2,
    * order, rating
    *
    * @return array (mixed) An array containing the search results.
    * Array keys: 'name', 'type', 'link', 'rating'
    */
    public function search( $artist ) {
      $search_array = array(
        'band_name'  =>     $artist,
        'type'       =>     $this->type_1,
        'order'      =>     $this->order,
        'rating'     =>     $this->ratings,
        'type2'      =>     $this->type_2
      );

      return $this->performSearch( $search_array );
    }

    private function performSearch( $search_params ) {
      // Construct query string based on search parameters
      $query_string = $this->buildQueryString( $search_params );

      // Create HTML Dom and specify timeout limit to 15 seconds so the
      // site may still function in case of some errors.
      $html = new HtmlDom();
      set_time_limit(15);

      // Don't continue if the html dom caused an error
      if ( @$html->load_file( $query_string ) === false ) {
        error_log( sprintf( __( 'Ultimate-Guitar Tabs & Chords: could not establish connection to "%s"', 'ug-tabs-chords' ), $this->query_string_ug_search_url ) );
        return array();
      }

      $result_table = $html->find( 'table.tresults tbody tr' );
      $ug_return_data = array();

      if ( ! empty( $result_table ) ) {

        $index = 0;
        foreach ( $result_table as $result_row ) {
          $single_entry_name_td = $result_row->find( 'td.search-version--td div.search-version--link a.result-link', 0 );

          // We don't want any empty values for tab/chord names
          if ( empty( $single_entry_name_td ) ) {
            continue;
          }

          $single_entry_name = $single_entry_name_td->plaintext;
          $single_entry_link = $single_entry_name_td->href;
          $single_entry_type = $result_row->last_child()->plaintext;

          $single_entry_rating_td = $result_row->find( 'td.tresults--rating span.rating', 0 );
          // Ratings are 0 if they don't exist
          $single_entry_rating  = 0;
          if ( ! empty( $single_entry_rating_td ) && isset( $single_entry_rating_td->title ) ) {
            $single_entry_rating = $single_entry_rating_td->title;
          }

          $result_row_array = array(
            'name'    =>   $single_entry_name,
            'type'    =>   $single_entry_type,
            'link'    =>   $single_entry_link,
            'rating'  =>   $single_entry_rating
          );

          $ug_return_data[$index] = $result_row_array;
          ++$index;
        }
      }

      return $ug_return_data;
    }

    private function buildQueryString( $searchParams ) {
      $query_string = $this->query_string_ug_search_url;
      $query_string .= http_build_query( $searchParams );

      return $query_string;
    }
  }
}
