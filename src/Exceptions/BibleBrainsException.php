<?php

namespace CodeZone\Bible\Exceptions;

use Exception;

class BibleBrainsException extends \Exception {
	public function __construct( $message = "", $code = 0, Exception $previous = null ) {
		parent::__construct( $message, $code, $previous );
	}

	public function __toString() {
		return json_encode( $this->toArray() );
	}

	public function __toArray() {
		return $this->toArray();
	}

	public function toArray() {
		return [
			'error' => $this->message,
			'code'  => $this->code
		];
	}

	public function __toJSON() {
		return json_encode( $this->__toArray() );
	}
}
