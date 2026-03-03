<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $product_description = null;

    #[ORM\Column]
    private ?int $price = null;

    /**
     * @var Collection<int, AlphaCamp>
     */
    #[ORM\ManyToMany(targetEntity: AlphaCamp::class, inversedBy: 'products')]
    private Collection $subCategory;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $images = null;

    #[ORM\Column]
    private ?int $stock = null;

    /**
     * @var Collection<int, AddProductHistory>
     */
    #[ORM\OneToMany(targetEntity: AddProductHistory::class, mappedBy: 'product')]
    private Collection $quantity;

    public function __construct()
    {
        $this->subCategory = new ArrayCollection();
        $this->quantity = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getProductDescription(): ?string
    {
        return $this->product_description;
    }

    public function setProductDescription(?string $product_description): static
    {
        $this->product_description = $product_description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, AlphaCamp>
     */
    public function getSubCategory(): Collection
    {
        return $this->subCategory;
    }

    public function addSubCategory(AlphaCamp $subCategory): static
    {
        if (!$this->subCategory->contains($subCategory)) {
            $this->subCategory->add($subCategory);
        }

        return $this;
    }

    public function removeSubCategory(AlphaCamp $subCategory): static
    {
        $this->subCategory->removeElement($subCategory);

        return $this;
    }

    public function getImages(): ?string
    {
        return $this->images;
    }

    public function setImages(?string $images): static
    {
        $this->images = $images;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * @return Collection<int, AddProductHistory>
     */
    public function getQuantity(): Collection
    {
        return $this->quantity;
    }

    public function addQuantity(AddProductHistory $quantity): static
    {
        if (!$this->quantity->contains($quantity)) {
            $this->quantity->add($quantity);
            $quantity->setProduct($this);
        }

        return $this;
    }

    public function removeQuantity(AddProductHistory $quantity): static
    {
        if ($this->quantity->removeElement($quantity)) {
            // set the owning side to null (unless already changed)
            if ($quantity->getProduct() === $this) {
                $quantity->setProduct(null);
            }
        }

        return $this;
    }
}
