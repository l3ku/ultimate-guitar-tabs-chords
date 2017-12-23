<?php
/**
* File for displaying plugin info page content.
*
* @package ug-tabs-chords
*/
require_once( plugin_dir_path( __FILE__ ) . '../ug-shortcode.php' );

// Generate shortcode on form submission, sanitize input and disallow HTML
if ( isset( $_POST['ugtc_shortcode_artist'] ) && isset( $_POST['ugtc_shortcode_limit_results'] ) ) {
  $artist = trim( wp_kses( $_POST['ugtc_shortcode_artist'], array() ) );
  $limit = trim( wp_kses( $_POST['ugtc_shortcode_limit_results'], array() ) );
  $gen_shortcode = UGShortCode::generateShortCode( $artist, $limit );
}
?>

<h1 class="ugtc-title">
  <?php _e( 'Ultimate Guitar Tabs & Chords', 'ug-tabs-chords' ); ?>
</h1>

<h2 class="ugtc-subtitle">
  <?php _e( 'Instructions', 'ug-tabs-chords' ); ?>
</h2>
<p class="ugtc-instructions">
  <ol class="ugtc-instructions-list">
    <li class="ugtc-instructions-list-entry">
      <h4>
        <?php _e( 'Specify your content search settings from <em>UGTC->Search Settings</em> (e.g. whether to show tabs and/or chords and how to order them)', 'ug-tabs-chords' ); ?>
      </h4>
    </li>
    <li class="ugtc-instructions-list-entry">
      <h4>
        <?php _e( 'Add the plugin shortcode to the desired page or post. You can generate the plugin shortcode by using the shortcode generator below:', 'ug-tabs-chords' ); ?>
      </h4>
    </li>
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
    <li class="ugtc-instructions-list-entry">
      <h4>
        <?php _e( 'Done!', 'ug-tabs-chords' ); ?>
      </h4>
    </li>
  </ol>
</p>

<h2 class="ugtc-subtitle">
  <?php _e( 'Development', 'ug-tabs-chords' ); ?>
</h2>
<p class="ugtc-development">
  <?php _e( 'Have any feature requests or willing to participate in the development of this project? Ultimate Guitar Tabs & Chords is open source software, feel free to track and even participate in its development in GitHub. If you have any bug reports or other feedback, please open a GitHub issue.', 'ug-tabs-chords' ); ?>
</p>
