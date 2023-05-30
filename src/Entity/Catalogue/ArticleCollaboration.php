<?php

namespace App\Entity\Catalogue;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArticleCollaboration
 *
 * @ORM\Entity
 */
class ArticleCollaboration extends Article {
    /**
     * @var string
     *
     * @ORM\Column(name="artiste", type="string")
     */
    private $artiste;

    /**
     * @var string
     *
     * @ORM\Column(name="entreprise", type="string")
     */
    private $entreprise;

    /**
     * @var string
     *
     * @ORM\Column(name="personnage", type="string")
     */
    private $personnage;

    /**
     * @var string
     *
     * @ORM\Column(name="univers", type="string")
     */
    private $univers;

    /**
     * @var string
     *
     * @ORM\Column(name="collection", type="string")
     */
    private $collection;

    /**
     * Set artiste
     *
     * @param string $auteur
     *
     * @return ArticleCollaboration
     */
    public function setArtiste($artiste)
    {
        $this->artiste = $artiste;

        return $this;
    }

    /**
     * Get artiste
     *
     * @return string
     */
    public function getArtiste()
    {
        return $this->artiste;
    }

    /**
     * Set entreprise
     *
     * @param string $entreprise
     *
     * @return ArticleCollaboration
     */
    public function setEntreprise($entreprise)
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    /**
     * Get entreprise
     *
     * @return string
     */
    public function getEntreprise()
    {
        return $this->entreprise;
    }

    /**
     * Set personnage
     *
     * @param integer $personnage
     *
     * @return ArticleCollaboration
     */
    public function setPersonnage($personnage)
    {
        $this->personnage = $personnage;

        return $this;
    }

    /**
     * Get personnage
     *
     * @return int
     */
    public function getPersonnage()
    {
        return $this->personnage;
    }

    /**
     * Set univers
     *
     * @param string $univers
     *
     * @return ArticleCollaboration
     */
    public function setUnivers($univers)
    {
        $this->univers = $univers;

        return $this;
    }

    /**
     * Get univers
     *
     * @return string
     */
    public function getUnivers()
    {
        return $this->univers;
    }

     /**
     * Set collection
     *
     * @param string $collection
     *
     * @return ArticleCollaboration
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get collection
     *
     * @return string
     */
    public function getCollection()
    {
        return $this->collection;
    }
}

