<?php

namespace CodeZone\Bible\Exceptions;

use Exception;

/**
 * Class BibleBrainsException
 *
 * Custom exception class for BibleBrains application
 */
class BibleBrainsException extends \Exception {
	/**
	 * Constructor of the class.
	 *
	 * @param string $message The error message.
	 * @param int $code The error code.
	 * @param \Exception $previous The previous exception (if any).
	 */
	public function __construct( $message = "", $code = 0, Exception $previous = null ) {
		parent::__construct( $message, $code, $previous );
	}

	/**
	 * Returns the object as a JSON string representation.
	 *
	 * @return string The JSON string representation of the object.
	 */
	public function __toString() {
		return json_encode( $this->toArray() );
	}

	/**
	 * Returns the object as an array representation.
	 *
	 * @return array The array representation of the object.
	 */
	public function __toArray() {
		return $this->toArray();
	}

	/**
	 * Returns the object as an array representation.
	 *
	 * @return array The array representation of the object.
	 */
	public function toArray() {
		return [
			'error' => $this->message,
			'code'  => $this->code
		];
	}

	/**
	 * Returns the object as a JSON string representation.
	 *
	 * @return string The JSON string representation of the object.
	 */
	public function __toJSON() {
		return json_encode( $this->__toArray() );
	}
}
