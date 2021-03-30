<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
	/**
	 * @Route("/panier/ajouter/{id}", name="cart_add")
	 */
    public function add($id, Request $request): Response
    {
        $cart = $request->getSession()->get( 'cart', []);

        if (array_key_exists( $id, $cart)) {
        	$cart[$id]++;
        } else {
        	$cart[$id] = 1;
        }

        $request->getSession()->set( 'cart', $cart);
//	    $request->getSession()->remove( 'cart');
//	    dd( $request->getSession()->get( 'cart'));
    }
}
