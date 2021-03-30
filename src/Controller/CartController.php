<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
	/**
	 * @Route("/panier/ajouter/{id}", name="cart_add",
	 *     requirements={"id":"\d+"})
	 */
    public function add($id, Request $request, ProductRepository $repo): Response
    {
    	$product = $repo->find( $id);
    	if (!$product) {
    		throw $this->createNotFoundException("Le produit $id n'existe pas");
	    }
        $cart = $request->getSession()->get( 'cart', []);

        if (array_key_exists( $id, $cart)) {
        	$cart[$id]++;
        } else {
        	$cart[$id] = 1;
        }

        $request->getSession()->set( 'cart', $cart);
//	    $request->getSession()->remove( 'cart');
//	    dd( $request->getSession()->get( 'cart'));

	    return $this->redirectToRoute( 'product_show', [
	    	'category_slug' => $product->getCategory()->getSlug(),
		    'slug' => $product->getSlug()
	    ])
    }
}
