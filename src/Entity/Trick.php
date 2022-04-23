<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TrickRepository::class)
 * @UniqueEntity(
 *     fields={"name"},
 *     message="This trick already exists"
 * )
 */
class Trick implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_updated_date;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tricks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="tricks")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="trick", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=TrickPicture::class, mappedBy="trick", orphanRemoval=true, cascade={"persist"})
     */
    private $pictures;

    private $firstPicture;

    /**
     * @Assert\All({
     * @Assert\Image(mimeTypes="image/jpeg")
     * })
     */
    private $pictureFiles;

    /**
     * @ORM\OneToMany(targetEntity=TrickVideo::class, mappedBy="trick", orphanRemoval=true, cascade={"persist"})
     */
    private $trickVideos;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mainPicture;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->pictures = new ArrayCollection();
        $this->trickVideos = new ArrayCollection();
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

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->created_date;
    }

    public function setCreatedDate(\DateTimeInterface $created_date): self
    {
        $this->created_date = $created_date;

        return $this;
    }

    public function getLastUpdatedDate(): ?\DateTimeInterface
    {
        return $this->last_updated_date;
    }

    public function setLastUpdatedDate(?\DateTimeInterface $last_updated_date): self
    {
        $this->last_updated_date = $last_updated_date;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTrick($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTrick() === $this) {
                $comment->setTrick(null);
            }
        }

        return $this;
    }

    public function jsonSerialize()
    {
        return
            [
                'id'   => $this->getId(),
                'name' => $this->getName(),
                'created_date' => $this->getCreatedDate(),
                'last_updated_date' => $this->getCreatedDate(),
                'author' => $this->getAuthor(),
                'description' => $this->getDescription(),
                'category' => $this->getCategory(),
                'comments' => $this->getComments(),
                'firstPicture' => $this->getPictureFiles()
            ];
    }

    /**
     * @return Collection|TrickPicture[]
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(TrickPicture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setTrick($this);
        }

        return $this;
    }

    public function removePicture(TrickPicture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getTrick() === $this) {
                $picture->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPictureFiles()
    {
        return $this->pictureFiles;
    }

    /**
     * @param mixed $pictureFiles
     */
    public function setPictureFiles($pictureFiles): self
    {
        foreach ($pictureFiles as $file) {
            $picture = new TrickPicture();
            $picture->setPictureFile($file);
            $this->addPicture($picture);
        }
        $this->pictureFiles = $pictureFiles;
        return $this;
    }

    public function setFirstPicture()
    {
        if (!empty($this->pictures)) {
            $this->firstPicture = $this->pictures[0];
        }
        return $this;
    }

    public function getFirstPicture()
    {
        return $this->firstPicture;
    }

    /**
     * @return Collection|TrickVideo[]
     */
    public function getTrickVideos(): Collection
    {
        return $this->trickVideos;
    }

    public function addTrickVideo(TrickVideo $trickVideo): self
    {
        if (!$this->trickVideos->contains($trickVideo)) {
            $this->trickVideos[] = $trickVideo;
            $trickVideo->setTrick($this);
        }

        return $this;
    }

    public function removeTrickVideo(TrickVideo $trickVideo): self
    {
        if ($this->trickVideos->removeElement($trickVideo)) {
            // set the owning side to null (unless already changed)
            if ($trickVideo->getTrick() === $this) {
                $trickVideo->setTrick(null);
            }
        }

        return $this;
    }

    public function getMainPicture(): ?string
    {
        return $this->mainPicture;
    }

    public function setMainPicture(?string $mainPicture): self
    {
        $this->mainPicture = $mainPicture;

        return $this;
    }
}
