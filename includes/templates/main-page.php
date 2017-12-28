<?php
/**
* File for displaying main plugin page content.
*
* @package ug-tabs-chords
*/

defined( 'ABSPATH' ) or die( 'Access Denied!' );

require_once( plugin_dir_path( __FILE__ ) . '../ug-shortcode.php' );
require_once( plugin_dir_path( __FILE__ ) . '../ug-cache.php' );

// Generate shortcode on form submission, sanitize input and disallow HTML
if ( isset( $_POST['ugtc_shortcode_artist'] ) && isset( $_POST['ugtc_shortcode_limit_results'] ) ) {
  $artist = trim( wp_kses( $_POST['ugtc_shortcode_artist'], array() ) );
  $limit = trim( wp_kses( $_POST['ugtc_shortcode_limit_results'], array() ) );

  // Generate shortcode and remove the artist from cache.
  $gen_shortcode = UGShortcode::generateShortcode( $artist, $limit );
  UGCache::removeFromCache( $artist );
}

?>

<div class="wrap">
  <!-- Display purge cache success -->
  <?php if ( isset( $_POST['ugtc_purge_cache'] ) ) : ?>
    <?php UGCache::purgeCache(); ?>
    <div class="notice notice-success is-dismissible ugtc-success-msg">
      <?php _e( 'Ultimate Guitar Tabs & Chords cache emptied successfully!', 'ug-tabs-chords' ); ?>
    </div>
  <?php endif; ?>

  <h1 class="ugtc-title">
    <?php _e( 'Ultimate Guitar Tabs & Chords', 'ug-tabs-chords' ); ?>
  </h1>

  <h2 class="ugtc-subtitle">
    <?php _e( 'Shortcode Generator', 'ug-tabs-chords' ); ?>
  </h2>
  <div class="ugtc-shortcode-generator">
    <form id="ugtc-shortcode-generator-form" class="ugtc-shortcode-generator-form" method="post" action="">
      <table class="form-table">
        <tr>
          <th scope="row">
            <?php _e( 'Artist', 'ug-tabs-chords' ); ?>
          </th>
          <td>
            <input type="text" name="ugtc_shortcode_artist" placeholder="Artist Name"
            <?php if ( isset( $artist ) ) echo 'value="' . $artist . '"'; ?>>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php _e( 'Limit results', 'ug-tabs-chords' ); ?>
          </th>
          <td>
            <input type="number" name="ugtc_shortcode_limit_results" placeholder="100" min="1" max="1000"
            <?php if ( isset( $limit ) ) echo 'value="' . $limit . '"'; ?>>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <input type="submit" class="button button-secondary" value="<?php _e( 'Generate Shortcode', 'ug-tabs-chords' ); ?>">
          </th>
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
              <?php echo 'Error: ' . $gen_shortcode->get_error_message(); ?>
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
    <?php _e( 'Ultimate Guitar Tabs & Chords uses the transient API of WordPress to cache data, resulting in much faster loading times. This might cause delay for your modifications to become visible, so be sure to empty the cache after modifications: ' ); ?>
  </p>
  <form id="ugtc-purge-cache-form" method="POST" action="">
    <input type="submit" name="ugtc_purge_cache" class="button button-secondary" value="<?php _e( 'Purge Cache', 'ug-tabs-chords' ); ?>">
  </form>
</div>
