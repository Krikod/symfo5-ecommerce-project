<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
	/**
	 * @Route("/panier/ajouter/{id}", name="cart_add",
	 *     requirements={"id":"\d+"})
	 */
    public function add($id, ProductRepository $repo, CartService $cart_service, Request $request): Response
    {
    	$product = $repo->find( $id);

    	if (!$product) {
    		throw $this->createNotFoundException("Le produit $id n'existe pas");
	    }

	    $cart_service->add( $id);

	    $this->addFlash( 'success', "Produit ajouté au panier !");

	    if ($request->query->get( 'returnToCart')) {
	    	return $this->redirectToRoute( 'cart_show');
	    }

	    return $this->redirectToRoute( 'product_show', [
	    	'category_slug' => $product->getCategory()->getSlug(),
		    'slug' => $product->getSlug()
	    ]);
    }

	/**
	 * @Route("/panier", name="cart_show")
	 */
	public function show(CartService $cart_service ): Response {

		$detailedCart = $cart_service->getDetailedCartItems();
		$total = $cart_service->getTotal();

		return $this->render( 'cart/index.html.twig', [
			'items' => $detailedCart,
			'total' => $total
		]);
    }

	/**
	 * @Route("/panier/supprimer/{id}", name="cart_delete", requirements={"id":"\d+"})
	 */
	public function delete($id, ProductRepository $repo, CartService $cart_service) {
		if (!$repo->find( $id)) {
			throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut être supprimé");
		}
		$cart_service->remove($id);
		$this->addFlash( 'success', 'Produit supprimé du panier !');
		return $this->redirectToRoute( 'cart_show');
	}

	/**
	 * @Route("/panier/decrementer/{id}", name="cart_decrement", requirements={"id":"\d+"})
	 */
	public function decrement($id, CartService $cart_service, ProductRepository $repo  ) {
		if (!$repo->find( $id)) {
			throw $this->createNotFoundException("Le produit $id n'existe pas donc ne peut être retiré du panier");
		}

		$cart_service->decrement($id);
		$this->addFlash( 'success', 'Produit retiré du panier !');
		return $this->redirectToRoute( 'cart_show');
	}
}
