<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CartController
 * @package App\Controller
 */
class CartController extends AbstractController
{
	/**
	 * @var ProductRepository
	 */
	protected $repo;

	/**
	 * @var CartService
	 */
	protected $cart_service;

	/**
	 * CartController constructor.
	 *
	 * @param ProductRepository $repo
	 * @param CartService $cart_service
	 */
	public function __construct(ProductRepository $repo, CartService $cart_service) {
		$this->repo = $repo;
		$this->cart_service = $cart_service;
	}

	/**
	 * @param $id
	 * @param Request $request
	 *
	 * @return Response
	 *
 	 * @Route("/panier/ajouter/{id}", name="cart_add", requirements={"id":"\d+"})
	 */
    public function add(int $id, Request $request): Response {
    	$product = $this->repo->find( $id);

    	if (!$product) {
    		throw $this->createNotFoundException("Le produit $id n'existe pas");
	    }

	    $this->cart_service->add( $id);

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
	 * @return Response
	 *
	 * @Route("/panier", name="cart_show")
	 */
	public function show(): Response {
		$detailedCart = $this->cart_service->getDetailedCartItems();
		$total = $this->cart_service->getTotal();

		return $this->render( 'cart/index.html.twig', [
			'items' => $detailedCart,
			'total' => $total
		]);
    }

	/**
	 * @param $id
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 *
	 * @Route("/panier/supprimer/{id}", name="cart_delete", requirements={"id":"\d+"})
	 */
	public function delete(int $id) {
		if (!$this->repo->find($id)) {
			throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut être supprimé");
		}

		$this->cart_service->remove($id);
		$this->addFlash( 'success', 'Produit supprimé du panier !');

		return $this->redirectToRoute( 'cart_show');
	}

	/**
	 * @param int $id
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 *
	 * @Route("/panier/decrementer/{id}", name="cart_decrement", requirements={"id":"\d+"})
	 */
	public function decrement(int $id) {
		if (!$this->repo->find( $id)) {
			throw $this->createNotFoundException("Le produit $id n'existe pas donc ne peut être retiré du panier");
		}

		$this->cart_service->decrement($id);
		$this->addFlash( 'success', 'Produit retiré du panier !');

		return $this->redirectToRoute( 'cart_show');
	}
}
