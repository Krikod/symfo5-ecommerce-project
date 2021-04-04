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
    public function add($id, ProductRepository $repo, CartService $cart_service): Response
    {
    	$product = $repo->find( $id);

    	if (!$product) {
    		throw $this->createNotFoundException("Le produit $id n'existe pas");
	    }

	    $cart_service->add( $id);

	    $this->addFlash( 'success', "Le produit a bien été ajouté au panier");


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
}
