<?php
namespace Wwwision\RestTest\Domain\Repository;

/*                                                                        *
 * This script belongs to the FLOW3 package "Wwwision.RestTest".          *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * A repository for products
 *
 * @FLOW3\Scope("singleton")
 */
class ProductRepository extends \TYPO3\FLOW3\Persistence\Repository {

	/**
	 * @var array
	 */
	protected $defaultOrderings = array('name' => \TYPO3\FLOW3\Persistence\QueryInterface::ORDER_ASCENDING);

}
?>