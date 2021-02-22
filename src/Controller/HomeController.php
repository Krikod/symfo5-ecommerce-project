<?php
/**
 * Created by PhpStorm.
 * User: krikod
 * Date: 22/02/21
 * Time: 11:30
 */

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {
	/**
	 * @Route("/", name="homepage")
	 */
	public function homepage() {
		return $this->render('home.html.twig');
	}
}