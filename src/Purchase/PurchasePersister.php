<?php
/**
 * Created by PhpStorm.
 * User: krikod
 * Date: 15/04/21
 * Time: 14:57
 */

namespace App\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class PurchasePersister
 * @package App\Purchase
 */
class PurchasePersister {

	protected $security;
	protected $cartService;
	protected $em;

	/**
	 * PurchasePersister constructor.
	 *
	 * @param Security $security
	 * @param CartService $cartService
	 * @param EntityManagerInterface $em
	 */
	public function __construct(Security $security, CartService $cartService, EntityManagerInterface $em) {
		$this->security = $security;
		$this->cartService = $cartService;
		$this->em = $em;
	}

	/**
	 * @param Purchase $purchase
	 */
	public function storePurchase(Purchase $purchase) {

		$purchase->setUser($this->security->getUser())
		         ->setPurchasedAt(new \DateTime())
		         ->setTotal($this->cartService->getTotal());

		$this->em->persist($purchase);

		foreach ($this->cartService->getDetailedCartItems() as $cart_item) {
			$purchaseItem = new PurchaseItem();
			$purchaseItem->setPurchase( $purchase)
			             ->setProduct( $cart_item->product)
			             ->setProductName( $cart_item->product->getName())
			             ->setQuantity( $cart_item->qty)
			             ->setTotal( $cart_item->getTotal())
			             ->setProductPrice( $cart_item->product->getPrice());

			$this->em->persist($purchaseItem);
		}

		$this->em->flush();
	}
}