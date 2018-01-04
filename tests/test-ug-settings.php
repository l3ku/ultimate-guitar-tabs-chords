<?php
/**
* Class Test_UG_Settings
*
* @package ug-tabs-chords
* @version  0.0.1
* @since 0.0.1
* @author Leo Toikka
*/
class Test_UG_Settings extends WP_UnitTestCase {

  /**
  * Init test case.
  */
  public function test_ug_settings_init() {
   $ug_settings = new UG_Settings();
   return $ug_settings;
  }

  /**
   * Test that all settings are registered successfully.
   * @depends test_ug_settings_init
   *
   * @param UG_Settings $instance Tested instance created in test_ug_settings_init()
   */
  public function test_ug_settings_register( $instance ) {
    // WP globals used to validate against
    global $new_whitelist_options, $wp_registered_settings;

    $instance->register_settings();

    // Test that the search settings group is registered along with its settings
    $this->assertTrue( array_key_exists( 'ugtc-search-settings-group', $new_whitelist_options ) );
    $this->assertTrue( array_key_exists( 'ugtc_search_entry_types', $wp_registered_settings ) );
    $this->assertTrue( array_key_exists( 'ugtc_search_entry_lengths', $wp_registered_settings ) );
    $this->assertTrue( array_key_exists( 'ugtc_search_sort_option', $wp_registered_settings ) );
    $this->assertTrue( array_key_exists( 'ugtc_search_ratings', $wp_registered_settings ) );
  }

  /**
   * Test that all settings sections and fields are added successfully.
   * @depends test_ug_settings_init
   *
   * @param UG_Settings $instance Tested instance created in test_ug_settings_init()
   */
  public function test_ug_settings_sections( $instance ) {
    // WP globals used to validate against
    global $wp_settings_sections, $wp_settings_fields;

    $instance->add_settings_sections();

    // Test that the settings sections are added successfully
    $this->assertTrue( array_key_exists( 'ug_tabs_chords_search_settings', $wp_settings_sections ) );
    $this->assertTrue( array_key_exists( 'ugtc-search-settings-section', $wp_settings_sections['ug_tabs_chords_search_settings'] ) );

    // Test that the settings fields are added successfully
    $this->assertTrue( array_key_exists( 'ug_tabs_chords_search_settings', $wp_settings_fields ) );
    $this->assertTrue( array_key_exists( 'ugtc-search-settings-section', $wp_settings_fields['ug_tabs_chords_search_settings'] ) );
    $this->assertTrue( array_key_exists( 'search-entry-types', $wp_settings_fields['ug_tabs_chords_search_settings']['ugtc-search-settings-section'] ) );
    $this->assertTrue( array_key_exists( 'search-entry-lengths', $wp_settings_fields['ug_tabs_chords_search_settings']['ugtc-search-settings-section'] ) );
    $this->assertTrue( array_key_exists( 'search-sort-option', $wp_settings_fields['ug_tabs_chords_search_settings']['ugtc-search-settings-section'] ) );
    $this->assertTrue( array_key_exists( 'search-ratings', $wp_settings_fields['ug_tabs_chords_search_settings']['ugtc-search-settings-section'] ) );
  }
}
