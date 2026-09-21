<?php

header( "X-XSS-Protection: 0" );

if( array_key_exists( "name", $_GET ) && $_GET[ 'name' ] != NULL ) {
	// Get input.
	$name = $_GET[ 'name' ];

	/*
	 * SECURITY FIX:
	 * Encode untrusted user input before outputting it in HTML.
	 * This prevents reflected HTML/JavaScript from executing in the browser.
	 */
	$safeName = htmlspecialchars( $name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

	// Feedback for the end user.
	$html .= "<pre>Hello {$safeName}</pre>";
}

?>