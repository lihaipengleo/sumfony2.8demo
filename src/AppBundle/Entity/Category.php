<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Utils\Jobeet as Jobeet;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Job", mappedBy="category")
     */
    private $jobs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Affiliate", mappedBy="categories")
     */
    private $affiliates;

    private $active_jobs;

    private $more_jobs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->jobs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->affiliates = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName() ? $this->getName() : "";
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
     * Set name
     *
     * @param string $name
     *
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
     * Add job
     *
     * @param \AppBundle\Entity\Job $job
     *
     * @return Category
     */
    public function addJob(\AppBundle\Entity\Job $job)
    {
        $this->jobs[] = $job;

        return $this;
    }

    /**
     * Remove job
     *
     * @param \AppBundle\Entity\Job $job
     */
    public function removeJob(\AppBundle\Entity\Job $job)
    {
        $this->jobs->removeElement($job);
    }

    /**
     * Get jobs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * Add affiliate
     *
     * @param \AppBundle\Entity\Affiliate $affiliate
     *
     * @return Category
     */
    public function addAffiliate(\AppBundle\Entity\Affiliate $affiliate)
    {
        $this->affiliates[] = $affiliate;

        return $this;
    }

    /**
     * Remove affiliate
     *
     * @param \AppBundle\Entity\Affiliate $affiliate
     */
    public function removeAffiliate(\AppBundle\Entity\Affiliate $affiliate)
    {
        $this->affiliates->removeElement($affiliate);
    }

    /**
     * Get affiliates
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAffiliates()
    {
        return $this->affiliates;
    }
    public function setActiveJobs($jobs)
    {
        $this->active_jobs = $jobs;
    }

    public function getActiveJobs()
    {
        return $this->active_jobs;
    }

    /**
     * @var string
     */
    private $slug;


    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Category
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return Jobeet::slugify($this->getName());
    }
   

    /**
     * @ORM\PrePersist
     */
    public function setSlugValue()
    {
        // Add your code here
        $this->slug = Jobeet::slugify($this->getName());
    }

    public function setMoreJobs($jobs)
    {
        $this->more_jobs = $jobs >=  0 ? $jobs : 0;
    }

    public function getMoreJobs()
    {
        return $this->more_jobs;
    }
}
