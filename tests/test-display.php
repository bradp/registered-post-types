<?php

class QATPT_Display_Test extends WP_UnitTestCase {

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'QATPT_Display') );
	}

	function test_class_access() {
		$this->assertTrue( query_all_the_post_types()->display instanceof QATPT_Display );
	}
}
