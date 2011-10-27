<?php
namespace Wwwision\RestTest;

/*                                                                        *
 * This script belongs to the FLOW3 package "Wwwision.RestTest".          *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * A request handler which can handle stream web requests.
 *
 * @FLOW3\Scope("singleton")
 */
class RestRequestHandler implements \TYPO3\FLOW3\MVC\RequestHandlerInterface {

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Utility\Environment
	 */
	protected $environment;

	/**
	 * @FLOW3\Inject
	 * @var \Wwwision\RestTest\RestRequestBuilder
	 */
	protected $requestBuilder;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\MVC\Dispatcher
	 */
	protected $dispatcher;

	/**
	 * @var \TYPO3\FLOW3\MVC\Web\Request
	 */
	protected $request;

	/**
	 * This request handler can handle POST, PUT and DELETE web request that provide a Content-Type header
	 *
	 * @return boolean
	 */
	public function canHandleRequest() {
		if (FLOW3_SAPITYPE !== 'Web') {
			return FALSE;
		}
		if (!in_array($this->environment->getRequestMethod(), array('POST', 'PUT', 'DELETE'))) {
			return FALSE;
		}
		$requestHeaders = $this->environment->getRawServerEnvironment();
		return isset($requestHeaders['CONTENT_TYPE']);
	}

	/**
	 * Returns the priority - how eager the handler is to actually handle the
	 * request.
	 *
	 * @return integer The priority of the request handler.
	 */
	public function getPriority() {
		return 200;
	}

	/**
	 * Handles the web request. The response will automatically be sent to the client.
	 *
	 * @return void
	 */
	public function handleRequest() {
		$this->request = $this->requestBuilder->build();
		$response = new \TYPO3\FLOW3\MVC\Web\Response();

		$this->dispatcher->dispatch($this->request, $response);
		$response->send();
	}

	/**
	 * Returns the top level request built by this request handler.
	 *
	 * In most cases the dispatcher or other parts of the request-response chain
	 * should be preferred for retrieving the current request, because sub requests
	 * or simulated requests are built later in the process.
	 *
	 * If, however, the original top level request is wanted, this is the right
	 * method for getting it.
	 *
	 * @return \TYPO3\FLOW3\MVC\Web\Request The originally built web request
	 */
	public function getRequest() {
		return $this->request;
	}

}
?>