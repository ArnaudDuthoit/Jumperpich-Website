<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjetRepository")
 * @Vich\Uploadable()
 */
class Projet
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255);
     */
    private $filename;

    /**
     * @var File|null
     * @Assert\Image(
     *     maxSize = "10240k",
     *     mimeTypes={"image/jpeg", "image/png"}
     * )
     * @Vich\UploadableField(mapping="projet_image", fileNameProperty="filename")
     */
    private $imageFile;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255);
     */
    private $mp3filename;

    /**
     * @var File|null
     * @Assert\File(
     *     maxSize = "1024000k",
     * )
     * @Vich\UploadableField(mapping="projet_mp3", fileNameProperty="mp3filename")
     */
    private $mp3File;

    /**
     * @ORM\Column(type="string",length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="projets")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", inversedBy="projets")
     */
    private $tags;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $YTB_link;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $views;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $downloadCount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $soundcloud;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $fileSize;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fileLength;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mixcloud;



    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->tags = new ArrayCollection();

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

    public function getSlug(): string
    {

        return (new Slugify())->slugify($this->title); // hello-world
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addProjet($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
            $tag->removeProjet($this);
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param null|string $filename
     */
    public function setFilename(?string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * @return null|File
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param null|File $imageFile
     */
    public function setImageFile(?File $imageFile): void
    {
        $this->imageFile = $imageFile;
        if ($this->imageFile instanceof UploadedFile) {
            $this->updated_at = new \DateTime('now');
        }

    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getYTBLink(): ?string
    {
        return $this->YTB_link;
    }

    public function setYTBLink(?string $YTB_link): self
    {
        $this->YTB_link = $YTB_link;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMp3filename(): ?string
    {
        return $this->mp3filename;
    }

    /**
     * @param null|string $mp3filename
     */
    public function setMp3filename(?string $mp3filename): void
    {
        $this->mp3filename = $mp3filename;

    }

    /**
     * @return null|File
     */
    public function getMp3File(): ?File
    {
        return $this->mp3File;
    }

    /**
     * @param null|File $mp3File
     */
    public function setMp3File(?File $mp3File): void
    {
        $this->mp3File = $mp3File;
        if ($this->mp3File instanceof UploadedFile) {
            $this->updated_at = new \DateTime('now');
        }
    }

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function setViews(?int $views): self
    {
        $this->views = $views;

        return $this;
    }

    public function getDownloadCount(): ?int
    {
        return $this->downloadCount;
    }

    public function setDownloadCount(?int $downloadCount): self
    {
        $this->downloadCount = $downloadCount;

        return $this;
    }

    public function getSoundcloud(): ?string
    {
        return $this->soundcloud;
    }

    public function setSoundcloud(?string $soundcloud): self
    {
        $this->soundcloud = $soundcloud;

        return $this;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function setFileSize(?int $fileSize): self
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    public function getFileLength(): ?string
    {
        return $this->fileLength;
    }

    public function setFileLength(?string $fileLength): self
    {
        $this->fileLength = $fileLength;

        return $this;
    }

    public function getMixcloud(): ?string
    {
        return $this->mixcloud;
    }

    public function setMixcloud(?string $mixcloud): self
    {
        $this->mixcloud = $mixcloud;

        return $this;
    }

}
