<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"Task", "Board"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=80)
     * @Groups({"Task", "Board"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"Task", "Board"})
     */
    private $Description;

    /**
     * @ORM\ManyToOne(targetEntity="Board", inversedBy="tasks")
     * @Groups({"Task"})
     */
    private $board;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function __construct(string $title, string $Description, Board $board)
    {
        $this->title = $title;
        $this->Description = $Description;
        $this->board = $board;
    }

    public function getBoard(): ?board
    {
        return $this->board;
    }

    public function setBoard(?board $board): self
    {
        $this->board = $board;

        return $this;
    }
}
