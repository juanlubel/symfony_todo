<?php

namespace App\Entity;

use App\Repository\BoardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BoardRepository::class)
 */
class Board
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
     * @ORM\Column(type="string", length=50)
     * @Groups({"Task", "Board"})
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="board")
     * @Groups({"Board"})
     */
    private $tasks;

    public function __construct(string $title, string $category)
    {
        $this->title = $title;
        $this->category = $category;
        $this->tasks = new ArrayCollection();
    }

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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setBoard($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
            // set the owning side to null (unless already changed)
            if ($task->getBoard() === $this) {
                $task->setBoard(null);
            }
        }

        return $this;
    }
}
