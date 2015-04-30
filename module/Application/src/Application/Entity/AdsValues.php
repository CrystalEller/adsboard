<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdsValues
 *
 * @ORM\Table(name="ads_values", indexes={@ORM\Index(name="attrid", columns={"valueid"}), @ORM\Index(name="adsid", columns={"adsid"})})
 * @ORM\Entity
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
     * @var \Application\Entity\Ads
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Ads")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="adsid", referencedColumnName="id")
     * })
     */
    private $adsid;

    /**
     * @var \Application\Entity\CategoryAttributesValues
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\CategoryAttributesValues")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="valueid", referencedColumnName="id")
     * })
     */
    private $valueid;



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
     * Set valueid
     *
     * @param \Application\Entity\CategoryAttributesValues $valueid
     *
     * @return AdsValues
     */
    public function setValueid(\Application\Entity\CategoryAttributesValues $valueid = null)
    {
        $this->valueid = $valueid;

        return $this;
    }

    /**
     * Get valueid
     *
     * @return \Application\Entity\CategoryAttributesValues
     */
    public function getValueid()
    {
        return $this->valueid;
    }
}
