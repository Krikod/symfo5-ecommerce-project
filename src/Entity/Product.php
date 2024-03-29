<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom est obligatoire")
     * @Assert\Length(min=3, minMessage="Le nom doit avoir {{ limit }} caractères minimum")
     * @Assert\Length(max=50, maxMessage="Le nom ne peut pas dépasser {{ limit }} caractères")
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Le prix est obligatoire")
     * @Assert\Positive(message="Le prix doit être supérieur à zéro")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

//    /**
//     * @ORM\Column(type="string", length=255)
//     */
//    private $mainPicture;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Une description courte est obligatoire")
     * @Assert\Length(min=30, minMessage="La description doit avoir {{ limit }} caractères minimum")
     * @Assert\Length(max=1000, maxMessage="La description ne peut pas dépasser {{ limit }} caractères")
     */
    private $shortDescription;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     * @Assert\NotBlank(message="La catégorie est obligatoire")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=PurchaseItem::class, mappedBy="product", cascade={"remove"})
     */
    private $purchaseItems;

    /**
     * @ORM\OneToMany(targetEntity=Images::class, mappedBy="product", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $images;

    public function __construct()
    {
        $this->purchaseItems = new ArrayCollection();
        $this->images = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

//    public function getMainPicture(): ?string
//    {
//        return $this->mainPicture;
//    }
//
//    public function setMainPicture(?string $mainPicture): self
//    {
//        $this->mainPicture = $mainPicture;
//
//        return $this;
//    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|PurchaseItem[]
     */
    public function getPurchaseItems(): Collection
    {
        return $this->purchaseItems;
    }

    public function addPurchaseItem(PurchaseItem $purchaseItem): self
    {
        if (!$this->purchaseItems->contains($purchaseItem)) {
            $this->purchaseItems[] = $purchaseItem;
            $purchaseItem->setProduct($this);
        }

        return $this;
    }

    public function removePurchaseItem(PurchaseItem $purchaseItem): self
    {
        if ($this->purchaseItems->removeElement($purchaseItem)) {
            // set the owning side to null (unless already changed)
            if ($purchaseItem->getProduct() === $this) {
                $purchaseItem->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Images[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
    	if ($this->images->contains( $image)) {
		    $this->images->removeElement($image);
		    // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
	            $image->setProduct( null );
            }
	    }
	    return $this;

//        if ($this->images->removeElement($image)) {
//            // set the owning side to null (unless already changed)
//            if ($image->getProduct() === $this) {
//                $image->setProduct(null);
//            }
//        }
//
//        return $this;
    }
}
