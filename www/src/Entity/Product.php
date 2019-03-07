<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_of_creation;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_of_last_modification;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="products")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Images", mappedBy="product")
     * @ORM\OrderBy({"position"= "ASC"})
     */
    private $images;

    public function __construct()
    {
        $this->category = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDateOfCreation(): ?\DateTimeInterface
    {
        return $this->date_of_creation;
    }

    public function setDateOfCreation(\DateTimeInterface $date_of_creation): self
    {
        $this->date_of_creation = $date_of_creation;

        return $this;
    }

    public function getDateOfLastModification(): ?\DateTimeInterface
    {
        return $this->date_of_last_modification;
    }

    public function setDateOfLastModification(\DateTimeInterface $date_of_last_modification): self
    {
        $this->date_of_last_modification = $date_of_last_modification;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImages()
    {
        return $this->images;
    }
    /**
     * @param $images
     * @return $this
     */
    public function setImages($images)
    {
        $this->images[] = $images;

        return $this;
    }

    public function getMainImage()
    {
        $images= $this->images;
        $placeholder = '/assets/uploads/placeholder.jpg';
        foreach ($images as $image) {
            if ($image->getMain() == 1)

                return $image->getPath();
        }
        return $placeholder;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->category->contains($category)) {
            $this->category->removeElement($category);
        }

        return $this;
    }
}
