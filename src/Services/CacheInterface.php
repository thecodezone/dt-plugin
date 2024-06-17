<?php
 namespace DT\Plugin\Services;

interface CacheInterface {
	public function scope_key( string $key ): string;
	public function get( string $key );
	public function set( string $key, $value, int $expiration = 60 * 60 );
	public function delete( string $key );
	public function flush();
}
