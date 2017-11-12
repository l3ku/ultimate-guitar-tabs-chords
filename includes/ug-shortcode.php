<?php
/**
* Shortcode file for displaying Ultimate Guitar Tabs & Chords frontend data.
*
* @package ug-tabs-chords
*/

defined( 'ABSPATH' ) or die( 'Access Denied!' );

/**
* Display shortcode functionality.
*/
function createShortCode( $ug_client, $atts, $content ) {
  // Normalize attribute keys to lowercase and search artists
  $atts = array_change_key_case( ( array ) $atts, CASE_LOWER );
  if ( ! isset( $atts['artist'] ) || empty( $atts['artist'] ) ) {
    return;
  }
  $artist = $atts['artist'];

  // Strip all whitespace and transform to lowercase with underscores
  $artist_clean = strtolower( str_replace( ' ', '_', $artist ) );

  // Use WP transient API for storing artist data
  // TODO: Somehow hook this with WP-Admin shortcode insertion instead of front?
  $artist_entries = get_transient( 'ugtc_artist_entries_' . $artist_clean );
  $results = $artist_entries;

  if ( false === $artist_entries ) {
    setSearchSettings( $ug_client );
    $results = $ug_client->search( $artist );
    set_transient( 'ugtc_artist_entries_' . $artist_clean, $results, 86400 ); // 24 hours
  }

  // Create a display div from artists' data
  $artist_div = '<h2 class="ugtc-single-artist-name">' . $artist . '</h2>';
  $artist_div .= '<div class="ugtc-single-artist-entries"><table>';

  // Include table head for labels
  $artist_div .= '<thead><tr>';
  $artist_div .= '<th>' . __( 'Name', 'ug-tabs-chords' ) . '</th>';
  $artist_div .= '<th>' . __( 'Type', 'ug-tabs-chords' ) . '</th>';
  $artist_div .= '<th>' . __( 'Rating', 'ug-tabs-chords' ) . '</th>';
  $artist_div .= '</tr></thead><tbody>';

  foreach ( $results as $result ) {
    $single_entry_row = '<tr class="ugtc-single-entry-row">';

    // Get all the data for a single entry
    $entry_name = '<td class="ugtc-single-entry-name"><a class="ugtc-single-entry ugtc-single-entry-link" href="' . $result['link'] . '">' . $result['name'] . '</a></td>';

    $entry_type = '<td class="ugtc-single-entry-type">' . $result['type'] . '</td>';
    $entry_rating = '<td class="ugtc-single-entry-rating">' . str_repeat( '&#9733;', $result['rating'] ) . '</td>';

    $single_entry_row .= $entry_name;
    $single_entry_row .= $entry_type;
    $single_entry_row .= $entry_rating;
    $single_entry_row .= '</tr>';

    $artist_div .= $single_entry_row;
  }
  $artist_div .= '</tbody></table></div>';
  return $artist_div;
}

/**
* Set search settings for Ultimate Guitar Client.
*/
function setSearchSettings( $ug_client ) {
  $search_entry_types = get_option( 'ugtc_search_entry_types' );
  if ( ! empty( $search_entry_types ) ) {
    $ug_client->setType1( $search_entry_types );
  }

  $search_entry_lengths = get_option( 'ugtc_search_entry_lengths' );
  if ( ! empty( $search_entry_lengths ) ) {
    $ug_client->setType2( $search_entry_lengths );
  }

  $search_sort_option = get_option( 'ugtc_search_sort_option' );
  if ( ! empty( $search_sort_option ) ) {
    $ug_client->setOrder( $search_sort_option );
  }

  $search_ratings = get_option( 'ugtc_search_ratings' );
  if ( ! empty( $search_ratings ) ) {
    $ug_client->setAllowedRatings( $search_ratings );
  }
}
