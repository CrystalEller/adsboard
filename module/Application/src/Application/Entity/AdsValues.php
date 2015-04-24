<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdsValues
 *
 * @ORM\Table(name="ads_values", indexes={@ORM\Index(name="attrid", columns={"attrid"}), @ORM\Index(name="adsid", columns={"adsid"})})
 * @ORM\Entity(repositoryClass="Application\EntityRepository\AdsValuesRepository")
 */
class AdsValues
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
     * @ORM\Column(name="value", type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * @var \Application\Entity\CategoryAttributes
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\CategoryAttributes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="attrid", referencedColumnName="id")
     * })
     */
    private $attrid;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return AdsValues
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get attrid
     *
     * @return \Application\Entity\CategoryAttributes
     */
    public function getAttrid()
    {
        return $this->attrid;
    }

    /**
     * Set attrid
     *
     * @param \Application\Entity\CategoryAttributes $attrid
     *
     * @return AdsValues
     */
    public function setAttrid(\Application\Entity\CategoryAttributes $attrid = null)
    {
        $this->attrid = $attrid;

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
     * Set adsid
     *
     * @param \Application\Entity\Ads $adsid
     *
     * @return AdsValues
     */
    public function setAdsid(\Application\Entity\Ads $adsid = null)
    {
        $this->adsid = $adsid;

        return $this;
    }
}
