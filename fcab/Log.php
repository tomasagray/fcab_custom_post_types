<?php

namespace fcab;

use JsonException;

class Log {

	public const LOG_FILE = "log.txt";

	public static function i( ...$data ): void {
		try {
			$log_output = json_encode( $data, JSON_THROW_ON_ERROR ) . "\n";
			$written    = file_put_contents( self::LOG_FILE, $log_output, FILE_APPEND );
			var_dump( $written );
			if ( $written === false ) {
				echo "<h1>Could not write to log file!</h1><p>Log file location: " . self::LOG_FILE . "</p>";
				var_dump( $data );
			}
		} catch ( JsonException $e ) {
			echo '<h1>ERROR: Could not write data to log</h1> <p>';
			var_dump( $e );
			echo '</p>';
		}
	}
}
