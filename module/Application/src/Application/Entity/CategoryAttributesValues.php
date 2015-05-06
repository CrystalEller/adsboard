<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryAttributesValues
 *
 * @ORM\Table(name="category_attributes_values", indexes={@ORM\Index(name="attrid", columns={"attrid"})})
 * @ORM\Entity(repositoryClass="Application\EntityRepository\CategoryAttributesValuesRepository")
 */
class CategoryAttributesValues
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
     * @ORM\Column(name="owner", type="string", columnDefinition="ENUM('admin', 'user')")
     */
    private $owner;


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
     * Set value
     *
     * @param string $value
     *
     * @return CategoryAttributesValues
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
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
     * Set attrid
     *
     * @param \Application\Entity\CategoryAttributes $attrid
     *
     * @return CategoryAttributesValues
     */
    public function setAttrid(\Application\Entity\CategoryAttributes $attrid = null)
    {
        $this->attrid = $attrid;

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
     * Set value
     *
     * @param string $value
     *
     * @return CategoryAttributesValues
     */
    public function setOwner($value)
    {
        $this->owner = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }
}
