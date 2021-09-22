<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController {
	/**
	 * CategoryController constructor.
	 */
	public function __construct(CategoryRepository $repo) {
		$this->repo = $repo;
	}

	/**
	 * @Route("/admin/categorie/ajouter", name="category_create")
	 */
	public function create (EntityManagerInterface $em, SluggerInterface $slugger, Request $request) : Response {

		$category = new Category();

		$form = $this->createForm(CategoryType::class, $category);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$category->setSlug(strtolower($slugger->slug($category->getName())));
			$em->persist($category);
			$em->flush();

			$this->addFlash( 'success', 'Catégorie créée !');

			return $this->redirectToRoute('categories');
		}

		$formView = $form->createView();

		return $this->render('category/create.html.twig', [
			'formView' => $formView
		]);
	}

	/**
	 * @Route("/admin/categorie/{id}/editer", name="category_edit")
	 */
	public function edit($id, EntityManagerInterface $em, Request $request, CategoryRepository $repo) : Response {
		$category = $repo->find($id);

		$form = $this->createForm(CategoryType::class, $category);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			$em->flush();
			$this->addFlash( 'success', 'Catégorie éditée !');

			return $this->redirectToRoute('categories');
		}

		$formView = $form->createView();

		return $this->render('category/edit.html.twig', [
			'category' => $category,
			'formView' => $formView
		]);
	}

	/**
	 * @Route("admin/categories", name="categories"))
	 */
	public function list(EntityManagerInterface $em, CategoryRepository $repo) : Response {
		// Récupérer les catégories
		$categories = $repo->findAll();


		return $this->render('category/list.html.twig', [
			'categories' => $categories,

		]);
	}
	/**
	 * @Route("/admin/category/{id}/supprimer", name="category_delete")
	 */
	public function delete(int $id, Category $category, EntityManagerInterface $em) {
		if (!$this->repo->find($id)) {
			throw $this->createNotFoundException("La catégorie $id n'existe pas et ne peut pas être supprimée");
		}
		$em->remove($category);
		$em->flush();
		$this->addFlash( 'success', 'Catégorie supprimée !');
// TODO Mettre category 0 à produit sur Preremove ??

		return $this->redirectToRoute('categories');
	}
}