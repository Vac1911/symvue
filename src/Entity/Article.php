<?php

namespace App\Entity;

use App\Entity\Traits\Proxiable;
use App\Entity\Traits\Sluggable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;

/**
 * Article
 *
 * @ORM\Table(name="article", indexes={@ORM\Index(name="articles_author_id_foreign", columns={"author_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\BaseRepository")
 */
class Article
{
    use Sluggable, Proxiable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cover_photo_url", type="text", length=65535, nullable=true)
     */
    private $coverPhotoUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", length=65535, nullable=false)
     */
    private $body;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="articles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     * })
     */
    private $author;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="article")
     */
    private $comments;

    /**
     * @var Collection
     *
     * @ManyToMany(targetEntity="Tag")
     * @JoinTable(name="article_tag")
     */
    private $tags;

    public function __construct() {
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->slug = $this->getSlug();
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Article
     */
    public function setTitle(string $title): Article
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCoverPhotoUrl(): ?string
    {
        return $this->coverPhotoUrl;
    }

    /**
     * @param string|null $coverPhotoUrl
     * @return Article
     */
    public function setCoverPhotoUrl(?string $coverPhotoUrl): Article
    {
        $this->coverPhotoUrl = $coverPhotoUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param string $body
     * @return Article
     */
    public function setBody(string $body): Article
    {
        $this->body = $body;
        return $this;
    }

    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param User $author
     * @return Article
     */
    public function setAuthor(User $author): Article
    {
        $this->author = $author;
        return $this;
    }

//    /**
//     * @param ArrayCollection $comments
//     * @return Article
//     */
//    public function setComments(ArrayCollection $comments): Article
//    {
//        $this->comments = $comments;
//        return $this;
//    }
}
