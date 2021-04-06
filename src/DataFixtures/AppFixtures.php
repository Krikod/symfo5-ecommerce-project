<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\User;
use Bezhanov\Faker\Provider\Commerce;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Liior\Faker\Prices;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
	protected $slugger;
	protected $encoder;

	public function __construct(SluggerInterface $slugger, UserPasswordEncoderInterface $encoder) {
		$this->slugger = $slugger;
		$this->encoder = $encoder;
	}

	public function load(ObjectManager $manager)
    {
    	$faker = Factory::create('fr_FR');
    	$faker->addProvider(new Prices($faker));
    	$faker->addProvider(new Commerce($faker));
    	$faker->addProvider(new PicsumPhotosProvider($faker));

    	$admin = new User();
    	$hash = $this->encoder->encodePassword( $admin, "password");
    	$admin->setFullName("Admin")
		    ->setRoles( ['ROLE_ADMIN'])
		    ->setEmail( "admin@gmail.com")
		    ->setPassword($hash);
    	$manager->persist( $admin);

    	// array for random relation with purchases
	    $users = [];

	    // Creation of Users
    	for ($u = 0; $u < 5; $u++) {
    		$user = new User();
    		$hash = $this->encoder->encodePassword( $user, "password");
    		$user->setEmail( "user-$u@gmail.com")
			    ->setFullName( $faker->name())
			    ->setPassword( $hash);

    		// We add each user in array $users:
		    $users[] = $user;

    		$manager->persist( $user);
	    }

	    // Creation of Categories
    	for ($c = 0; $c < 3; $c++) {
    		$category = new Category();
    		$category->setName($faker->department) // Bezhanov
		    ->setSlug(strtolower($this->slugger->slug($category->getName())));

    		$manager->persist($category);

    		// Creation of Products linked to Categories
    		for ($p = 0; $p < mt_rand(15, 20); $p++) {
    			$product = new Product();
    			$product->setName($faker->productName) // Bezhanov
			    ->setPrice($faker->price(4000, 20000)) // lib Liior
			    // construct -> slug à partir du nom du produit
			    ->setSlug(strtolower($this->slugger->slug($product->getName())))
			    ->setCategory($category)
				    ->setShortDescription($faker->paragraph())
				    // bluemmb, true=images différentes
				    ->setMainPicture($faker->imageUrl(400, 400, true));

    			$manager->persist($product);
		    }
	    }

	    // Creation of Purchases
	    for ($p = 0; $p < mt_rand(20, 40); $p++) {
    		$purchase = new Purchase();

    		$purchase->setFullName( $faker->name)
			    ->setAddress( $faker->streetAddress)
			    ->setPostalCode( $faker->postcode)
			    ->setCity( $faker->city)
			    ->setTotal( mt_rand(2000, 30000))
			    ->setUser( $faker->randomElement($users));

    		// 90% of purchases set to status PAID. The others are PENDING.
		    if ($faker->boolean(90)) {
		    	$purchase->setStatus( Purchase::STATUS_PAID);
		    }

    		$manager->persist( $purchase);
	    }

	    $manager->flush();
    }
}
