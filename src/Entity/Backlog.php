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
 * @ORM\Entity(repositoryClass="App\Repository\BacklogRepository")
 * @ExclusionPolicy("all")
 */
class Backlog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Expose
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Expose
     */
    private $rank;

    /**
     * @ORM\Column(type="integer")
     * @Expose
     */
    private $estimatedTime;



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserStory", mappedBy="backlog")
     * @Expose
     */
    private $userStories;

    /**
     * @ORM\Column(type="integer")
     * @Expose
     */
    private $sprint;

    /**
     * @ORM\Column(type="string", length=255)
     * @Expose
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="backlog")
     */
    private $project;

    /**
     * @ORM\Column(type="string", length=255)
     * @Expose
     */
    private $startdate;

    public function __construct()
    {
        $this->userStories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setRank(int $rank): self
    {
        $this->rank = $rank;

        return $this;
    }

    public function getEstimatedTime(): ?int
    {
        return $this->estimatedTime;
    }

    public function setEstimatedTime(int $estimatedTime): self
    {
        $this->estimatedTime = $estimatedTime;

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
            $userStory->setBacklog($this);
        }

        return $this;
    }

    public function removeUserStory(UserStory $userStory): self
    {
        if ($this->userStories->contains($userStory)) {
            $this->userStories->removeElement($userStory);
            // set the owning side to null (unless already changed)
            if ($userStory->getBacklog() === $this) {
                $userStory->setBacklog(null);
            }
        }

        return $this;
    }

    public function getSprint(): ?int
    {
        return $this->sprint;
    }

    public function setSprint(int $sprint): self
    {
        $this->sprint = $sprint;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getStartdate(): ?string
    {
        return $this->startdate;
    }

    public function setStartdate(string $startdate): self
    {
        $this->startdate = $startdate;

        return $this;
    }
}
