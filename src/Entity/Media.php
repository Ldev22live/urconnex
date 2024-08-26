<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Media
 *
 * @ORM\Table(name="media", indexes={@ORM\Index(name="IDX_6A2CA10C539B0606", columns={"uid"})})
 * @ORM\Entity
 */
class Media
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="media_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=200, nullable=false)
     */
    private $filename;

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getFilepath(): string
    {
        return $this->filepath;
    }

    /**
     * @param string $filepath
     */
    public function setFilepath(string $filepath): void
    {
        $this->filepath = $filepath;
    }

    /**
     * @return string
     */
    public function getFiletype(): string
    {
        return $this->filetype;
    }

    /**
     * @param string $filetype
     */
    public function setFiletype(string $filetype): void
    {
        $this->filetype = $filetype;
    }

    /**
     * @return string|null
     */
    public function getSharedId(): ?string
    {
        return $this->sharedId;
    }

    /**
     * @param string|null $sharedId
     */
    public function setSharedId(?string $sharedId): void
    {
        $this->sharedId = $sharedId;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateUploaded(): ?\DateTime
    {
        return $this->dateUploaded;
    }

    /**
     * @param \DateTime|null $dateUploaded
     */
    public function setDateUploaded(?\DateTime $dateUploaded): void
    {
        $this->dateUploaded = $dateUploaded;
    }

    /**
     * @return \DateTime|null
     */
    public function getFileUpdated(): ?\DateTime
    {
        return $this->fileUpdated;
    }

    /**
     * @param \DateTime|null $fileUpdated
     */
    public function setFileUpdated(?\DateTime $fileUpdated): void
    {
        $this->fileUpdated = $fileUpdated;
    }

    /**
     * @return string|null
     */
    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    /**
     * @param string|null $thumbnail
     */
    public function setThumbnail(?string $thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * @return int|null
     */
    public function getViews(): ?int
    {
        return $this->views;
    }

    /**
     * @param int|null $views
     */
    public function setViews(?int $views): void
    {
        $this->views = $views;
    }

    /**
     * @return int|null
     */
    public function getLikes(): ?int
    {
        return $this->likes;
    }

    /**
     * @param int|null $likes
     */
    public function setLikes(?int $likes): void
    {
        $this->likes = $likes;
    }

    /**
     * @return int|null
     */
    public function getUid(): int
    {
        return $this->uid;
    }

    /**
     * @param int|null
     */
    public function setUid(int $uid): void
    {
        $this->uid = $uid;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="filepath", type="string", length=255, nullable=false)
     */
    private $filepath;

    /**
     * @var string
     *
     * @ORM\Column(name="filetype", type="string", length=50, nullable=false)
     */
    private $filetype;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shared_id", type="string", length=255, nullable=true)
     */
    private $sharedId;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_uploaded", type="datetime", nullable=true)
     */
    private $dateUploaded;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="file_updated", type="datetime", nullable=true)
     */
    private $fileUpdated;

    /**
     * @var string|null
     *
     * @ORM\Column(name="thumbnail", type="string", length=255, nullable=true)
     */
    private $thumbnail;

    /**
     * @var int|null
     *
     * @ORM\Column(name="views", type="integer", nullable=true)
     */
    private $views;

    /**
     * @var int|null
     *
     * @ORM\Column(name="likes", type="integer", nullable=true)
     */
    private $likes;

    /**
     * @var int|null
     */
    private $uid;


}
