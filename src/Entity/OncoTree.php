<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;



/**
 * The OncoTree cancer types associated with a dataset
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
 * @ORM\Table(name="onco_trees")
 * @UniqueEntity("onco_tree_code")
 */
class OncoTree {
/**
   * @ORM\Column(type="integer",name="onco_tree_id")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

/**
   * @Assert\Regex(
   *     pattern="/<[a-z][\s\S]*>/i",
   *     match=false,
   *     message="Name cannot contain HTML or script tags"
   * )
   * @ORM\Column(type="string",unique=true)
   */
  protected $onco_tree_code;

/**
   * @Assert\Regex(
   *     pattern="/<[a-z][\s\S]*>/i",
   *     match=false,
   *     message="Name cannot contain HTML or script tags"
   * )
   * @ORM\Column(type="string",length=255)
   */
  protected $onco_tree_name;

  /**
   * @ORM\Column(type="string",length=512)
   */
  protected $slug;

/**
   * @Assert\Regex(
   *     pattern="/<[a-z][\s\S]*>/i",
   *     match=false,
   *     message="URL cannot contain HTML or script tags"
   * )
   * @ORM\Column(type="string",length=256, nullable=true)
   */
  protected $onco_tree_main_type;

/**
   * @Assert\Regex(
   *     pattern="/<[a-z][\s\S]*>/i",
   *     match=false,
   *     message="URL cannot contain HTML or script tags"
   * )
   * @ORM\Column(type="string",length=256, nullable=true)
   */
  protected $onco_tree_tissue;

 /**
   * @Assert\Regex(
   *     pattern="/<[a-z][\s\S]*>/i",
   *     match=false,
   *     message="URL cannot contain HTML or script tags"
   * )
   * @ORM\Column(type="string",length=256, nullable=true)
   */
  protected $onco_tree_parent;

 /**
   * @Assert\Regex(
   *     pattern="/<[a-z][\s\S]*>/i",
   *     match=false,
   *     message="URL cannot contain HTML or script tags"
   * )
   * @ORM\Column(type="string",length=256, nullable=true)
   */
  protected $onco_tree_nci;

/**
   * @Assert\Regex(
   *     pattern="/<[a-z][\s\S]*>/i",
   *     match=false,
   *     message="URL cannot contain HTML or script tags"
   * )
   * @ORM\Column(type="string",length=256, nullable=true)
   */
  protected $onco_tree_umls;
  /**
   * @ORM\ManyToMany(targetEntity="Dataset", mappedBy="onco_trees")
   */
  protected $datasets;


    /**
     * Add datasets
     *
     * @param \App\Entity\Dataset $datasets
     * @return DataType
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
   * Get name for display
   *
   * @return string
   */
  public function getDisplayName() {
    return $this->onco_tree_name;
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
     * Set onco_tree_name
     *
     * @param string $oncoTreeName
     * @return OncoTree
     */
    public function setOncoTreeName($oncoTreeName)
    {
        $this->onco_tree_name = $oncoTreeName;

        return $this;
    }

    /**
     * Get onco_tree_name
     *
     * @return string 
     */
    public function getOncoTreeName()
    {
        return $this->onco_tree_name;
    }

        /**
     * Set onco_tree_code
     *
     * @param string $oncoTreeCode
     * @return OncoTree
     */
    public function setOncoTreeCode($oncoTreeCode)
    {
        $this->onco_tree_code = $oncoTreeCode;

        return $this;
    }

    /**
     * Get onco_tree_code
     *
     * @return string 
     */
    public function getOncoTreeCode()
    {
        return $this->onco_tree_code;
    }

    /**
     * Set onco_tree_main_type
     *
     * @param string $oncoTreeMainType
     * @return OncoTree
     */
    public function setOncoTreeMainType($oncoTreeMainType)
    {
        $this->onco_tree_main_type = $oncoTreeMainType;

        return $this;
    }

    /**
     * Get onco_tree_main_type
     *
     * @return string 
     */
    public function getOncoTreeMainType()
    {
        return $this->onco_tree_main_type;
    }

    /**
     * Set onco_tree_tissue
     *
     * @param string $oncoTreeTissue
     * @return OncoTree
     */
    public function setOncoTreeTissue($oncoTreeTissue)
    {
        $this->onco_tree_tissue = $oncoTreeTissue;

        return $this;
    }

    /**
     * Get onco_tree_tissue
     *
     * @return string 
     */
    public function getOncoTreeTissue()
    {
        return $this->onco_tree_tissue;
    }

    /**
     * Set onco_tree_parent
     *
     * @param string $oncoTreeParent
     * @return OncoTree
     */
    public function setOncoTreeParent($oncoTreeParent)
    {
        $this->onco_tree_parent = $oncoTreeParent;

        return $this;
    }

    /**
     * Get onco_tree_parent
     *
     * @return string 
     */
    public function getOncoTreeParent()
    {
        return $this->onco_tree_parent;
    }  

    /**
     * Set onco_tree_nci
     *
     * @param string $oncoTreeNci
     * @return OncoTree
     */
    public function setOncoTreeNci($oncoTreeNci)
    {
        $this->onco_tree_nci = $oncoTreeNci;

        return $this;
    }

    /**
     * Get onco_tree_nci
     *
     * @return string 
     */
    public function getOncoTreeNci()
    {
        return $this->onco_tree_nci;
    }

    /**
     * Set onco_tree_umls
     *
     * @param string $oncoTreeUmls
     * @return OncoTree
     */
    public function setOncoTreeUmls($oncoTreeUmls)
    {
        $this->onco_tree_umls = $oncoTreeUmls;

        return $this;
    }

    /**
     * Get onco_tree_umls
     *
     * @return string 
     */
    public function getOncoTreeUmls()
    {
        return $this->onco_tree_umls;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->datasets = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return OncoTree
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

    /**
     * get OncoTree Metadata
     *
     * @return string
     */
    public function getOncoTreeMetadata() {
      $html =  "<p><strong>Code:</strong> {$this->onco_tree_code}</p>";
      $html .= "<p><strong>Main Type:</strong> {$this->onco_tree_main_type}</p>";
      $html .= "<p><strong>Tissue:</strong> {$this->onco_tree_tissue}</p>";
      $html .= "<p><strong>Parent:</strong> {$this->onco_tree_parent}</p>";
      $html .= "<p><strong>NCI #:</strong> {$this->onco_tree_nci}</p>";
      $html .= "<p><strong>UMLS #:</strong> {$this->onco_tree_umls}</p>";
      $html .= "<p><a href='http://oncotree.mskcc.org' target='_blank'>More at OncoTree</a></p>";
      return $html;
    }

    /**
     * Serialize all properties
     *
     * @return array
     */
    public function getAllProperties() {
        return array(
            'onco_tree_name'=>$this->onco_tree_name,
            'onco_tree_code'=>$this->onco_tree_code,
            'onco_tree_main_type'=>$this->onco_tree_main_type,
            'onco_tree_tissue'=>$this->onco_tree_tissue,
            'onco_tree_parent'=>$this->onco_tree_parent,
            'onco_tree_nci'=>$this->onco_tree_nci,
            'onco_tree_umls'=>$this->onco_tree_umls
        );
    }
}
