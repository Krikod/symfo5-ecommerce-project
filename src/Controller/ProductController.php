<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
	/**
	 * @Route("/{slug}", name="product_category")
	 */
    public function category($slug, CategoryRepository $repository): Response
    {
    	$category = $repository->findOneBy([
    		'slug' => $slug
	    ]);

        return $this->render('product/category.html.twig', [
            'category' => $category,
        ]);
    }
}
