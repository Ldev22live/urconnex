<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Messages
 *
 * @ORM\Table(name="messages", indexes={@ORM\Index(name="IDX_DB021E96F624B39D", columns={"sender_id"}), @ORM\Index(name="IDX_DB021E96CD53EDB6", columns={"receiver_id"})})
 * @ORM\Entity
 */
class Messages
{
    /**
     * @var int
     *
     * @ORM\Column(name="messageid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="messages_messageid_seq", allocationSize=1, initialValue=1)
     */
    private $messageid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @var string|null
     *
     * @ORM\Column(name="attachments", type="string", length=200, nullable=true)
     */
    private $attachments;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sender_id", referencedColumnName="id")
     * })
     */
    private $sender;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="receiver_id", referencedColumnName="id")
     * })
     */
    private $receiver;

    /**
     * @return int
     */
    public function getMessageid(): int
    {
        return $this->messageid;
    }

    /**
     * @param int $messageid
     */
    public function setMessageid(int $messageid): void
    {
        $this->messageid = $messageid;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     */
    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string|null
     */
    public function getAttachments(): ?string
    {
        return $this->attachments;
    }

    /**
     * @param string|null $attachments
     */
    public function setAttachments(?string $attachments): void
    {
        $this->attachments = $attachments;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    /**
     * @param \DateTime|null $created
     */
    public function setCreated(?\DateTime $created): void
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    /**
     * @param \DateTime|null $updated
     */
    public function setUpdated(?\DateTime $updated): void
    {
        $this->updated = $updated;
    }

    /**
     * @return \Users
     */
    public function getSender(): \Users
    {
        return $this->sender;
    }

    /**
     * @param \Users $sender
     */
    public function setSender(\Users $sender): void
    {
        $this->sender = $sender;
    }

    /**
     * @return \Users
     */
    public function getReceiver(): \Users
    {
        return $this->receiver;
    }

    /**
     * @param \Users $receiver
     */
    public function setReceiver(\Users $receiver): void
    {
        $this->receiver = $receiver;
    }


}
