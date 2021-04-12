<?php

namespace App\Controller\Purchase;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PurchasesListController extends AbstractController
{
	/**
	 * @Route("/commandes", name="commandes_index")
	 * @IsGranted("ROLE_USER", message="Vous devez être connecté(e) pour accéder à vos commandes.")
	 */
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user) {
        	return $this->redirectToRoute( 'homepage');
        }

        return $this->render( 'purchase/index.html.twig', [
        	'purchases' => $user->getPurchases()
        ]);
    }
}
