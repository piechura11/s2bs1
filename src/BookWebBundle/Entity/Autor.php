<?php

namespace BookWebBundle\Entity;
use Symfony\Component\Validator\Constraints;
use Doctrine\ORM\Mapping as ORM;

/**
 * Autor
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Autor
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @Constraints\Length(min=3, minMessage="Ziom co najmiej {{ limit }}!")
     * @ORM\Column(name="surname", type="string", length=128)
     */
    private $surname;
        /**
     * @var string
     *
     * @ORM\Column(name="sex", type="string", length=128)
     */
    private $sex;
    /**
     * @var boolean
     *
     * @ORM\Column(name="isDead", type="boolean")
     */

    private $isDead;

    /**
     *
     * @ORM\OneToMany(targetEntity="Book", mappedBy="autor")
     */
    private $books;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthDay", type="datetime" )
     */
    private $birthDay;


    /**
     * Get id
     *
     * @return integer 
     */

    public function __construct(){
        $this->isDead=false;
        $this->books = new \Doctrine\Common\Collections\ArrayCollection;
    }
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Autor
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
     * Set surname
     *
     * @param string $surname
     * @return Autor
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string 
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set birthDay
     *
     * @param \DateTime $birthDay
     * @return Autor
     */
    public function setBirthDay($birthDay)
    {
        $this->birthDay = $birthDay;

        return $this;
    }

    /**
     * Get birthDay
     *
     * @return \DateTime 
     */
    public function getBirthDay()
    {
        return $this->birthDay;
    }

    /**
     * Set isDead
     *
     * @param boolean $isDead
     * @return Autor
     */
    public function setIsDead($isDead)
    {
        $this->isDead = $isDead;

        return $this;
    }

    /**
     * Get isDead
     *
     * @return boolean 
     */
    public function getIsDead()
    {
        return $this->isDead;
    }

    /**
     * Set sex
     *
     * @param string $sex
     * @return Autor
     */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Get sex
     *
     * @return string 
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Add books
     *
     * @param \BookWebBundle\Entity\Book $books
     * @return Autor
     */
    public function addBook(\BookWebBundle\Entity\Book $books)
    {
        $this->books[] = $books;

        return $this;
    }

    /**
     * Remove books
     *
     * @param \BookWebBundle\Entity\Book $books
     */
    public function removeBook(\BookWebBundle\Entity\Book $books)
    {
        $this->books->removeElement($books);
    }

    /**
     * Get books
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBooks()
    {
        return $this->books;
    }
}
