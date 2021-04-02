<?php

namespace App\Controller;

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
    public function add($id, ProductRepository $repo, SessionInterface $session): Response
    {
    	$product = $repo->find( $id);
    	if (!$product) {
    		throw $this->createNotFoundException("Le produit $id n'existe pas");
	    }
        $cart = $session->get( 'cart', []);

        if (array_key_exists( $id, $cart)) {
        	$cart[$id]++;
        } else {
        	$cart[$id] = 1;
        }

        $session->set( 'cart', $cart);
//	    $request->getSession()->remove( 'cart');
//	    dd( $request->getSession()->get( 'cart'));
//      dd( $session->get( 'cart'));

	    $this->addFlash( 'success', "Le produit a bien été ajouté au panier");


	    return $this->redirectToRoute( 'product_show', [
	    	'category_slug' => $product->getCategory()->getSlug(),
		    'slug' => $product->getSlug()
	    ]);
    }

	/**
	 * @Route("/panier", name="cart_show")
	 */
	public function show( SessionInterface $session, ProductRepository $repo ): Response {

		$detailedCart = [];
		$total = 0;
//		dd( $session->get( 'cart'));

		foreach ($session->get('cart', []) as $id => $qty) {
			$product = $repo->find( $id);
			$detailedCart[] = [
				'product' => $product,
				'qty' => $qty
			];
			$total += ($product->getPrice() * $qty);
		}
		dump( $detailedCart);

		return $this->render( 'cart/index.html.twig', [
			'items' => $detailedCart,
			'total' => $total
		]);
    }
}
