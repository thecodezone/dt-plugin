<?php
 namespace DT\Plugin\Services;

use DT\Plugin\Psr\Http\Message\ResponseInterface;

interface ResponseRendererInterface {
	public function render( ResponseInterface $response );
}
