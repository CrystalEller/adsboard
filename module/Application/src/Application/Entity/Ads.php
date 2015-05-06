<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ads
 *
 * @ORM\Table(name="ads", indexes={@ORM\Index(name="userId", columns={"userId"}), @ORM\Index(name="cityid", columns={"cityid"}), @ORM\Index(name="currencyid", columns={"currencyid"}), @ORM\Index(name="categoryid", columns={"categoryid"}), @ORM\Index(name="regionid", columns={"regionid"})})
 * @ORM\Entity(repositoryClass="Application\EntityRepository\AdsRepository")
 */
class Ads
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
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="price", type="integer", nullable=true)
     */
    private $price;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var string
     *
     * @ORM\Column(name="userName", type="string", length=64, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=64, nullable=false)
     */
    private $telephone;

    /**
     * @var \Application\Entity\City
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\City")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cityid", referencedColumnName="id")
     * })
     */
    private $cityid;

    /**
     * @var \Application\Entity\Currency
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Currency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="currencyid", referencedColumnName="id")
     * })
     */
    private $currencyid;

    /**
     * @var \Application\Entity\Categories
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Categories")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categoryid", referencedColumnName="id")
     * })
     */
    private $categoryid;

    /**
     * @var \Application\Entity\Region
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Region")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="regionid", referencedColumnName="id")
     * })
     */
    private $regionid;

    /**
     * @var \Application\Entity\Users
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userId", referencedColumnName="id")
     * })
     */
    private $userid;



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
     * Set title
     *
     * @param string $title
     *
     * @return Ads
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Ads
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set price
     *
     * @param integer $price
     *
     * @return Ads
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return integer
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Ads
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
     * Set username
     *
     * @param string $username
     *
     * @return Ads
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Ads
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set cityid
     *
     * @param \Application\Entity\City $cityid
     *
     * @return Ads
     */
    public function setCityid(\Application\Entity\City $cityid = null)
    {
        $this->cityid = $cityid;

        return $this;
    }

    /**
     * Get cityid
     *
     * @return \Application\Entity\City
     */
    public function getCityid()
    {
        return $this->cityid;
    }

    /**
     * Set currencyid
     *
     * @param \Application\Entity\Currency $currencyid
     *
     * @return Ads
     */
    public function setCurrencyid(\Application\Entity\Currency $currencyid = null)
    {
        $this->currencyid = $currencyid;

        return $this;
    }

    /**
     * Get currencyid
     *
     * @return \Application\Entity\Currency
     */
    public function getCurrencyid()
    {
        return $this->currencyid;
    }

    /**
     * Set categoryid
     *
     * @param \Application\Entity\Categories $categoryid
     *
     * @return Ads
     */
    public function setCategoryid(\Application\Entity\Categories $categoryid = null)
    {
        $this->categoryid = $categoryid;

        return $this;
    }

    /**
     * Get categoryid
     *
     * @return \Application\Entity\Categories
     */
    public function getCategoryid()
    {
        return $this->categoryid;
    }

    /**
     * Set regionid
     *
     * @param \Application\Entity\Region $regionid
     *
     * @return Ads
     */
    public function setRegionid(\Application\Entity\Region $regionid = null)
    {
        $this->regionid = $regionid;

        return $this;
    }

    /**
     * Get regionid
     *
     * @return \Application\Entity\Region
     */
    public function getRegionid()
    {
        return $this->regionid;
    }

    /**
     * Set userid
     *
     * @param \Application\Entity\Users $userid
     *
     * @return Ads
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
