<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Addressbook
 *
 * @ORM\Table(name="addressbook", indexes={@ORM\Index(name="IDX_CDC0E044539B0606", columns={"uid"}), @ORM\Index(name="IDX_CDC0E0445B7FF48", columns={"memberid"})})
 * @ORM\Entity
 */
class Addressbook
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="addressbook_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=200, nullable=false)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=200, nullable=false)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="cellphone", type="string", length=155, nullable=false)
     */
    private $cellphone;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dob", type="date", nullable=true)
     */
    private $dob;

    /**
     * @var string|null
     *
     * @ORM\Column(name="gender", type="string", length=20, nullable=true)
     */
    private $gender;

    /**
     * @var string|null
     *
     * @ORM\Column(name="city", type="string", length=200, nullable=true)
     */
    private $city;

    /**
     * @var string|null
     *
     * @ORM\Column(name="region_state_province", type="string", length=255, nullable=true)
     */
    private $regionStateProvince;

    /**
     * @var string|null
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @var string|null
     *
     * @ORM\Column(name="zip", type="string", length=255, nullable=true)
     */
    private $zip;

    /**
     * @var string|null
     *
     * @ORM\Column(name="carrier", type="string", length=255, nullable=true)
     */
    private $carrier;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="uid", referencedColumnName="id")
     * })
     */
    private $uid;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="memberid", referencedColumnName="id")
     * })
     */
    private $memberid;


}
