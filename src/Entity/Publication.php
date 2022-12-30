<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;



/**
 * Describe a publication related to a dataset
 *
 *   This file is part of the Data Catalog project.
 *   Copyright (C) 2016 NYU Health Sciences Library
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @ORM\Entity
 * @ORM\Table(name="publications")
 * @UniqueEntity("slug")
 * @ORM\Entity(repositoryClass="App\Repository\PublicationRepository")
 */
class Publication {
  /**
   * @ORM\Column(type="integer",name="publication_id")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @Assert\Regex(
   *     pattern="/<[a-z][\s\S]*>/i",
   *     match=false,
   *     message="Citation cannot contain HTML or script tags"
   * )
   * @ORM\Column(type="string",length=512)
   */
  protected $citation;

  /**
   * @Assert\Regex(
   *     pattern="/<[a-z][\s\S]*>/i",
   *     match=false,
   *     message="URL cannot contain HTML or script tags"
   * )
   * @ORM\Column(type="string",length=1028, nullable=true)
   */
  protected $url;

  /**
   * @Assert\Regex(
   *     pattern="/<[a-z][\s\S]*>/i",
   *     match=false,
   *     message="Synapse ID cannot contain HTML or script tags"
   * )
   * @ORM\Column(type="string",length=128, nullable=true)
   */
  protected $synapseid;

  /**
   * @Assert\Regex(
   *     pattern="/<[a-z][\s\S]*>/i",
   *     match=false,
   *     message="DOI cannot contain HTML or script tags"
   * )
   * @ORM\Column(type="string",length=128, nullable=true)
   */
  protected $doi;

  /**
   * @ORM\Column(type="string",length=128, unique=true)
   */
  protected $slug;


  /**
   * @ORM\ManyToMany(targetEntity="Dataset", mappedBy="publications")
   **/
  protected $datasets;

    public function __construct() {
      $this->datasets = new \Doctrine\Common\Collections\ArrayCollection();
    }

  /**
   * Get name for display
   *
   * @return string
   */
  public function getDisplayName() {
    return $this->citation;
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
     * Set citation
     *
     * @param string $citation
     * @return Publication
     */
    public function setCitation($citation)
    {
        $this->citation = $citation;

        return $this;
    }

    /**
     * Get citation
     *
     * @return string 
     */
    public function getCitation()
    {
        return $this->citation;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Publication
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set synapseid
     *
     * @param string $synapseid
     * @return Publication
     */
    public function setSynapseid($synapseid)
    {
        $this->synapseid = $synapseid;

        return $this;
    }

    /**
     * Get synapseid
     *
     * @return string 
     */
    public function getSynapseid()
    {
        return $this->synapseid;
    }


    /**
     * Set doi
     *
     * @param string $doi
     * @return Publication
     */
    public function setDoi($doi)
    {
        $this->doi = $doi;

        return $this;
    }

    /**
     * Get doi
     *
     * @return string 
     */
    public function getDoi()
    {
        return $this->doi;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Publication
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
        return $this->slug;
    }

    /* Return a string concatenating the citation and synapse id for searching on the admin form */
    public function __toString()
    {
        if ($this->synapseid) {
            $combined = $this->citation." (SynapseID: ".$this->synapseid.")";
            return (string) $combined;
        }
        else {
            return (string) $this->citation;
        }
    }

    /**
     * Add datasets
     *
     * @param \App\Entity\Dataset $datasets
     * @return Publication
     */
    public function addDataset(\App\Entity\Dataset $datasets)
    {
        $this->datasets[] = $datasets;

        return $this;
    }

    /**
     * Remove datasets
     *
     * @param \App\Entity\Dataset $datasets
     */
    public function removeDataset(\App\Entity\Dataset $datasets)
    {
        $this->datasets->removeElement($datasets);
    }

    /**
     * Get datasets
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDatasets()
    {
        return $this->datasets;
    }

    /**
    * Serialize all properties
    *
    * @return array
    */
    public function getAllProperties() {
      return array(
        'citation'=>$this->citation,
        'url'=>$this->url,
        'doi'=>$this->doi,
        'synapseid'=>$this->synapseid
      );
    }
}
