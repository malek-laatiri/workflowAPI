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
 * @ORM\Entity(repositoryClass="App\Repository\CommentsRepository")
 * @ExclusionPolicy("all")
 */
class Comments
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Expose
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Expose
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Expose
     */
    private $writtenAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserStory", inversedBy="comments")
     */
    private $userStory;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @Expose
     */
    private $writtenBy;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Files", mappedBy="comments")
     * @Expose
     */
    private $files;

    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->setWrittenAt(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getWrittenAt(): ?\DateTimeInterface
    {
        return $this->writtenAt;
    }

    public function setWrittenAt(\DateTimeInterface $writtenAt): self
    {
        $this->writtenAt = $writtenAt;

        return $this;
    }

    public function getUserStory(): ?UserStory
    {
        return $this->userStory;
    }

    public function setUserStory(?UserStory $userStory): self
    {
        $this->userStory = $userStory;

        return $this;
    }

    public function getWrittenBy(): ?User
    {
        return $this->writtenBy;
    }

    public function setWrittenBy(?User $writtenBy): self
    {
        $this->writtenBy = $writtenBy;

        return $this;
    }

    /**
     * @return Collection|Files[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(Files $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setComments($this);
        }

        return $this;
    }

    public function removeFile(Files $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
            // set the owning side to null (unless already changed)
            if ($file->getComments() === $this) {
                $file->setComments(null);
            }
        }

        return $this;
    }
}
