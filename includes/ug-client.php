<?php
/**
* Ultimate Guitar client file for retreiving scraped HTML.
*
* @package ug-tabs-chords
*/
namespace UGTabsChords;

require_once( plugin_dir_path( __FILE__ ) . '../vendor/autoload.php' );

use \simplehtmldom_1_5\simple_html_dom as HtmlDom;

if ( ! class_exists( 'UGClient' ) ) {
  /**
  *
  */
  class UGClient {

    /* Basic search URL for Ultimate Guitar */
    private $query_string_ug_search_url;

    /* Type 1 value, describes whether to search for chords (200) or tabs (300) */
    private $type_1;

    /* Type 2 value, describes whether to search for full songs or (40000) or other */
    private $type_2;

    /* Tab/Chord ratings values to retreive */
    private $ratings;

    /* The order in which to retreive the results */
    private $order;

    /**
    * Initialize the class.
    *
    * @since 1.0.0
    */
    public function __construct() {
      // Set defaults
      $this->query_string_ug_search_url = 'https://www.ultimate-guitar.com/search.php?';
      $this->setDefaultParams();
    }

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
    */
    public function setType1( $type1 ) {
      $possible_values = array( 100, 200, 300, 400, 500, 600, 700, 800, 900 );
      if ( ! in_array( $type1, $possible_values ) ) {
        return new WP_Error( 'invalid_type_1_value', __( 'Invalid value supplied for type 1 search parameter', 'ug-tabs-chords' ) );
      }
      $this->type1 = $type1;
      return true;
    }

    /**
    * Set the type 2 for the search.
    *
    * @param array $type2 The type 2 values (int) to set.
    * --- POSSIBLE VALUES ---
    *   10000 = album
    *   20000 = intro
    *   30000 = solo
    *   40000 = whole song
    */
    public function setType2( $type2 ) {
      $possible_values = array( 10000, 20000, 30000, 40000 );
      if ( ! in_array( $type2, $possible_values ) ) {
        return new WP_Error( 'invalid_type_2_value', __( 'Invalid value supplied for type 2 search parameter', 'ug-tabs-chords' ) );
      }
      $this->type2 = $type2;
      return true;
    }

    /**
    * Set the order for the retreived search results.
    *
    * @param string $order The order to set.
    * --- POSSIBLE VALUES ---
    *   'title_srt' = Sort by title ABC
    *   'myweight' = Sort by relevancy
    */
    public function setOrder( $order ) {
      $possible_values = array( 10000, 20000, 30000, 40000 );
      if ( ! in_array( $type2, $possible_values ) ) {
        return new WP_Error( 'invalid_type_2_value', __( 'Invalid value supplied for type 2 search parameter', 'ug-tabs-chords' ) );
      }
      $this->type2 = $type2;
      return true;
    }

    /**
    * Set the allowed ratings for the search.
    *
    * @param array $ratings The rating values (1-5) to set.
    */
    public function setAllowedRatings( $ratings ) {
      $possible_values = array( 1, 2, 3, 4, 5 );
      if ( ! in_array( $type2, $possible_values ) ) {
        return new WP_Error( 'invalid_rating_value', __( 'Invalid value supplied for type 2 search parameter', 'ug-tabs-chords' ) );
      }
      $this->type2 = $type2;
      return true;
    }


    public function search( $artist ) {

      $search_array = array(
        'band_name' =>     $artist,
        'type'              =>     $this->type_1,
        'order'            =>     $this->order,
        'rating'           =>     $this->ratings,
        'type2'           =>     $this->type_2
      );

      // DEBUG
      //var_dump($this->performSearch( $search_array ));
      //die();
      return $this->performSearch( $search_array );
    }

    /**
    *
    */
    private function performSearch( $search_params ) {
      $ug_return_data = array();
      // Construct query string based on search parameters
      $query_string = $this->buildQueryString( $search_params );

      $html = new HtmlDom();
      $html->load_file( $query_string );
      $result_table = $html->find( 'table.tresults tbody tr' );

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

          $single_entry_rating_td = $result_row->find( 'td.tresults--rating span.rating');
          // Ratings are 0 if they don't exist
          $single_entry_rating  = 0;
          if ( ! empty($single_entry_rating_td ) && isset( $single_entry_rating_td->title ) ) {
            $single_entry_rating = $single_entry_rating_td->title;
          }

          $result_row_array = array(
            'name' => $single_entry_name,
            'type' => $single_entry_type,
            'link' => $single_entry_link,
            'rating' => $single_entry_rating
          );

          $ug_return_data[$index] = $result_row_array;
          ++$index;
        }
      }
      return $ug_return_data;
    }

    /**
    *
    */
    private function buildQueryString( $searchParams ) {
      $query_string = $this->query_string_ug_search_url;
      $query_string .= \http_build_query( $searchParams );
      return $query_string;
    }
  }
}
