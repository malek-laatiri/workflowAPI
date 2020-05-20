<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LabelRepository")
 * @ExclusionPolicy("all")
 */
class Label
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Expose
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Expose
     */
    private $color;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserStory", mappedBy="label")
     */
    private $userstory;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="labels")
     */
    private $project;

    public function __construct()
    {
        $this->userstory = new ArrayCollection();
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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection|UserStory[]
     */
    public function getUserstory(): Collection
    {
        return $this->userstory;
    }

    public function addUserstory(UserStory $userstory): self
    {
        if (!$this->userstory->contains($userstory)) {
            $this->userstory[] = $userstory;
            $userstory->setLabel($this);
        }

        return $this;
    }

    public function removeUserstory(UserStory $userstory): self
    {
        if ($this->userstory->contains($userstory)) {
            $this->userstory->removeElement($userstory);
            // set the owning side to null (unless already changed)
            if ($userstory->getLabel() === $this) {
                $userstory->setLabel(null);
            }
        }

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }
}
