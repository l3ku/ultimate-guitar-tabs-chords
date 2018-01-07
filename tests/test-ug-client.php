<?php
/**
* Class Test_UG_Client
*
* @package ug-tabs-chords
* @version  0.0.1
* @since 0.0.1
* @author Leo Toikka
*/
class Test_UG_Client extends WP_UnitTestCase {

  /**
  * Init test case.
  */
  public function test_ug_client_init() {
   $ug_client = new UG_Client();
   return $ug_client;
  }

  /**
  * Test that the type parameter values are set correctly.
  *
  * @depends test_ug_client_init
  */
  public function test_ug_client_type( $client ) {
    // Test default settings
    $default_settings_reference = 'tabs';
    $default_settings = $client->get_type();
    $this->assertEquals( $default_settings_reference, $default_settings );

    // Test setting type with an invalid value
    $type_invalid_value = 'pork_chops';
    $response = $client->set_type( $type_invalid_value );
    $this->assertEquals( true, is_wp_error( $response ) );

    // Test setting type to allowed value
    $type_allowed_value = 'ukulele';
    $response = $client->set_type( $type_allowed_value );
    $this->assertEquals( true, $response );
  }

  /**
  * Test that the order search parameter values are set correctly.
  *
  * @depends test_ug_client_init
  */
  public function test_ug_client_order($client ) {
    // Test default settings
    $default_settings_reference = 'title_srt';
    $default_settings = $client->get_order();
    $this->assertEquals( $default_settings_reference, $default_settings );

    // Test setting order with an invalid value ('test')
    $order_invalid_value ='test';
    $response = $client->set_order( $order_invalid_value );
    $this->assertEquals( true, is_wp_error( $response ) );

    // Test setting order to allowed values
    $order_allowed_value = 'date';
    $response = $client->set_order( $order_allowed_value );
    $this->assertEquals( true, $response );
  }

  /**
  * Test that the ratings search parameter values are set correctly.
  *
  * @depends test_ug_client_init
  */
  public function test_ug_client_ratings($client ) {
    // Test default settings
    $default_settings_reference = array( 1, 2, 3, 4, 5 );
    $default_settings = $client->get_allowed_ratings();
    $this->assertEquals( $default_settings_reference, $default_settings );

    // Test setting ratings with an invalid value (6)
    $ratings_invalid_values = array( 1, 2, 4, 6 );
    $response = $client->set_allowed_ratings( $ratings_invalid_values );
    $this->assertEquals( true, is_wp_error( $response ) );

    // Test setting order to allowed values
    $ratings_allowed_values = array( 1, 2 );
    $response = $client->set_allowed_ratings( $ratings_allowed_values );
    $this->assertEquals( true, $response );
  }
}
