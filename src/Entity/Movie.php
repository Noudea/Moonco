<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MovieRepository::class)
 */
class Movie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
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
    private $sypnosis;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $category = [];
    

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $actors;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $link;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $picture;

    /**
     * @ORM\ManyToOne(targetEntity=Server::class, inversedBy="movies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $relation;

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

    public function getSypnosis(): ?string
    {
        return $this->sypnosis;
    }

    public function setSypnosis(string $sypnosis): self
    {
        $this->sypnosis = $sypnosis;

        return $this;
    }

    public function getCategory(): ?array
    {
        return $this->category;
    }

    public function setCategory(?array $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCategoryInput(): string
    {
        //transformer tableau en string
        return implode(' ', $this->category);
    }

    public function setCategoryInput(?string $categoryInput): self
    {
        //transformer le string en tableau dans slug
        if ($categoryInput) {
            $this->category = explode(' ', $categoryInput);
        }
        return $this;
    }


    public function getActors(): ?string
    {
        return $this->actors;
    }

    public function setActors(?string $actors): self
    {
        $this->actors = $actors;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getRelation(): ?Server
    {
        return $this->relation;
    }

    public function setRelation(?Server $relation): self
    {
        $this->relation = $relation;

        return $this;
    }
}
