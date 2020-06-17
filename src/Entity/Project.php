<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Swagger\Annotations as SWG;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 * @ExclusionPolicy("all")
 */
class Project
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @SWG\Property(description="The unique identifier of the user.")
     * @Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @SWG\Property(description="The unique identifier of the user.")
     * @Expose
     *
     */
    private $name;

    /**
     * @ORM\Column(type="string",name="startDate")
     * @SWG\Property(description="The unique identifier of the user.")
     * @Expose
     */
    private $startDate;

    /**
     * @ORM\Column(type="string",name="dueDate")
     * @SWG\Property(description="The unique identifier of the user.")
     * @Expose
     */
    private $dueDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="projects")
     * @SWG\Property(description="The unique identifier of the user.")
     * @Expose
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Backlog", mappedBy="project",cascade={"remove"})
     * @Expose
     */
    private $backlog;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="projectMembers")
     * @Expose
     */
    private $Team;

    /**
     * @ORM\Column(type="boolean")
     * @Expose
     */
    private $done;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Label", mappedBy="project",cascade={"remove"})
     */
    private $labels;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Status", mappedBy="project",cascade={"remove"})
     */
    private $statuses;


    public function __construct()
    {
        $this->backlog = new ArrayCollection();
        $this->Team = new ArrayCollection();
        $this->done=false;
        $this->labels = new ArrayCollection();
        $this->statuses = new ArrayCollection();
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

    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    public function setStartDate(string $startDate): self
    {
        $this->startDate = $startDate;

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


    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return Collection|Backlog[]
     */
    public function getBacklog(): Collection
    {
        return $this->backlog;
    }

    public function addBacklog(Backlog $backlog): self
    {
        if (!$this->backlog->contains($backlog)) {
            $this->backlog[] = $backlog;
            $backlog->setProject($this);
        }

        return $this;
    }

    public function removeBacklog(Backlog $backlog): self
    {
        if ($this->backlog->contains($backlog)) {
            $this->backlog->removeElement($backlog);
            // set the owning side to null (unless already changed)
            if ($backlog->getProject() === $this) {
                $backlog->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getTeam(): Collection
    {
        return $this->Team;
    }

    public function addTeam(User $team): self
    {
        if (!$this->Team->contains($team)) {
            $this->Team[] = $team;
        }

        return $this;
    }

    public function removeTeam(User $team): self
    {
        if ($this->Team->contains($team)) {
            $this->Team->removeElement($team);
        }

        return $this;
    }

    public function getDone(): ?bool
    {
        return $this->done;
    }

    public function setDone(bool $done): self
    {
        $this->done = $done;

        return $this;
    }

    /**
     * @return Collection|Label[]
     */
    public function getLabels(): Collection
    {
        return $this->labels;
    }

    public function addLabel(Label $label): self
    {
        if (!$this->labels->contains($label)) {
            $this->labels[] = $label;
            $label->setProject($this);
        }

        return $this;
    }

    public function removeLabel(Label $label): self
    {
        if ($this->labels->contains($label)) {
            $this->labels->removeElement($label);
            // set the owning side to null (unless already changed)
            if ($label->getProject() === $this) {
                $label->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Status[]
     */
    public function getStatuses(): Collection
    {
        return $this->statuses;
    }

    public function addStatus(Status $status): self
    {
        if (!$this->statuses->contains($status)) {
            $this->statuses[] = $status;
            $status->setProject($this);
        }

        return $this;
    }

    public function removeStatus(Status $status): self
    {
        if ($this->statuses->contains($status)) {
            $this->statuses->removeElement($status);
            // set the owning side to null (unless already changed)
            if ($status->getProject() === $this) {
                $status->setProject(null);
            }
        }

        return $this;
    }


}
