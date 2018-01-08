<?php
/**
* File for displaying main plugin page content.
*
* @package ug-tabs-chords
*/

defined( 'ABSPATH' ) or die( 'Access Denied!' ); // Prevent direct access

require_once( plugin_dir_path( __FILE__ ) . '../shortcode/ug-shortcode.php' );
require_once( plugin_dir_path( __FILE__ ) . '../cache/ug-cache.php' );
require_once( plugin_dir_path( __FILE__ ) . '../client/ug-client-values.php' );

use UGTC\Shortcode\UG_Shortcode;
use UGTC\Cache\UG_Cache;
use UGTC\Client;

// Generate shortcode on form submission, sanitize input and disallow HTML
if ( isset( $_POST['ugtc_generate_shortcode'] ) ) {
  $artist = trim( wp_kses( $_POST['ugtc_shortcode_artist'], array() ) );
  $type = trim( wp_kses( $_POST['ugtc_shortcode_type'], array() ) );
  $order = trim( wp_kses( $_POST['ugtc_shortcode_order'], array() ));
  $limit = trim( wp_kses( $_POST['ugtc_shortcode_limit'], array() ) );
  $data = array(
    'artist' => $artist,
    'type'   => $type,
    'order'  => $order,
    'limit'  => $limit
  );

  // Generate shortcode and remove the artist from cache.
  $gen_shortcode = UG_Shortcode::generate_shortcode( $data );
  UG_Cache::remove_from_cache( $artist );
}

?>

<div class="wrap">
  <!-- Display purge cache success -->
  <?php if ( isset( $_POST['ugtc_purge_cache'] ) ) : ?>
    <?php UG_Cache::purge_cache(); ?>
    <div class="notice notice-success is-dismissible ugtc-success-msg">
      <?php _e( 'Cache emptied successfully!', 'ug-tabs-chords' ); ?>
    </div>
  <?php endif; ?>

  <h1 class="ugtc-title">
    <?php _e( 'Ultimate Guitar Tabs & Chords', 'ug-tabs-chords' ); ?>
  </h1>

  <h2 class="ugtc-subtitle">
    <?php _e( 'Shortcode Generator', 'ug-tabs-chords' ); ?>
  </h2>
  <div class="ugtc-shortcode-generator">
    <p class="ugtc-instructions">
      <?php _e( 'This plugin works via a shortcode, which is added to a page or post. Generate the shortcode by using the generator below:', 'ug-tabs-chords' ) ?>
    </p>
    <form id="ugtc-shortcode-generator-form" class="ugtc-shortcode-generator-form" method="post" action="">
      <table class="form-table">
        <tr>
          <th scope="row">
            <?php _e( 'Artist', 'ug-tabs-chords' ); ?>
          </th>
          <td>
            <input type="text" name="ugtc_shortcode_artist" placeholder="<?php _e( 'Artist Name', 'ug-tabs-chords' ); ?>"
            <?php if ( isset( $artist ) ) echo 'value="' . $artist . '"'; ?>>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php _e( 'Type', 'ug-tabs-chords' ); ?>
          </th>
          <td>
            <select name="ugtc_shortcode_type">
              <?php $valid_type_values = Client\ugtc_get_valid_types(); ?>
              <?php foreach ( $valid_type_values as $key => $val ) : ?>
                <option value="<?php echo $key; ?>"><?php echo $val; ?>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php _e( 'Order', 'ug-tabs-chords' ); ?>
          </th>
          <td>
            <select name="ugtc_shortcode_order">
              <?php $valid_order_values = Client\ugtc_get_valid_orders(); ?>
              <?php foreach ( $valid_order_values as $key => $val ) : ?>
                <option value="<?php echo $key; ?>"><?php echo $val; ?>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php _e( 'Limit results', 'ug-tabs-chords' ); ?>
          </th>
          <td>
            <input type="number" name="ugtc_shortcode_limit" placeholder="100" min="1" max="1000"
            <?php if ( isset( $limit ) ) echo 'value="' . $limit . '"'; ?>>
          </td>
        </tr>
        <tr>
          <td>
            <input type="submit" class="button button-secondary" name="ugtc_generate_shortcode" value="<?php _e( 'Generate Shortcode', 'ug-tabs-chords' ); ?>">
          </td>
        </tr>
      </table>
      <div class="ugtc-shortcode-generator-shortcode">
        <?php if ( isset( $gen_shortcode ) ) : ?>
          <?php if ( ! empty( $gen_shortcode ) && ! is_wp_error( $gen_shortcode ) ) : ?>
            <?php _e( 'Your Shortcode: ', 'ug-tabs-chords' ); ?><br>
            <code>
              <?php echo $gen_shortcode; ?>
            </code>
          <?php elseif ( is_wp_error( $gen_shortcode ) ): ?>
            <small class="ugtc-shortcode-generator-error">
              <?php echo sprintf( '%s: %s', __( 'Error', 'ug-tabs-chords' ), $gen_shortcode->get_error_message() ); ?>
            </small>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <h2 class="ugtc-subtitle">
    <?php _e( 'Cache', 'ug-tabs-chords' ); ?>
  </h2>
  <p class="ugtc-instructions">
    <?php _e( 'Ultimate Guitar Tabs & Chords uses the transient API of WordPress to cache data from ultimate-guitar.com. This results as much faster page loading times, as the data is stored in the database instead of having to always make requests to external servers. The cache can be emptied by using the button below: ', 'ug-tabs-chords' ); ?>
  </p>
  <form id="ugtc-purge-cache-form" method="POST" action="">
    <input type="submit" name="ugtc_purge_cache" class="button button-secondary" value="<?php _e( 'Purge Cache', 'ug-tabs-chords' ); ?>">
  </form>
</div>
