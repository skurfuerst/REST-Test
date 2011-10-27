<?php
namespace Wwwision\RestTest\Controller;

/*                                                                        *
 * This script belongs to the FLOW3 package "Wwwision.RestTest".          *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;
use Wwwision\RestTest\Domain\Model\Product;

/**
 * Restful Product controller for the Wwwision.RestTest package
 *
 * @FLOW3\Scope("singleton")
 */
class ProductController extends AbstractRestController {

	/**
	 * @FLOW3\Inject
	 * @var \Wwwision\RestTest\Domain\Repository\ProductRepository
	 */
	protected $productRepository;

	/**
	 * @var string
	 * @see \TYPO3\FLOW3\MVC\Controller\RestController
	 */
	protected $resourceArgumentName = 'product';

	/**
	 * @var array
	 * @see AbstractRestController::detectFormat()
	 */
	protected $supportedFormats = array('xml', 'json', 'html');

	/**
	 * @return void
	 */
	public function listAction() {
		$this->view->assign('products', $this->productRepository->findAll());
	}

	/**
	 * @param \Wwwision\RestTest\Domain\Model\Product $product
	 * @return void
	 */
	public function showAction(Product $product) {
		$this->view->assign('product', $product);
	}

	/**
	 * @param \Wwwision\RestTest\Domain\Model\Product $product
	 * @return void
	 */
	public function createAction(Product $product) {
		$this->productRepository->add($product);
		$this->redirectToResource(array('product' => $product), 202);
	}

	/**
	 * @param \Wwwision\RestTest\Domain\Model\Product $product
	 * @return void
	 */
	public function updateAction(Product $product) {
		$this->productRepository->update($product);
		$this->redirectToResource(array('product' => $product));
	}

	/**
	 * @param \Wwwision\RestTest\Domain\Model\Product $product
	 * @return void
	 */
	public function deleteAction(Product $product) {
		$this->productRepository->remove($product);
		$this->redirectToResource();
	}

}

?>