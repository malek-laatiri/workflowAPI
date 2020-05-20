<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HistoryRepository")
 * @ExclusionPolicy("all")
 */
class History
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Expose
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Status", inversedBy="histories")
     * @Expose
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     * @Expose
     */
    private $modifiedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserStory", inversedBy="histories")
     */
    private $userstory;

    /**
     * History constructor.
     */
    public function __construct()
    {
        $this->setModifiedAt(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getModifiedAt(): ?\DateTimeInterface
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(\DateTimeInterface $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    public function getUserstory(): ?UserStory
    {
        return $this->userstory;
    }

    public function setUserstory(?UserStory $userstory): self
    {
        $this->userstory = $userstory;

        return $this;
    }

}
