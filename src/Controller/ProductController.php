<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
	/**
	 * @Route("/{slug}", name="product_category")
	 *
	 * @param $slug
	 * @param CategoryRepository $repository
	 *
	 * @return Response
	 */
    public function category($slug, CategoryRepository $repository): Response
    {
    	$category = $repository->findOneBy([
    		'slug' => $slug
	    ]);

    	if (!$category) {
    		throw $this->createNotFoundException("La catégorie demandée n'existe pas.");
	    }

        return $this->render('product/category.html.twig', [
            'category' => $category,
        ]);
    }

	/**
	 * @Route("/{category_slug}/{slug}", name="product_show")
	 *
	 * @param $slug
	 * @param ProductRepository $repository
	 *
	 * @return Response
	 */
	public function show($slug, ProductRepository $repository): Response {
		$product = $repository->findOneBy([
			'slug' => $slug
		]);

		if (!$product) {
			throw $this->createNotFoundException("Le produit demandé n'existe pas.");
		}

		return $this->render('product/show.html.twig', [
			'product' => $product
		]);
    }

	/**
	 * @Route("/admin/produit/ajouter", name="product_create")
	*/
	public function create(
		FormFactoryInterface $factory,
		Request $request,
		SluggerInterface $slugger,
		EntityManagerInterface $em) {

		$builder = $factory->createBuilder(FormType::class, null, [
			'data_class' => Product::class
		]);

		$builder
			->add('name', TextType::class, [
				'label' => 'Nom du produit',
				'attr' => [
					'placeholder' => 'Tapez le nom du produit'
				]
			])
		->add('shortDescription', TextareaType::class, [
			'label' => 'Description courte',
			'attr' => [
				'placeholder' => 'Tapez une description courte du produit'
			]
		])
			->add('price', MoneyType::class, [
				'label' => 'Prix',
				'attr' => [
					'placeholder' => 'Tapez le prix du produit'
				]
			])
			->add('mainPicture', UrlType::class, [
				'label' => 'Image du produit',
				'attr' => ['placeholder' => 'Tapez l\'URL de l\'image']
			])
			->add('category', EntityType::class, [
				'label' => 'Catégorie',
				'placeholder' => '-- Choisir une catégorie --',
				'class' => Category::class,
				'choice_label' => function(Category $category) {
				return strtoupper($category->getName());
				}
			]);

		$form = $builder->getForm();

		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			$product = $form->getData();
			$product->setSlug(strtolower($slugger->slug($product->getName())));

			$em->persist($product);
			$em->flush();

			dump($product);
		}

		$formView = $form->createView();

		return $this->render('product/create.html.twig', [
			'formView' => $formView
		]);
    }
}
