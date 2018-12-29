<?php
/**
 * File for displaying main plugin page content.
 *
 * @package ug-tabs-chords
 */

if ( ! defined( 'ABSPATH' ) ) {
  die( 'Access Denied!' );
}

require_once plugin_dir_path( __FILE__ ) . '../shortcode/ug-shortcode.php';
require_once plugin_dir_path( __FILE__ ) . '../ug-cache.php';
require_once plugin_dir_path( __FILE__ ) . '../ug-client-values.php';

use UGTC\Shortcode\UG_Shortcode;
use UGTC\Cache\UG_Cache;
use UGTC\Client;

function ugtc_render_main_page() {
  // Generate shortcode on form submission, sanitize input and disallow HTML
  if ( isset( $_POST['ugtc_generate_shortcode'] ) && isset( $_POST['_ugtc_nonce'] )
    && wp_verify_nonce( $_POST['_ugtc_nonce'], 'ugtc_generate_shortcode' ) ) {

    $artist = trim( wp_kses( $_POST['ugtc_shortcode_artist'], array() ) );
    $type   = trim( wp_kses( $_POST['ugtc_shortcode_type'], array() ) );
    $order  = trim( wp_kses( $_POST['ugtc_shortcode_order'], array() ));
    $limit  = trim( wp_kses( $_POST['ugtc_shortcode_limit'], array() ) );
    $data   = array(
      'artist' => $artist,
      'type'   => $type,
      'order'  => $order,
      'limit'  => $limit,
    );

    // Generate shortcode and remove the artist from cache.
    $gen_shortcode = UG_Shortcode::generate_shortcode( $data );
    UG_Cache::remove_from_cache( $artist );
  }

  ?>

  <div class="wrap">
    <!-- Display purge cache success -->
    <?php
    if ( isset( $_POST['ugtc_purge_cache'] ) && isset( $_POST['_ugtc_nonce'] )
      && wp_verify_nonce( $_POST['_ugtc_nonce'], 'ugtc_purge_cache' ) ) :
      UG_Cache::purge_cache();
      ?>
      <div class="notice notice-success is-dismissible ugtc-success-msg">
        <?php esc_attr_e( 'Cache emptied successfully!', 'ug-tabs-chords' ); ?>
      </div>
    <?php endif; ?>

    <h1 class="ugtc-title">
      <?php esc_attr_e( 'Ultimate Guitar Tabs & Chords', 'ug-tabs-chords' ); ?>
    </h1>

    <h2 class="ugtc-subtitle">
      <?php esc_attr_e( 'Shortcode Generator', 'ug-tabs-chords' ); ?>
    </h2>
    <div class="ugtc-shortcode-generator">
      <p class="ugtc-instructions">
        <?php esc_attr_e( 'This plugin works via a shortcode, which is added to a page or post. Generate the shortcode by using the generator below:', 'ug-tabs-chords' ); ?>
      </p>
      <form id="ugtc-shortcode-generator-form" class="ugtc-shortcode-generator-form" method="post" action="">
        <table class="form-table">
          <tr>
            <th scope="row">
              <?php esc_attr_e( 'Artist', 'ug-tabs-chords' ); ?>
            </th>
            <td>
              <input type="text" name="ugtc_shortcode_artist" placeholder="<?php esc_attr_e( 'Artist Name', 'ug-tabs-chords' ); ?>"
              <?php if ( isset( $artist ) ) : ?>
                value="<?php echo esc_attr( $artist ); ?>"
              <?php endif; ?>
                />
            </td>
          </tr>
          <tr>
            <th scope="row">
              <?php esc_attr_e( 'Type', 'ug-tabs-chords' ); ?>
            </th>
            <td>
              <select name="ugtc_shortcode_type">
                <?php $valid_type_values = Client\ugtc_get_valid_types(); ?>
                <?php foreach ( $valid_type_values as $key => $val ) : ?>
                  <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $val ); ?>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>
          <tr>
            <th scope="row">
              <?php esc_attr_e( 'Order', 'ug-tabs-chords' ); ?>
            </th>
            <td>
              <select name="ugtc_shortcode_order">
                <?php $valid_order_values = Client\ugtc_get_valid_orders(); ?>
                <?php foreach ( $valid_order_values as $key => $val ) : ?>
                  <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $val ); ?>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>
          <tr>
            <th scope="row">
              <?php esc_attr_e( 'Limit results', 'ug-tabs-chords' ); ?>
            </th>
            <td>
              <input type="number" name="ugtc_shortcode_limit" placeholder="100" min="1" max="1000"
              <?php if ( isset( $limit ) ) : ?>
                value="<?php echo esc_attr( $limit ); ?>"
              <?php endif; ?>
              />
            </td>
          </tr>
          <tr>
            <td>
              <input type="submit" class="button button-secondary" name="ugtc_generate_shortcode" value="<?php esc_attr_e( 'Generate Shortcode', 'ug-tabs-chords' ); ?>">
            </td>
          </tr>
        </table>
        <div class="ugtc-shortcode-generator-shortcode">
          <?php if ( isset( $gen_shortcode ) ) : ?>
            <?php if ( ! empty( $gen_shortcode ) && ! is_wp_error( $gen_shortcode ) ) : ?>
              <?php esc_attr_e( 'Your Shortcode: ', 'ug-tabs-chords' ); ?><br>
              <code>
                <?php echo esc_attr( $gen_shortcode ); ?>
              </code>
            <?php elseif ( is_wp_error( $gen_shortcode ) ) : ?>
              <small class="ugtc-shortcode-generator-error">
                <?php echo esc_html( sprintf( '%s: %s', __( 'Error', 'ug-tabs-chords' ), $gen_shortcode->get_error_message() ) ); ?>
              </small>
            <?php endif; ?>
          <?php endif; ?>
        </div>
        <?php wp_nonce_field( 'ugtc_generate_shortcode', '_ugtc_nonce' ); ?>
      </form>
    </div>

    <h2 class="ugtc-subtitle">
      <?php esc_attr_e( 'Cache', 'ug-tabs-chords' ); ?>
    </h2>
    <p class="ugtc-instructions">
      <?php esc_attr_e( 'Ultimate Guitar Tabs & Chords uses the transient API of WordPress to cache data from ultimate-guitar.com. This results as much faster page loading times, as the data is stored in the database instead of having to always make requests to external servers. The cache can be emptied by using the button below: ', 'ug-tabs-chords' ); ?>
    </p>
    <form id="ugtc-purge-cache-form" method="POST" action="">
      <input type="submit" name="ugtc_purge_cache" class="button button-secondary" value="<?php esc_attr_e( 'Purge Cache', 'ug-tabs-chords' ); ?>">
      <?php wp_nonce_field( 'ugtc_purge_cache', '_ugtc_nonce' ); ?>
    </form>
  </div>
  <?php
}
