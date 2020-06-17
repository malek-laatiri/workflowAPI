<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserStoryRepository")
 */
class UserStory
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
    private $subject;

    /**
     * @ORM\Column(type="string")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Priority", inversedBy="userStories")
     */
    private $priority;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Status", inversedBy="userStories")
     */
    private $status;

    /**
     * @ORM\Column(type="integer")
     */
    private $estimatedTime;

    /**
     * @ORM\Column(type="string")
     */
    private $dueDate;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tags;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Backlog", inversedBy="userStories")
     */
    private $backlog;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comments", mappedBy="userStory",cascade={"remove"})
     */
    private $comments;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userStories")
     */
    private $asignedTo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Activity", inversedBy="userStory")
     */
    private $activity;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\History", mappedBy="userstory",cascade={"remove"})
     */
    private $histories;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isComfirmed;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Label", inversedBy="userstory")
     */
    private $label;

    /**
     * @ORM\Column(type="integer")
     */
    private $progress;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProgressHistory", mappedBy="Userstory",cascade={"remove"})
     */
    private $progressHistories;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->histories = new ArrayCollection();
        $this->isComfirmed=false;
        $this->isVerified=false;
        $this->progress=0;
        $this->progressHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPriority(): ?Priority
    {
        return $this->priority;
    }

    public function setPriority(?Priority $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

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

    public function getDueDate(): ?string
    {
        return $this->dueDate;
    }

    public function setDueDate(string $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(string $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getBacklog(): ?Backlog
    {
        return $this->backlog;
    }

    public function setBacklog(?Backlog $backlog): self
    {
        $this->backlog = $backlog;

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUserStory($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUserStory() === $this) {
                $comment->setUserStory(null);
            }
        }

        return $this;
    }





    public function getAsignedTo(): ?User
    {
        return $this->asignedTo;
    }

    public function setAsignedTo(?User $asignedTo): self
    {
        $this->asignedTo = $asignedTo;

        return $this;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

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
            $history->setUserstory($this);
        }

        return $this;
    }

    public function removeHistory(History $history): self
    {
        if ($this->histories->contains($history)) {
            $this->histories->removeElement($history);
            // set the owning side to null (unless already changed)
            if ($history->getUserstory() === $this) {
                $history->setUserstory(null);
            }
        }

        return $this;
    }

    public function getIsComfirmed(): ?bool
    {
        return $this->isComfirmed;
    }

    public function setIsComfirmed(bool $isComfirmed): self
    {
        $this->isComfirmed = $isComfirmed;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getLabel(): ?Label
    {
        return $this->label;
    }

    public function setLabel(?Label $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getProgress(): ?int
    {
        return $this->progress;
    }

    public function setProgress(int $progress): self
    {
        $this->progress = $progress;

        return $this;
    }

    /**
     * @return Collection|ProgressHistory[]
     */
    public function getProgressHistories(): Collection
    {
        return $this->progressHistories;
    }

    public function addProgressHistory(ProgressHistory $progressHistory): self
    {
        if (!$this->progressHistories->contains($progressHistory)) {
            $this->progressHistories[] = $progressHistory;
            $progressHistory->setUserstory($this);
        }

        return $this;
    }

    public function removeProgressHistory(ProgressHistory $progressHistory): self
    {
        if ($this->progressHistories->contains($progressHistory)) {
            $this->progressHistories->removeElement($progressHistory);
            // set the owning side to null (unless already changed)
            if ($progressHistory->getUserstory() === $this) {
                $progressHistory->setUserstory(null);
            }
        }

        return $this;
    }
}
