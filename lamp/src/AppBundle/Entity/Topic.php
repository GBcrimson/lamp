<?php

namespace AppBundle\Entity;

use AppBundle\AppBundle;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Topic
 *
 * @ORM\Table(name="topic")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TopicRepository")
 */
class Topic
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=255)
     */
    private $content;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToMany(targetEntity="User", cascade={"remove"})
     * @ORM\JoinTable(name="topics_likes",
     * joinColumns={@ORM\JoinColumn(name="topic_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     * )
     */
    private $likes;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tag", cascade={"persist"}))
     * @ORM\JoinTable(name="topics_tags",
     * joinColumns={@ORM\JoinColumn(name="topic_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     */

    private $tag;

    /**
     * @param Tag $tag
     */
    public function addTag(Tag $tag){
        $this->tag->add($tag);
    }


    /**
     * @return ArrayCollection
     */
    public function getTag()
    {
        return $this->tag;
    }


    public function __construct()
    {
        $this->likes = new ArrayCollection();

        $this->tag = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return int
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
     * @return Topic
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
     * Set content
     *
     * @param string $content
     *
     * @return Topic
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Topic
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set author
     *
     * @param user $author
     *
     * @return Topic
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return integer
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return mixed
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * @param mixed $likes
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;
    }


    /**
     * Add like
     *
     * @param \AppBundle\Entity\User $like
     *
     * @return Topic
     */
    public function addLike(User $like)
    {
        $this->likes ->add($like);
        return $this;
    }

    /**
     * Remove like
     *
     * @param \AppBundle\Entity\User $like
     */
    public function removeLike(User $like)
    {
        $this->likes->removeElement($like);
    }

    /**
     * Remove tag
     *
     * @param \AppBundle\Entity\Tag $tag
     */
    public function removeTag(Tag $tag)
    {
        $this->tag->removeElement($tag);
    }
}
