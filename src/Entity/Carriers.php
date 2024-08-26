<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Carriers
 *
 * @ORM\Table(name="carriers")
 * @ORM\Entity
 */
class Carriers
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="carriers_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getCarrierName(): string
    {
        return $this->carrierName;
    }

    /**
     * @param string $carrierName
     */
    public function setCarrierName(string $carrierName): void
    {
        $this->carrierName = $carrierName;
    }

    /**
     * @return string
     */
    public function getCarrierUrl(): string
    {
        return $this->carrierUrl;
    }

    /**
     * @param string $carrierUrl
     */
    public function setCarrierUrl(string $carrierUrl): void
    {
        $this->carrierUrl = $carrierUrl;
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
     * @return string|null
     */
    public function getCarrierMms(): ?string
    {
        return $this->carrierMms;
    }

    /**
     * @param string|null $carrierMms
     */
    public function setCarrierMms(?string $carrierMms): void
    {
        $this->carrierMms = $carrierMms;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="carrier_name", type="string", length=150, nullable=false)
     */
    private $carrierName;

    /**
     * @var string
     *
     * @ORM\Column(name="carrier_url", type="string", length=150, nullable=false)
     */
    private $carrierUrl;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var string|null
     *
     * @ORM\Column(name="carrier_mms", type="string", nullable=true)
     */
    private $carrierMms;


}
