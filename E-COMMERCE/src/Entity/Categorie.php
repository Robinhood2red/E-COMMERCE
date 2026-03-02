<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $name = null;

    /**
     * @var Collection<int, AlphaCamp>
     */
    #[ORM\OneToMany(targetEntity: AlphaCamp::class, mappedBy: 'category', orphanRemoval: true)]
    private Collection $alphaCamps;

    public function __construct()
    {
        $this->alphaCamps = new ArrayCollection();
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

    /**
     * @return Collection<int, AlphaCamp>
     */
    public function getAlphaCamps(): Collection
    {
        return $this->alphaCamps;
    }

    public function addAlphaCamp(AlphaCamp $alphaCamp): static
    {
        if (!$this->alphaCamps->contains($alphaCamp)) {
            $this->alphaCamps->add($alphaCamp);
            $alphaCamp->setCategory($this);
        }

        return $this;
    }

    public function removeAlphaCamp(AlphaCamp $alphaCamp): static
    {
        if ($this->alphaCamps->removeElement($alphaCamp)) {
            // set the owning side to null (unless already changed)
            if ($alphaCamp->getCategory() === $this) {
                $alphaCamp->setCategory(null);
            }
        }

        return $this;
    }
}
