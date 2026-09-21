<?php

if( isset( $_POST[ 'Submit' ] ) ) {
	// Get and normalize user input.
	$target = trim( $_POST[ 'ip' ] ?? '' );

	/*
	 * SECURITY FIX:
	 * Only allow a valid IP address. This blocks shell operators such as
	 * &&, ;, |, and other command-injection input.
	 */
	if( !filter_var( $target, FILTER_VALIDATE_IP ) ) {
		$html .= "<pre>Invalid IP address. Please enter a valid IPv4 or IPv6 address.</pre>";
	} else {
		/*
		 * SECURITY FIX:
		 * Escape the validated value before adding it to the system command.
		 */
		$safeTarget = escapeshellarg( $target );

		// Determine OS and execute the ping command using safe input.
		if( stristr( php_uname( 's' ), 'Windows NT' ) ) {
			$cmd = shell_exec( 'ping ' . $safeTarget );
		} else {
			$cmd = shell_exec( 'ping -c 4 ' . $safeTarget );
		}

		// Feedback for the end user.
		$html .= "<pre>{$cmd}</pre>";
	}
}

?>