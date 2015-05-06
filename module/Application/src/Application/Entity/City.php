<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * City
 *
 * @ORM\Table(name="city", indexes={@ORM\Index(name="regionid", columns={"regionid"})})
 * @ORM\Entity(repositoryClass="\Application\EntityRepository\CityRepository")
 */
class City
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
     * @ORM\Column(name="name", type="string", length=50, nullable=true)
     */
    private $name;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return City
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * Set regionid
     *
     * @param \Application\Entity\Region $regionid
     * @return City
     */
    public function setRegionid(\Application\Entity\Region $regionid = null)
    {
        $this->regionid = $regionid;

        return $this;
    }
}
