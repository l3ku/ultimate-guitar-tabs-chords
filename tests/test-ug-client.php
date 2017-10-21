<?php
/**
* Class TestUGClient
*
* @package ug-tabs-chords
* @version  1.0.0
* @since 1.0.0
* @author Leo Toikka
*/
class TestUGClient extends WP_UnitTestCase {

  /**
  * Init test case.
  */
  public function testUGClientInit() {
   $ug_client = new UGTabsChords\UGClient();
   return $ug_client;
  }

  /**
  * Test that the type 1 search parameter values are set correctly.
  *
  * @depends testUGClientInit
  */
  public function testUGClientType1( $client ) {
    // Test default settings
    $default_settings_reference = array( 200, 300 );
    $default_settings = $client->getType1();
    $this->assertEquals( $default_settings_reference, $default_settings );

    // Test setting type 1 with an invalid value (9999)
    $type_1_invalid_values = array ( 9999, 200 );
    $response = $client->setType1( $type_1_invalid_values );
    $this->assertEquals( true, is_wp_error( $response ) );

    // Test setting type 1 to allowed values
    $type_1_allowed_values = array( 500, 600, 700, 800, 900 );
    $response = $client->setType1( $type_1_allowed_values );
    $this->assertEquals( true, $response );
  }

  /**
  * Test that the type 2 search parameter values are set correctly.
  *
  * @depends testUGClientInit
  */
  public function testUGClientType2( $client ) {
    // Test default settings
    $default_settings_reference = array( 40000 );
    $default_settings = $client->getType2();
    $this->assertEquals( $default_settings_reference, $default_settings );

    // Test setting type 2 with an invalid value (9999)
    $type_2_invalid_values = array ( 20000, 30000, 9999 );
    $response = $client->setType2( $type_2_invalid_values );
    $this->assertEquals( true, is_wp_error( $response ) );

    // Test setting type 1 to allowed values
    $type_2_allowed_values = array( 20000, 30000, 40000 );
    $response = $client->setType2( $type_2_allowed_values );
    $this->assertEquals( true, $response );
  }

  /**
  * Test that the order search parameter values are set correctly.
  *
  * @depends testUGClientInit
  */
  public function testUGClientOrder($client ) {
    // Test default settings
    $default_settings_reference = 'myweight';
    $default_settings = $client->getOrder();
    $this->assertEquals( $default_settings_reference, $default_settings );

    // Test setting order with an invalid value ('test')
    $order_invalid_value ='test';
    $response = $client->setOrder( $order_invalid_value );
    $this->assertEquals( true, is_wp_error( $response ) );

    // Test setting order to allowed values
    $order_allowed_value = 'title_srt';
    $response = $client->setOrder( $order_allowed_value );
    $this->assertEquals( true, $response );
  }

  /**
  * Test that the ratings search parameter values are set correctly.
  *
  * @depends testUGClientInit
  */
  public function testUGClientRatings($client ) {
    // Test default settings
    $default_settings_reference = array( 1, 2, 3, 4, 5 );
    $default_settings = $client->getAllowedRatings();
    $this->assertEquals( $default_settings_reference, $default_settings );

    // Test setting ratings with an invalid value (6)
    $ratings_invalid_values = array( 1, 2, 4, 6 );
    $response = $client->setAllowedRatings( $ratings_invalid_values );
    $this->assertEquals( true, is_wp_error( $response ) );

    // Test setting order to allowed values
    $ratings_allowed_values = array( 1, 2 );
    $response = $client->setAllowedRatings( $ratings_allowed_values );
    $this->assertEquals( true, $response );
  }
}
