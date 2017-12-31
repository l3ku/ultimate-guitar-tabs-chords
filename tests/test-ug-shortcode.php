<?php
/**
* Class TestUGShortcode
*
* @package ug-tabs-chords
* @version  0.0.1
* @since 0.0.1
* @author Leo Toikka
*/
class TestUGShortcode extends WP_UnitTestCase {

  /**
  * Init test case.
  */
  public function testUGShortcodeInit() {
   $ug_shortcode = new UGShortcode();
   return $ug_shortcode;
  }

  /**
   * Test that the plugin shortcode is registered successfully.
   * @depends testUGShortcodeInit
   *
   * @param UGShortcode $instance Tested instance created in testUGShortcodeInit()
   */
  public function testUGShortcodeRegister( $instance ) {
    global $shortcode_tags;
    $instance->registerShortcode();

    $this->assertTrue( array_key_exists( 'ug-tabs-chords', $shortcode_tags ) );
  }

  /**
   * Test that the plugin shortcode is generated.
   * @depends testUGShortcodeInit
   *
   * @param UGShortcode $instance Tested instance created in testUGShortcodeInit()
   */
  public function testUGShortcodeGenerate( $instance ) {
    $instance->registerShortcode();

    // Test that providing no artist at all causes an error
    $this->assertTrue( is_wp_error( $instance->generateShortcode( '', 0 ) ) );

    // Test with valid values
    $test_artist = 'Test';
    $test_limit = 50;
    $result = $instance->generateShortcode( $test_artist, $test_limit );
    $this->assertEquals( $result, '[ug-tabs-chords artist="Test" limit="50"]' );
  }

  /**
   * Test that the plugin shortcode is created.
   * @depends testUGShortcodeInit
   *
   * @param UGShortcode $instance Tested instance created in testUGShortcodeInit()
   */
  public function testUGShortcodeCreate( $instance ) {
    // Test that an error is caused when the shortcode has not been registered
    remove_shortcode( 'ug-tabs-chords' );
    $this->assertTrue( is_wp_error( $instance->createShortcode( array(), array() ) ) );

    // TODO: Test with valid values
  }

}
