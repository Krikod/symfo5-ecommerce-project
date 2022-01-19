<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController {

	protected  $repo;

	public function __construct(CategoryRepository $repo) {
		$this->repo = $repo;
	}

	/**
	 * @Route("/admin/categorie/ajouter", name="category_create")
	 */
	public function create (EntityManagerInterface $em, SluggerInterface $slugger, Request $request) : Response {

		$category = new Category();
		// TODO verif nom cat n'existe pas

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
	public function edit($id, CategoryRepository $repo, EntityManagerInterface $em, Request $request) : Response {
		$category = $repo->find($id);

		// TODO verif nom cat n'existe pas
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
	public function list(CategoryRepository $repo, EntityManagerInterface $em) : Response {
		// Récupérer les catégories
		$categories = $repo->findAll();

		return $this->render('category/list.html.twig', [
			'categories' => $categories,
		]);
	}

	/**
	 * @Route("/admin/categorie/{id}/supprimer", name="category_delete", requirements={"id": "\d+"})
	 */
	public function delete(int $id, CategoryRepository $repo, Category $category, SluggerInterface $slugger, EntityManagerInterface $em) {

		$category_other = $repo->findOneBy(array('name'=> 'Autre'));
// Todo éventuellement demander confirmation pour suppression de catégorie
//		$nb_products = $category->getProducts()->count();

//		$syntax;
//		$singular = 'produit est attaché';
//		$plural = 'produits sont attachés';

//		if ($nb_products > 1) {
//			$syntax = $plural;
//		} elseif ($nb_products === 1) {
//			$syntax = $singular;
//		}

		$category = $repo->find($id);

		if (!$category) {
			throw $this->createNotFoundException("La catégorie $id n'existe pas et ne peut pas être supprimée");
		}
//		elseif ($nb_products) {
//			$this->addFlash("warning", "Attention, $nb_products $syntax à cette catégorie !");
//		}

		if (!$category_other) {
			$category_other = new Category();
			$category_other->setName('Autre');
			$category_other->setSlug(strtolower($slugger->slug($category_other->getName())));
			$em->persist($category_other);
		}

		foreach ($category->getProducts() as $product) {
			$category->removeProduct($product);
			$product->setCategory($category_other);
		}

		$em->remove($category);
		$em->flush();
		$this->addFlash( 'success', 'Catégorie supprimée !');

		return $this->redirectToRoute('categories');
	}
}