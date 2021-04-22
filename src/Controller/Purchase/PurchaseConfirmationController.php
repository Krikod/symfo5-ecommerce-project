<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PurchaseConfirmationController
 * @package App\Controller\Purchase
 */
class PurchaseConfirmationController extends AbstractController {

	protected $cartService;
	protected $em;
	protected $persister;

	/**
	 * PurchaseConfirmationController constructor.
	 *
	 * @param CartService $cartService
	 * @param EntityManagerInterface $em
	 * @param PurchasePersister $persister
	 */
	public function __construct(CartService $cartService, EntityManagerInterface $em, PurchasePersister $persister) {
		$this->cartService = $cartService;
		$this->em = $em;
		$this->persister = $persister;
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 *
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

		$purchase = $form->getData();

		$this->persister->storePurchase($purchase);

		return $this->redirectToRoute('purchase_payment_form', [
			'id' => $purchase->getId()
		]);
	}
}