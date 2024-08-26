<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Logger
 *
 * @ORM\Table(name="logger")
 * @ORM\Entity
 */
class Logger
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="users_id_seq", allocationSize=1, initialValue=1)
     */
    public $id;

    public $errmessage;

    public $created;
}
