<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CartService
 * @package App\Cart
 */
class CartService {

	/**
	 * @var SessionInterface
	 */
	protected $session;

	/**
	 * @var ProductRepository
	 */
	protected $repo;

	/**
	 * CartService constructor.
	 *
	 * @param SessionInterface $session
	 * @param ProductRepository $repo
	 */
	public function __construct(SessionInterface $session, ProductRepository $repo) {
		$this->session = $session;
		$this->repo = $repo;
	}

	/**
	 * @return array
	 */
	public function getCart() : array {
		return $this->session->get('cart', []);
	}

	/**
	 * @param array $cart
	 */
	public function saveCart(array $cart) {
		$this->session->set('cart', $cart);
	}

	public function empty(  ) {
		$this->saveCart([]);
	}

	/**
	 * @param int $id
	 */
	public function add(int $id) {
		$cart = $this->getCart();

		if (!array_key_exists($id, $cart)) {
			$cart[$id] = 0;
		}
		$cart[$id]++;

		$this->saveCart($cart);
	}

	/**
	 * @param int $id
	 */
	public function remove(int $id) {
		$cart = $this->getCart();
		unset( $cart[$id]);
		$this->saveCart($cart);
	}

	/**
	 * @param int $id
	 */
	public function decrement(int $id) {
		$cart = $this->getCart();

		if (!array_key_exists( $id, $cart)) {
			return;
		}

		if ($cart[$id] === 1) {
			$this->remove( $id);
			return;
		} else {
			$cart[$id]--;
			$this->saveCart($cart);
		}
	}

	/**
	 * @return int
	 */
	public function getTotal() : int {
		$total = 0;

		foreach ($this->getCart() as $id => $qty) {
			$product = $this->repo->find( $id);

			if (!$product) {
				continue;
			}

			$total += $product->getPrice() * $qty;
		}

		return $total;
	}

	/**
	 * @return array
	 */
	public function getDetailedCartItems() : array {
		$detailedCart = [];

		foreach ($this->getCart() as $id => $qty) {
			$product = $this->repo->find( $id);

			if (!$product) {
				continue;
			}

			$detailedCart[] = new CartItem( $product, $qty);
		}

		return $detailedCart;
	}
}