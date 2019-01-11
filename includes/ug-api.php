<?php
/**
 * Provide API to Ultimate Guitar content by scraping the HTML by using Html_Dom.
 *
 * @package ug-tabs-chords
 */

namespace UGTC\Client\API;

use \Exception;

if ( ! defined( 'ABSPATH' ) ) {
  die( 'Access Denied!' );
}

require_once plugin_dir_path( __FILE__ ) . '../vendor/autoload.php';

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

    /**
     * Initialize the class.
     */
    public function __construct() {
      $this->ug_tabs_url = 'https://www.ultimate-guitar.com';
    }


    public function get_tabs_archive( $params, $limit ) {
      // Due to the nature of the Ultimate Guitar website having the tabs and chords of an artist
      // listed on an URL with the artist name suffixed with a random string (e.g. dream_theater_123123),
      // we can not know this for all possible artists beforehand. Thus, we must do a search for the
      // artist, and get the link from the results if there is a match.
      $search_url = $this->build_search_url($params);
      $search_payload = $this->get_ugapp_store_json($search_url);
      $search_data = $search_payload->data;

      // @TODO: check that there is at least a single result.

      // We can be sure that there is at least one result because otherwise 404 would be returned.
      // However, use the name of the artist from the remote URL instead of the user provided one.
      $artist_name = $search_data->results[0]->artist_name;
      $artist_url = $search_data->results[0]->artist_url;

      // Now that we have the artist link we can construct the full request URL to get all the entries.
      $query_string = $this->build_tabs_query_string($params);
      $artist_full_url = $artist_url . '?' . $query_string;
      return $this->get_ug_content($artist_full_url);
    }


    private function get_ug_content( $artist_full_url ) {
      $results = array();

      // Get the index page for the artist tabs, it will contain details about pagination and the
      // number of available entries for the requested type.
      $payload = $this->get_ugapp_store_json($artist_full_url);
      $first_page_tabs = $payload->data->other_tabs;
      $pages = $payload->data->pagination->pages;

      // Process the data of the first page as we already have access to it here
      foreach ( $first_page_tabs as $tab ) {
        if ( is_object($tab) ) {
          $results[] = array(
            'name'   => $tab->song_name,
            'type'   => $tab->type,
            'link'   => $tab->tab_url,
            'rating' => $tab->rating,
            'date'   => $tab->date,
            'tuning' => $tab->tuning,
          );
        }
      }

      // Remove the first page from the pagination because we already processed the data previously
      array_shift($pages);

      // Loop through the rest of the pages. We have to make a HTTP GET request into the URLs of
      // those pages in order to gain access to the tab data on each page
      if ( ! empty($pages) ) {
        foreach ( $pages as $page ) {
          $page_url = $this->ug_tabs_url . $page->url;
          $page_payload = $this->get_ugapp_store_json($page_url);
          $nth_page_tabs = $page_payload->data->other_tabs;
          foreach ( $nth_page_tabs as $tab ) {
            if ( is_object($tab) ) {
              $results[] = array(
                'name'   => $tab->song_name,
                'type'   => $tab->type,
                'link'   => $tab->tab_url,
                'rating' => $tab->rating,
                'date'   => $tab->date,
                'tuning' => $tab->tuning,
              );
            }
          }
        }
      }
      return $results;
    }


    private function get_ugapp_store_json( $url ) {
      $html_dom = new Html_Dom();

      // Don't continue if the html dom causes an error
      if ( false === $html_dom->load_file($url, false) ) {
        /* translators: %s: API URL */
        $error_msg = sprintf(__('Ultimate-Guitar Tabs & Chords: could not establish connection to "%s"', 'ug-tabs-chords'), $url);
        throw new Exception($error_msg);
      }

      // The window.UGAPP.store.page JS variable contains the data we want to access
      $noise = $html_dom->search_noise('window.UGAPP.store.page');

      // Clear memory to avoid PHP5 circular references memory leak.
      // See http://simplehtmldom.sourceforge.net/manual_faq.htm for details.
      $html_dom->clear();
      unset($html_dom);

      // Tidy up the noise a bit...
      // @TODO: check that this regexp works in every situations
      $noise = str_replace('window.UGAPP.store.page = ', '', $noise);
      $noise = str_replace(';     window.UGAPP.store.i18n = {};', '', $noise);
      $trimmed_noise = trim($noise);

      // Return the payload as stdClass object from decoded JSON
      $json_payload = json_decode($trimmed_noise);
      return $json_payload;
    }


    private function build_search_url( $content_params ) {
      $query_array = array(
        'search_type' => 'band',
        'value'       => $content_params['artist'],
      );
      return $this->ug_tabs_url . '/search.php?' . http_build_query($query_array);
    }


    private function build_tabs_query_string( $content_params ) {
      $query_array = array();

      // Get content type. If content type is "all", it does not need to be
      // included in the query string.
      $type_formatted = strtolower(trim($content_params['type']));
      if ( 'all' !== $type_formatted ) {
        $query_array['filter'] = $type_formatted;
      }

      // Check if sort method is set (not mandatory). Default is title_srt, so
      // only include the sort method if it is different
      if ( isset($content_params['order']) && ! empty($content_params['order'])
        && 'title_srt' !== strtolower(trim( $content_params['order'])) ) {
        $sort_formatted = strtolower(trim($content_params['order']));
        $query_array['sort'] = $content_params['order'];
      }
      return http_build_query($query_array);
    }
  }

}
