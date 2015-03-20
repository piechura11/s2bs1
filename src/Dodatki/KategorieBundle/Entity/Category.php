<?php

namespace Dodatki\KategorieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Category
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     */
    private $parent=null;

    /**
     * @ORM\Column(name="ikona", type="string", length=255, nullable=true)
     */
    private $ikona;


    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    
   public function __construct()
    {
        $this->children = new ArrayCollection();
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
     * Set children
     *
     * @param string $children
     * @return Category
     */
    public function setChildren(Category $child) {
       $this->children[] = $child;
       $child->setParent($this);
    }

    /**
     * Get children
     *
     * @return string 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param string $parent
     * @return Category
     */
    public function setParent(Category $parent) {
       $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return string 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set ikona
     *
     * @param string $ikona
     * @return Category
     */
    public function setIkona($ikona)
    {
        $this->ikona = $ikona;

        return $this;
    }

    /**
     * Get ikona
     *
     * @return string 
     */
    public function getIkona()
    {
        return $this->ikona;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Add children
     *
     * @param \Dodatki\KategorieBundle\Entity\Category $children
     * @return Category
     */
    public function addChild(\Dodatki\KategorieBundle\Entity\Category $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Dodatki\KategorieBundle\Entity\Category $children
     */
    public function removeChild(\Dodatki\KategorieBundle\Entity\Category $children)
    {
        $this->children->removeElement($children);
    }
}
