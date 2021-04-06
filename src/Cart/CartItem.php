<?php

namespace App\Cart;

use App\Entity\Product;

/**
 * Class CartItem
 * @package App\Cart
 */
class CartItem {
	/**
	 * @var Product
	 */
	public $product;

	/**
	 * @var int
	 */
	public $qty;

	/**
	 * CartItem constructor.
	 *
	 * @param Product $product
	 * @param int $qty
	 */
	public function __construct(Product $product, int $qty) {
		$this->product = $product;
		$this->qty = $qty;
	}

	/**
	 * @return int
	 */
	public function getTotal() : int {
		return $this->product->getPrice() * $this->qty;
	}
}