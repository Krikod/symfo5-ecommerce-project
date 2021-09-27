<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
		        ],
		        'divisor' => 100
	        ])
	        // Todo supprimer
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
// Todo contrainte type, taille, nombre...
	    $imageConstraints = [
		    new All([
			    new Image([
				    // todo réduire poids - et vérif constraints
				    'maxSize' => '5M'
			    ])
		    ])];
// todo rendre 1 image obligatoire / Validation !
//	    /** @var Product $product */
//	    if (!$product->getImageFilename()) {
//		    $imageConstraints[] = new NotNull([
//			    'message' => 'Please upload an image',
//		    ]);
//	    }

        $builder
        ->add( 'uploads', FileType::class, [
//        	'label' => 'Image(s) du produit',
        	'label' => false,
	        'multiple' => true,
//	        Mapped false car pas lié ici à la base de donnée
	        'mapped' => false,
	        'required' => false,
	        'constraints' => $imageConstraints,
	        'attr' => [
	        	'accept' => '.jpg, .jpeg, .png',
//		        'placeholder' => 'Sélectionnez une image'
	        ]
        ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
