<?php

namespace App\Controller\Purchase;


use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PurchaseConfirmationController extends AbstractController {

	protected $cartService;
	protected $em;

	public function __construct(CartService $cartService, EntityManagerInterface $em) {
		$this->cartService = $cartService;
		$this->em = $em;
	}

	/**
	 * @Route("/commande/confirmation", name="purchase_confirm")
	 * @IsGranted("ROLE_USER", message="Connectez-vous pour confirmer votre commande")
	 */
	public function confirm(Request $request) : Response {
		$form = $this->createForm(CartConfirmationType::class);
		$form->handleRequest($request);

		if (!$form->isSubmitted()) {
			$this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation');
			return $this->redirectToRoute('cart_show');
		}

		$cartItems = $this->cartService->getDetailedCartItems();

//		if (count($cartItems) === 0) {
//			$this->addFlash('warning', 'Il est impossible de confirmer une commande avec un panier vide');
//			return $this->redirectToRoute('cart_show');
//		}

		$user = $this->getUser();

		$purchase = $form->getData();
		$purchase->setUser($user)->setPurchasedAt(new \DateTime());
		$this->em->persist( $purchase);

//		dd( $purchase);
		foreach ( $this->cartService->getDetailedCartItems() as $cart_item ) {
			$purchaseItem = new PurchaseItem();
			$purchaseItem->setPurchase( $purchase)
			             ->setProduct( $cart_item->product)
			             ->setProductName( $cart_item->product->getName())
			             ->setQuantity( $cart_item->qty)
			             ->setTotal( $cart_item->getTotal())
			             ->setProductPrice( $cart_item->product->getPrice());

//			$total += $cart_item->getTotal();

			$this->em->persist( $purchaseItem);
		}

		$purchase->setTotal($this->cartService->getTotal());
		$this->em->flush();
		$this->addFlash( 'success', 'La commande a bien été enregistrée');
		return $this->redirectToRoute( 'purchases_index');
	}
}