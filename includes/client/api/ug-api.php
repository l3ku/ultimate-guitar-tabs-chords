<?php
/**
 * Provide API to Ultimate Guitar content by scraping the HTML by using Html_Dom.
 *
 * @package ug-tabs-chords
 */

namespace UGTC\Client\API;

if ( ! defined( 'ABSPATH' ) ) {
  die( 'Access Denied!' );
}

require_once plugin_dir_path( __FILE__ ) . '../../../vendor/autoload.php';

use \simplehtmldom_1_5\simple_html_dom as Html_Dom;

if ( ! class_exists( 'UG_API' ) ) {

  /**
   * Class UG_API
   *
   * @package ug-tabs-chords
   * @version  0.0.1
   * @since 0.0.1
   * @author Leo Toikka
   */
  class UG_API {

    /* Basic URL for Ultimate Guitar tabs archive */
    private $ug_tabs_archive_url;

    /* HTML Dom for scraping HTML */
    private $html_dom;

    /**
     * Initialize the class.
     */
    public function __construct() {
      $this->ug_tabs_archive_url = 'https://www.ultimate-guitar.com/tabs/';
      $this->html_dom            = new Html_Dom();
      $this->stream_context      = stream_context_create(
        array(
          'http' => array(
            'method' => 'GET',
            'header' => 'Cookie: back_to_classic_ug=1',
          ),
        )
      );
    }

    public function get_tabs_archive( $params, $limit ) {
      // Construct query string based on search parameters
      $query_string = $this->build_query_string( $params );

      set_time_limit(60);

      // Don't continue if the html dom causes an error
      if ( false === @$this->html_dom->load_file( $query_string, false, $this->stream_context ) ) {
        /* translators: %s: Ultimate Guitar tabs archive URL */
        error_log( sprintf( __( 'Ultimate-Guitar Tabs & Chords: could not establish connection to "%s"', 'ug-tabs-chords' ), $this->ug_tabs_archive_url ) );
        return array();
      }

      // Find page numbering, find the largest page value to get all possible
      // results.
      $page_numbers_html = $this->html_dom->find( 'table tbody td b a.ys' );
      $max_page_number   = 1;
      if ( ! empty( $page_numbers_html ) ) {
        foreach ( $page_numbers_html as $page_number_html ) {
          $page_number_str = $page_number_html->plaintext;

          if ( ! empty( $page_number_str ) && is_numeric( $page_number_str )
            && intval( $page_number_str ) > $max_page_number ) {
            $max_page_number = intval( $page_number_str );
          }
        }
      }

      // Loop through all pages
      $ug_return_data = array();
      $content_count  = 0;
      for ( $page_number = 1; $page_number <= $max_page_number; $page_number++ ) {
        // Modify query string so it contains the current page number
        $query_string = preg_replace( '/[0-9]+.htm/', (string) $page_number . '.htm', $query_string );

        // Don't continue if the page was not found, but return the content
        // that was fetched before the error
        if ( false === @$this->html_dom->load_file( $query_string, false, $this->stream_context ) ) {
          return $ug_return_data;
        }

        $result_table = $this->html_dom->find( 'table tbody tr.tr__lg' );

        if ( ! empty( $result_table ) ) {

          foreach ( $result_table as $result_row ) {
            $single_entry_name_td = $result_row->find( 'td a', 0 );

            // We don't want any empty values for tab/chord names
            if ( empty( $single_entry_name_td ) ) {
              continue;
            }

            // Don't exceed the limit
            if ( $content_count >= $limit ) {
              return $ug_return_data;
            }

            $single_entry_name = $single_entry_name_td->plaintext;
            $single_entry_link = $single_entry_name_td->href;
            $single_entry_type = $result_row->last_child()->plaintext;

            $single_entry_rating_td = $result_row->find( 'td.tresults--rating span.rating', 0 );
            // Ratings are 0 if they don't exist
            $single_entry_rating = 0;
            if ( ! empty( $single_entry_rating_td ) && isset( $single_entry_rating_td->title ) ) {
              $single_entry_rating = $single_entry_rating_td->title;
            }

            $result_row_array = array(
              'name'   => $single_entry_name,
              'type'   => $single_entry_type,
              'link'   => $single_entry_link,
              'rating' => $single_entry_rating,
            );

            $ug_return_data[] = $result_row_array;
            $content_count++;
          }
        }
      }
      return $ug_return_data;
    }

    private function build_query_string( $content_params ) {
      $query_string = $this->ug_tabs_archive_url;

      // Get the artist, trim whitespace, replace space with underscores and
      // transform to lowercase, e.g. "  Dream Theater" => "dream_theater".
      $artist           = $content_params['artist'];
      $formatted_artist = strtolower( preg_replace( '/\s+/', '_', trim( $artist ) ) );
      $query_string    .= $formatted_artist;

      // Get content type. If content type is "all", it does not need to be
      // included in the query string.
      $type_formatted = strtolower( trim( $content_params['type'] ) );
      if ( 'all' !== $type_formatted ) {
        $query_string .= '_' . $type_formatted;
      }

      // Always start from the first page
      $query_string .= '_tabs1.htm';

      // Check if sort method is set (not mandatory). Default is title_srt, so
      // only include the sort method if it is different
      if ( isset( $content_params['order'] ) && ! empty( $content_params['order'] )
        && 'title_srt' !== strtolower( trim( $content_params['order'] ) ) ) {
        $sort_formatted = strtolower( trim( $content_params['order'] ) );
        $query_string  .= '?sort=' . $sort_formatted;
      }

      return $query_string;
    }
  }

}
