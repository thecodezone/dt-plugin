<?php

namespace CodeZone\Bible\Conditions;

use CodeZone\Bible\CodeZone\Router\Conditions\Condition;

/**
 * Class Backend
 *
 * Represents a backend condition for determining if the current path is an admin path.
 */
class Backend implements Condition {

	/**
	 * Determines if the current path is an admin path.
	 *
	 * @return bool True if the current path is an admin path.
	 */
	public function test(): bool {
		return is_admin();
	}
}
