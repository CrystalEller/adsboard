<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryAttributes
 *
 * @ORM\Table(name="category_attributes", indexes={@ORM\Index(name="catid", columns={"catid"})})
 * @ORM\Entity(repositoryClass="Application\EntityRepository\CategoryAttributesRepository")
 */
class CategoryAttributes
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
     * @ORM\Column(name="name", type="string", length=11, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="values", type="array", length=65535, nullable=true)
     */
    private $values;

    /**
     * @var \Application\Entity\Categories
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Categories")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="catid", referencedColumnName="id")
     * })
     */
    private $catid;


    public function toArray()
    {
        $arr = array();

        foreach (get_class_methods($this) as $method) {
            if (preg_match('/^get(\w+)/', $method, $matches)) {
                $arr[lcfirst($matches[1])] = $this->$method();
            }
        }

        return $arr;
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
     * Set id
     *
     * @return integer
     */
    public function setId($id)
    {
        return $this->id = $id;
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
     *
     * @return CategoryAttributes
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get values
     *
     * @return string
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Set values
     *
     * @param string $values
     *
     * @return CategoryAttributes
     */
    public function setValues($values)
    {
        $this->values = $values;

        return $this;
    }

    /**
     * Get catid
     *
     * @return \Application\Entity\Categories
     */
    public function getCatid()
    {
        return $this->catid;
    }

    /**
     * Set catid
     *
     * @param \Application\Entity\Categories $catid
     *
     * @return CategoryAttributes
     */
    public function setCatid(\Application\Entity\Categories $catid = null)
    {
        $this->catid = $catid;

        return $this;
    }
}
