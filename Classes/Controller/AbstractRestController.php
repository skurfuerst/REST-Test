<?php
namespace Wwwision\RestTest\Controller;

/*                                                                        *
 * This script belongs to the FLOW3 package "Wwwision.RestTest".          *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Abstract REST controller that extends FLOW3s basic RestController and provides some useful helper functions
 *
 * @FLOW3\Scope("singleton")
 */
abstract class AbstractRestController extends \TYPO3\FLOW3\MVC\Controller\RestController {

	/**
	 * Pattern for the hypermedia type that this service produces.
	 * This will be set as Content-Type for all responses for formats != html
	 * The tokens '@package', '@subPackage', '@controller', '@action', '@format' and '@resourceName' will be replaced
	 *
	 * @var string
	 */
	protected $mediaTypePattern = 'application/@package.@resourceName+@format';

	/**
	 * This detects the format from the request header.
	 * The first format that is supported by this Controller will be returned.
	 *
	 * @return string
	 */
	protected function detectFormat() {
		$format = 'html';
		foreach ($this->environment->getAcceptedFormats() as $acceptedFormat) {
			if (array_search($acceptedFormat, $this->supportedFormats) !== FALSE) {
				$format = $acceptedFormat;
				break;
			}
		}
		if ($format !== 'html') {
			$contentType = $this->mediaTypePattern;
			$contentType = strtr(
				$contentType,
				array(
					'@package' => $this->request->getControllerPackageKey(),
					'@subPackage' => $this->request->getControllerSubpackageKey(),
					'@controller' => $this->request->getControllerName(),
					'@action' => $this->request->getControllerActionName(),
					'@resourceName' => $this->resourceArgumentName,
					'@format' => $format,
				)
			);
			$this->response->setHeader('Content-Type', strtolower($contentType));
		}
		return $format;
	}

	/**
	 * A custom redirect, that does not set action, controller, package and format arguments
	 *
	 * @param array $arguments
	 * @param integer $statusCode
	 * @return void
	 */
	protected function redirectToResource(array $arguments = array(), $statusCode = 204) {
		$this->uriBuilder->reset();
		$uri = $this->uriBuilder->uriFor(NULL, $arguments);
		$uri = $this->request->getBaseUri() . $uri;
		$this->response->setHeader('Location', $uri);
		$this->response->setStatus($statusCode);
		$this->response->setHeader('Accept', $this->environment->getHTTPAccept());
		$this->response->setContent('');
		throw new \TYPO3\FLOW3\MVC\Exception\StopActionException();
	}

	/**
	 * Throw a 409 (Conflict) status, if a (validation) error occurs
	 *
	 * @return void
	 */
	protected function errorAction() {
		if ($this->request->getFormat() === 'html') {
			return parent::errorAction();
		}
		$this->throwStatus(409);
	}

}

?>