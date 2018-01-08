<?php

use UGTC\Shortcode\UG_Shortcode;

/**
* Class Test_UG_Shortcode
*
* @package ug-tabs-chords
* @version  0.0.1
* @since 0.0.1
* @author Leo Toikka
*/
class Test_UG_Shortcode extends WP_UnitTestCase {

  /**
  * Init test case.
  */
  public function test_ug_shortcode_init() {
    $ug_shortcode = new UG_Shortcode;
    return $ug_shortcode;
  }

  /**
   * Test that the plugin shortcode is registered successfully.
   * @depends test_ug_shortcode_init
   *
   * @param UG_Shortcode $instance Tested instance created in test_ug_shortcode_init()
   */
  public function test_ug_shortcode_register( $instance ) {
    global $shortcode_tags;
    $instance->register_shortcode();

    $this->assertTrue( array_key_exists( 'ug-tabs-chords', $shortcode_tags ) );
  }

  /**
   * Test that the plugin shortcode is generated.
   * @depends test_ug_shortcode_init
   *
   * @param UG_Shortcode $instance Tested instance created in test_ug_shortcode_init()
   */
  public function test_ug_shortcode_generate( $instance ) {
    $instance->register_shortcode();

    // Test that providing no type causes an error
    $this->assertTrue( is_wp_error( UG_Shortcode::generate_shortcode( array( 'artist' => 'Crap Artist' ) ) ) );

    // Test with valid values
    $test_values = array(
      'artist' => 'Crap Artist',
      'type'   => 'ukulele'
    );
    $result = $instance->generate_shortcode( $test_values );
    $this->assertEquals( $result, '[ug-tabs-chords artist="' . $test_values['artist'] . '" type="' . $test_values['type'] . '"]' );
  }

  /**
   * Test that the plugin shortcode is created.
   * @depends test_ug_shortcode_init
   *
   * @param UG_Shortcode $instance Tested instance created in test_ug_shortcode_init()
   */
  public function test_ug_shortcode_create( $instance ) {
    // Test that an error is caused when the shortcode has not been registered
    remove_shortcode( 'ug-tabs-chords' );
    $this->assertTrue( is_wp_error( $instance->create_shortcode( array(), array() ) ) );

    $instance->register_shortcode();
    $this->assertTrue( shortcode_exists( 'ug-tabs-chords' ) );
  }

}
