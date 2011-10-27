<?php
namespace Wwwision\RestTest\ViewHelpers;

/*                                                                        *
 * This script belongs to the FLOW3 package "Wwwision.RestTest".          *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * View helper which renders the identifier (usually UUID) of the given object
 *
 * = Examples =
 *
 * <code title="Example">
 * {someObject -> x:identity()}
 * </code>
 * <output>
 * 92fb193b-7156-4c12-8b0f-9a19fa71c354
 * (depending on the object)
 * </output>
 */
class IdentifierViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * Render the identifier of the given child nodes
	 *
	 * @return string identifier (usually UUID).
	 */
	public function render() {
		$object = $this->renderChildren();
		if (!is_object($object)) {
			throw new \TYPO3\Fluid\Core\ViewHelper\Exception('Expected object, got "' . gettype($object) . '"', 1319470107);
		}
		$identifier = $this->persistenceManager->getIdentifierByObject($object);
		return (string)$identifier;
	}
}

?>
