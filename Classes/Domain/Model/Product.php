<?php
namespace Wwwision\RestTest\Domain\Model;

/*                                                                        *
 * This script belongs to the FLOW3 package "Wwwision.RestTest".          *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * A Product
 *
 * @FLOW3\Scope("prototype")
 * @FLOW3\Entity
 */
class Product {

	/**
	 * @var string
	 * @FLOW3\Identity
	 * @FLOW3\Validate(type="NotEmpty")
	 */
	protected $name;

	/**
	 * @var float
	 */
	protected $price;

	/**
	 * @return string The product name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name The product name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param float $price The product price
	 */
	public function setPrice($price) {
		$this->price = (float)$price;
	}

	/**
	 * @return float The product price
	 */
	public function getPrice() {
		return $this->price;
	}
}
?>