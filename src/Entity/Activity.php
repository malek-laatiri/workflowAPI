<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActivityRepository")
 * @ExclusionPolicy("all")
 */
class Activity
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
     * @ORM\OneToMany(targetEntity="App\Entity\UserStory", mappedBy="activity")
     */
    private $userStory;

    public function __construct()
    {
        $this->userStory = new ArrayCollection();
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

    /**
     * @return Collection|UserStory[]
     */
    public function getUserStory(): Collection
    {
        return $this->userStory;
    }

    public function addUserStory(UserStory $userStory): self
    {
        if (!$this->userStory->contains($userStory)) {
            $this->userStory[] = $userStory;
            $userStory->setActivity($this);
        }

        return $this;
    }

    public function removeUserStory(UserStory $userStory): self
    {
        if ($this->userStory->contains($userStory)) {
            $this->userStory->removeElement($userStory);
            // set the owning side to null (unless already changed)
            if ($userStory->getActivity() === $this) {
                $userStory->setActivity(null);
            }
        }

        return $this;
    }
}
