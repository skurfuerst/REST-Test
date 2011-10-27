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
 * Builds a web request object from the raw HTTP information for POST, PUT and DELETE web requests.
 * This is needed currently, because FLOW3 itself does not support stream requests
 *
 * @FLOW3\Scope("singleton")
 */
class RestRequestBuilder {

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Utility\Environment
	 */
	protected $environment;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\MVC\Web\Routing\RouterInterface
	 */
	protected $router;

	/**
	 * Builds a web request object from the raw HTTP information
	 * Note: As opposed to the default implementation, this calls setArgumentsFromRawRequestData() *after* routing
	 *
	 * @return \TYPO3\FLOW3\MVC\Web\Request The web request as an object
	 */
	public function build() {
		$request = new \TYPO3\FLOW3\MVC\Web\Request();
		$request->setRequestUri($this->environment->getRequestUri());
		$request->setBaseUri($this->environment->getBaseUri());
		$request->setMethod($this->environment->getRequestMethod());

		$routesConfiguration = $this->configurationManager->getConfiguration(\TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_ROUTES);
		$this->router->setRoutesConfiguration($routesConfiguration);
		$this->router->route($request);
		$this->setArgumentsFromRawRequestData($request);

		return $request;
	}

	/**
	 * Maps stream data to the given request.
	 * If the requested content type is XML (header Content-Type contains the string "xml") the stream is
	 * mapped by parsing the XML with SimpleXML otherwise parse_str() is used to map URL encoded strings
	 * like (Foo%3DBar%26Bar%3DBaz)
	 *
	 * @param \TYPO3\FLOW3\MVC\Web\Request $request The web request which will contain the arguments
	 * @return void
	 */
	protected function setArgumentsFromRawRequestData(\TYPO3\FLOW3\MVC\Web\Request $request) {
		$arguments = $request->getArguments();
		$getArguments = $request->getRequestUri()->getArguments();
		$arguments = \TYPO3\FLOW3\Utility\Arrays::arrayMergeRecursiveOverrule($arguments, $getArguments);

		$postArguments = $this->environment->getRawPostArguments();
		$arguments = \TYPO3\FLOW3\Utility\Arrays::arrayMergeRecursiveOverrule($arguments, $postArguments);

		$uploadArguments = $this->environment->getUploadedFiles();
		$arguments = \TYPO3\FLOW3\Utility\Arrays::arrayMergeRecursiveOverrule($arguments, $uploadArguments);

		$inputStream = file_get_contents('php://input');
		$streamArguments = $this->parseRequestStream($inputStream);
		$arguments = \TYPO3\FLOW3\Utility\Arrays::arrayMergeRecursiveOverrule($arguments, $streamArguments);

		$request->setArguments($arguments);
	}

	/**
	 * @param string $inputStream
	 * @return array
	 */
	protected function parseRequestStream($inputStream) {
		if (strlen(trim($inputStream)) === 0) {
			return array();
		}
		$requestHeaders = $this->environment->getRawServerEnvironment();
		if (preg_match('/[\/\.]xml($|;|,)/', $requestHeaders['CONTENT_TYPE'])) {
			return $this->parseXmlString($inputStream);
		} elseif (preg_match('/[\/\.]json($|;|,)/', $requestHeaders['CONTENT_TYPE'])) {
			return $this->parseJsonString($inputStream);
		} else {
			return $this->parseUrlEncodedString($inputStream);
		}
	}

	/**
	 * Converts the given xml string to an array using SimpleXML
	 *
	 * @param string $input
	 * @return array
	 */
	protected function parseXmlString($input) {
		try {
			$xmlElement = new \SimpleXMLElement(urldecode($input));
		} catch (\Exception $e) {
			throw new \TYPO3\FLOW3\MVC\Exception\InvalidFormatException('The input stream could not be parsed as XML.', 1319670014);
		}
		return \TYPO3\FLOW3\Utility\Arrays::convertObjectToArray($xmlElement);
	}

	/**
	 * Converts the given json string to an array
	 *
	 * @param string $input
	 * @return array
	 */
	protected function parseJsonString($input) {
		$parsedArguments = json_decode($input, TRUE);
		if ($parsedArguments === NULL) {
			throw new \TYPO3\FLOW3\MVC\Exception\InvalidFormatException('The input stream could not be parsed as JSON. Error code: ' . json_last_error(), 1319670037);
		}
		return $parsedArguments;
	}

	/**
	 * Converts the given URL encoded string to an array
	 *
	 * @param $input
	 * @return array
	 */
	protected function parseUrlEncodedString($input) {
		$parsedArguments = array();
		parse_str($input, $parsedArguments);
		return $parsedArguments;
	}
}
?>