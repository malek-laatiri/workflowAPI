<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StatusRepository")
 * @ExclusionPolicy("all")
 */
class Status
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
     * @ORM\OneToMany(targetEntity="App\Entity\UserStory", mappedBy="status")
     * @Expose
     */
    private $userStories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\History", mappedBy="status")
     */
    private $histories;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="statuses")
     * @Expose
     */
    private $project;

    /**
     * @ORM\Column(type="string", length=255)
     * @Expose
     */
    private $role;

    public function __construct()
    {
        $this->userStories = new ArrayCollection();
        $this->histories = new ArrayCollection();
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
    public function getUserStories(): Collection
    {
        return $this->userStories;
    }

    public function addUserStory(UserStory $userStory): self
    {
        if (!$this->userStories->contains($userStory)) {
            $this->userStories[] = $userStory;
            $userStory->setStatus($this);
        }

        return $this;
    }

    public function removeUserStory(UserStory $userStory): self
    {
        if ($this->userStories->contains($userStory)) {
            $this->userStories->removeElement($userStory);
            // set the owning side to null (unless already changed)
            if ($userStory->getStatus() === $this) {
                $userStory->setStatus(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|History[]
     */
    public function getHistories(): Collection
    {
        return $this->histories;
    }

    public function addHistory(History $history): self
    {
        if (!$this->histories->contains($history)) {
            $this->histories[] = $history;
            $history->setStatus($this);
        }

        return $this;
    }

    public function removeHistory(History $history): self
    {
        if ($this->histories->contains($history)) {
            $this->histories->removeElement($history);
            // set the owning side to null (unless already changed)
            if ($history->getStatus() === $this) {
                $history->setStatus(null);
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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }
}
