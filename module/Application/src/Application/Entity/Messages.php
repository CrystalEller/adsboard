<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Messages
 *
 * @ORM\Table(name="messages", indexes={@ORM\Index(name="userid", columns={"userid"}), @ORM\Index(name="messages_ibfk_2", columns={"adsid"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Messages
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255, nullable=true)
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var \Application\Entity\Ads
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Ads")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="adsid", referencedColumnName="id")
     * })
     */
    private $adsid;

    /**
     * @var \Application\Entity\Users
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userid", referencedColumnName="id")
     * })
     */
    private $userid;

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->created = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Messages
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Messages
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Messages
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set viewed
     *
     * @param boolean $viewed
     *
     * @return Messages
     */
    public function setViewed($viewed)
    {
        $this->viewed = $viewed;

        return $this;
    }

    /**
     * Get viewed
     *
     * @return boolean
     */
    public function getViewed()
    {
        return $this->viewed;
    }

    /**
     * Set adsid
     *
     * @param \Application\Entity\Ads $adsid
     *
     * @return Messages
     */
    public function setAdsid(\Application\Entity\Ads $adsid = null)
    {
        $this->adsid = $adsid;

        return $this;
    }

    /**
     * Get adsid
     *
     * @return \Application\Entity\Ads
     */
    public function getAdsid()
    {
        return $this->adsid;
    }

    /**
     * Set userid
     *
     * @param \Application\Entity\Users $userid
     *
     * @return Messages
     */
    public function setUserid(\Application\Entity\Users $userid = null)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get userid
     *
     * @return \Application\Entity\Users
     */
    public function getUserid()
    {
        return $this->userid;
    }
}
