<?php
 namespace DT\Plugin\Services;

interface RewritesInterface {
	public function flush();
	public function has_latest();
	public function exists( $rule, $query = null );
	public function add( $regex, $query );
	public function apply();
	public function sync();
	public function refresh();
	public function dump();
}
