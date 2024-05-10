<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'cate', targetEntity: SanPham::class)]
    private Collection $sanPhams;

    #[ORM\Column(length: 255)]
    private ?string $photo = null;

    public function __construct()
    {
        $this->sanPhams = new ArrayCollection();
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
     * @return Collection<int, SanPham>
     */
    public function getSanPhams(): Collection
    {
        return $this->sanPhams;
    }

    public function addSanPham(SanPham $sanPham): static
    {
        if (!$this->sanPhams->contains($sanPham)) {
            $this->sanPhams->add($sanPham);
            $sanPham->setCate($this);
        }

        return $this;
    }

    public function removeSanPham(SanPham $sanPham): static
    {
        if ($this->sanPhams->removeElement($sanPham)) {
            // set the owning side to null (unless already changed)
            if ($sanPham->getCate() === $this) {
                $sanPham->setCate(null);
            }
        }

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }
}
