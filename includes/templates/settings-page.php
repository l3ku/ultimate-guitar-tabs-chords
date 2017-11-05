<?php
/**
* File for displaying settings page content.
*
* @package ug-tabs-chords
*/
?>

<div class="wrap">
  <?php settings_errors(); ?>
  <h1><?php _e( 'Settings', 'ug-tabs-chords' ); ?></h1>
  <form method="post" action="options.php" class="ug-settings-form">
    <?php settings_fields( 'ugtc-settings-group' ); ?>
    <?php do_settings_sections( 'ug_tabs_chords_settings' ); ?>
    <?php submit_button( __( 'Save Changes' ), 'primary', 'btnSubmit'); ?>
  </form>
</div>
