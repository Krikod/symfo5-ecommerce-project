<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartService {

	protected $session;
	protected $repo;

	public function __construct(SessionInterface $session, ProductRepository $repo) {
		$this->session = $session;
		$this->repo = $repo;
	}

	public function add(int $id) {
		$cart = $this->session->get('cart', []);
		if (array_key_exists( $id, $cart)) {
			$cart[$id]++;
		} else {
			$cart[$id] = 1;
		}
		$this->session->set( 'cart', $cart);
		// $request->getSession()->remove( 'cart');
	}

	public function remove($id) {
		$cart = $this->session->get( 'cart', []);
		unset( $cart[$id]);
		$this->session->set( 'cart', $cart);
	}

	public function getTotal(  ) : int {
		$total = 0;
		foreach ($this->session->get( 'cart') as $id => $qty) {
			$product = $this->repo->find( $id);
			if (!$product) {
				continue;
			}
			$total += $product->getPrice() * $qty;
		}
		return $total;
	}

	public function getDetailedCartItems(  ) : array {
		$detailedCart = [];
		foreach ($this->session->get( 'cart') as $id => $qty) {
			$product = $this->repo->find( $id);
			if (!$product) {
				continue;
			}
			$detailedCart[] = new CartItem( $product, $qty);

		}
		return $detailedCart;
	}

}