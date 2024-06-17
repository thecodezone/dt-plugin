<?php
 namespace DT\Plugin\Services;

interface RouteInterface {
	public function as_uri( $uri ): \DT\Plugin\Services\Route;
	public function with_middleware( $middleware ): \DT\Plugin\Services\Route;
	public function with_request( $request ): \DT\Plugin\Services\Route;
	public function with_routes( callable $register_routes ): \DT\Plugin\Services\Route;
	public function from_route_file( $file ): \DT\Plugin\Services\Route;
	public function from_file( $file ): \DT\Plugin\Services\Route;
	public function dispatch(): \DT\Plugin\Services\Route;
    public function render(): \DT\Plugin\Services\Route;
}
