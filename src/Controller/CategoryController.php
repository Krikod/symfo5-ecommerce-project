<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController {

	/**
	 * @Route("/admin/categorie/ajouter", name="category_create")
	 */
	public function create (EntityManagerInterface $em, SluggerInterface $slugger, Request $request) : Response {

		$category = new Category();

		$form = $this->createForm(CategoryType::class, $category);

		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			$category->setSlug(strtolower($slugger->slug($category->getName())));
			$em->persist($category);
			$em->flush();

			return $this->redirectToRoute('homepage');
		}

		$formView = $form->createView();

		return $this->render('category/create.html.twig', [
			'formView' => $formView
		]);

	}
}