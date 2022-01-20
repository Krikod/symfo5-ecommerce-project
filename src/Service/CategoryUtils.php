<?php

namespace App\Service;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\Criteria;

class CategoryUtils {

	CONST CAT_NAME_LAST = 'AUTRE';

	private $repo;

	public function __construct( CategoryRepository $repo) {
		$this->repo = $repo;

	}

	/**
	 * Liste toutes les catégories
	 * @return \App\Entity\Category[]
	 */
	public function catListAll(  ) {
		$categories = $this->repo->findAll();
		return $categories;
	}

	public function catListAllButLast(  ) {
		$categories_but_last = $this->repo
			->matching( Criteria::create()
			                    ->where( Criteria::expr()
			                                     ->notIn('name', [SELF::CAT_NAME_LAST])));
		return $categories_but_last;
	}

	public function catOnlyLast(  ) {
		$category_last = $this->repo
			->matching( Criteria::create()
			                    ->where( Criteria::expr()
			                                     ->eq( 'name', SELF::CAT_NAME_LAST)));
		return $category_last;

	}
}