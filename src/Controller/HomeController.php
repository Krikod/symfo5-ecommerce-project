<?php
/**
 * Created by PhpStorm.
 * User: krikod
 * Date: 22/02/21
 * Time: 11:30
 */

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController {
	/**
	 * @Route("/", name="homepage")
	 */
	public function homepage(ProductRepository $repo) {
		$products = $repo->findBy( [], [], 3);

		return $this->render('home.html.twig', [
			'products' => $products
		]);
	}
}