<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ExclusionPolicy("all")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Expose
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="createdBy")
     */
    private $projects;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserStory", mappedBy="asignedTo")
     */
    private $userStories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comments", mappedBy="writtenBy")
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Project", mappedBy="Team")
     */
    private $projectMembers;

    /**
     * @ORM\Column(type="boolean")
     * @Expose
     */
    private $privilege;


    public function __construct()
    {
        parent::__construct();
        $this->userStories = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->projectMembers = new ArrayCollection();
        $this->privilege=0;

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setCreatedBy($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            // set the owning side to null (unless already changed)
            if ($project->getCreatedBy() === $this) {
                $project->setCreatedBy(null);
            }
        }

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
            $userStory->setAsignedTo($this);
        }

        return $this;
    }

    public function removeUserStory(UserStory $userStory): self
    {
        if ($this->userStories->contains($userStory)) {
            $this->userStories->removeElement($userStory);
            // set the owning side to null (unless already changed)
            if ($userStory->getAsignedTo() === $this) {
                $userStory->setAsignedTo(null);
            }
        }

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
            $comment->setWrittenBy($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getWrittenBy() === $this) {
                $comment->setWrittenBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjectMembers(): Collection
    {
        return $this->projectMembers;
    }

    public function addProjectMember(Project $projectMember): self
    {
        if (!$this->projectMembers->contains($projectMember)) {
            $this->projectMembers[] = $projectMember;
            $projectMember->addTeam($this);
        }

        return $this;
    }

    public function removeProjectMember(Project $projectMember): self
    {
        if ($this->projectMembers->contains($projectMember)) {
            $this->projectMembers->removeElement($projectMember);
            $projectMember->removeTeam($this);
        }

        return $this;
    }

    public function getPrivilege(): ?bool
    {
        return $this->privilege;
    }

    public function setPrivilege(bool $privilege): self
    {
        $this->privilege = $privilege;

        return $this;
    }


}
