<?php
/**
* File for displaying settings page content.
*
* @package ug-tabs-chords
*/

defined( 'ABSPATH' ) or die( 'Access Denied!' );
?>

<div class="wrap">
  <?php settings_errors(); ?>
  <h1><?php _e( 'Search Settings', 'ug-tabs-chords' ); ?></h1>
  <form method="post" action="options.php" class="ug-settings-form">
    <?php settings_fields( 'ugtc-search-settings-group' ); ?>
    <?php do_settings_sections( 'ug_tabs_chords_search_settings' ); ?>
    <?php submit_button( __( 'Save Changes' ), 'primary', 'btnSubmit'); ?>
  </form>
</div>
