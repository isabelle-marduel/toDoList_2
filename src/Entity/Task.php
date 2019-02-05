<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le contenu est obligatoire.")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $status = "en cours";

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ToDoList", inversedBy="tasks")
     */
    private $list;

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
     * Get the value of status
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */
    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getList(): ?ToDoList
    {
        return $this->list;
    }

    public function setList(?ToDoList $list): self
    {
        $this->list = $list;

        return $this;
    }
}
