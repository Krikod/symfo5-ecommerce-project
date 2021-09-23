<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ImagesRepository;
use App\Repository\ProductRepository;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
	/**
	 * ProductController constructor.
	 */
	public function __construct(ProductRepository $repo) {
		$this->repo = $repo;
	}

	/**
	 * @Route("/{slug}", name="product_category", priority=-1)
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

	    $nb_products = $category->getProducts()->count();

        return $this->render('product/category.html.twig', [
            'category' => $category,
	        'nb_products' => $nb_products
        ]);
    }

	/**
	 * @Route("/{category_slug}/{slug}", name="product_show", priority=-1)
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
	 * @Route("/admin/produit/{id}/editer", name="product_edit")
	 */
	public function edit($id, Request $request, ProductRepository $repo,
		EntityManagerInterface $em, SluggerInterface $slugger, UploaderHelper $uploaderHelper) {
		// todo voir problème slug qui change -> rediriger avec l'id et non le slug
		$product = $repo->find($id);
		$form = $this->createForm(ProductType::class, $product);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$product->setSlug(strtolower($slugger->slug($product->getName())));

			// Uploads management
			/** @var UploadedFile $uploadedFiles */
			$uploadedFiles = $form->get('uploads')->getData();

			foreach ($uploadedFiles as $uploadedFile) {
				$newFilename = $uploaderHelper->uploadProductImage($uploadedFile);

				$img = new Images();
				$img->setName($newFilename);
				$product->addImage($img);
			}

			$em->flush();

			$this->addFlash('success', 'Produit édité avec succès !');

			return $this->redirectToRoute('product_show', [
				'category_slug' => $product->getCategory()->getSlug(),
				'slug' => $product->getSlug()
			]);
		}

		$formView = $form->createView();

		return $this->render('product/edit.html.twig', [
			'product' => $product,
			'formView' => $formView
		]);
    }


	/**
	 * @Route("/admin/produit/ajouter", name="product_create")
	*/
	public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em, UploaderHelper $uploaderHelper) {

		$product = new Product();
		$form = $this->createForm(ProductType::class, $product);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$product->setSlug(strtolower($slugger->slug($product->getName())));

			/** @var UploadedFile $uploadedFiles */
			$uploadedFiles = $form->get('uploads')->getData();

			foreach ($uploadedFiles as $uploadedFile) {
				$newFilename = $uploaderHelper->uploadProductImage($uploadedFile);

				$img = new Images();
				$img->setName($newFilename);
				$product->addImage($img);
			}



			// On récupère les images
//			$images = $form->get('uploads')->getData();
//
//			// On boucle sur les images
//			foreach ($images as $image) {
//				// On génère un nom de fichier
//				$fichier = md5(uniqid()).'.'.$image->guessExtension();
//				// On copie le fichier dans le dossier uploads
//				$image->move(
//					$this->getParameter('images_directory'),
//					$fichier
//				);
				// On stocke l'img dans la bdd (son nom)
//				$img = new Images();
//				$img->setName($fichier);
//				$product->addImage($img);
//			}

			// On ne persiste pas $images car on a Cascade "persist" dns $images de Product()
			$em->persist($product);
			$em->flush();


			return $this->redirectToRoute('product_show', [
				'category_slug' => $product->getCategory()->getSlug(),
				'slug' => $product->getSlug()
			]);

		}

		$formView = $form->createView();

		return $this->render('product/create.html.twig', [
			'formView' => $formView
		]);
    }

//	/**
//	 * @Route("/admin/produit/{id}/supprimer", name="product_delete")
//	 */
//	public function delete(Request $request, Product $product, EntityManagerInterface $em): Response {
//		if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
//			$em->remove($product);
//			$em->flush();
//		}
//		return $this->redirectToRoute('homepage');
//    }

	/**
	 * @Route("/admin/produit/{id}/supprimer", name="product_delete")
	 */
	public function delete(int $id, Product $product, EntityManagerInterface $em) {

		if (!$this->repo->find($id)) {
			throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut être supprimé");
		}

		$em->remove($product);
		$em->flush();

		$this->addFlash( 'success', 'Produit supprimé !');

		return $this->redirectToRoute('homepage');
	}

	/**
	 * @Route("/admin/image/{id}/supprimer", name="product_image_delete", methods={"DELETE"})
	 */
	public function deleteImage(Request $request, Images $image, EntityManagerInterface $em) {
		$data = json_decode($request->getContent(), true);

		// On vérifie si le token est valide
		if ($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])) {
			// On récup le nom de l'image
			$nom = $image->getName();
			// On supprime le fichier
			unlink($this->getParameter('images_directory').'/'.$nom);

			// On supprime l'entrée de la bdd
			$em->remove($image);
			$em->flush();

			// On répond en Json
			return new JsonResponse(['success' => 1]);
		} else {
			return new JsonResponse(['error' => 'Token invalide'], 400);
		}
    }
}
