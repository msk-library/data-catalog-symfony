<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * The MSK core facilities associated with a dataset
 *
 *   This file is part of the Data Catalog project.
 *   Copyright (C) 2022 NYU Health Sciences Library
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
 * @ORM\Table(name="core_facilities")
 * @UniqueEntity("core_facility_name")
 */
class CoreFacility {
  /**
   * @ORM\Column(type="integer",name="core_facility_id")
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
   * @ORM\Column(type="string",length=255, unique=true)
   */
  protected $core_facility_name;

  /**
   * @ORM\Column(type="string",length=512)
   */
  protected $slug;

    /**
   * @ORM\Column(type="string",length=256, nullable=true)
   */
  protected $core_facility_email;

  /**
   * @Assert\Regex(
   *     pattern="/<[a-z][\s\S]*>/i",
   *     match=false,
   *     message="URL cannot contain HTML or script tags"
   * )
   * @ORM\Column(type="string",length=1028, nullable=true)
   */
  protected $core_facility_url;

  /**
   * @ORM\ManyToMany(targetEntity="Dataset", mappedBy="core_facilities")
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
    return $this->core_facility_name;
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
     * Set core_facility_name
     *
     * @param string $coreFacilityName
     * @return CoreFacility
     */
    public function setCoreFacilityName($coreFacilityName)
    {
        $this->core_facility_name = $coreFacilityName;

        return $this;
    }

    /**
     * Get core_facility_name
     *
     * @return string 
     */
    public function getCoreFacilityName()
    {
        return $this->core_facility_name;
    }

        /**
     * Set core_facility_email
     *
     * @param string $coreFacilityEmail
     * @return CoreFacility
     */
    public function setCoreFacilityEmail($coreFacilityEmail)
    {
        $this->core_facility_email = strip_tags($coreFacilityEmail);

        return $this;
    }

    /**
     * Get core_facility_email
     *
     * @return string 
     */
    public function getCoreFacilityEmail()
    {
        return $this->core_facility_email;
    }

    /**
     * Set core_facility_url
     *
     * @param string $coreFacilityUrl
     * @return CoreFacility
     */
    public function setCoreFacilityUrl($coreFacilityUrl)
    {
        $this->core_facility_url = $coreFacilityUrl;

        return $this;
    }

    /**
     * Get core_facility_url
     *
     * @return string 
     */
    public function getCoreFacilityUrl()
    {
        return $this->core_facility_url;
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
     * @return CoreFacility
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
     * Serialize all properties
     *
     * @return array
     */
    public function getAllProperties() {
        return array(
            'core_facility_name'=>$this->core_facility_name,
            'core_facility_url'=>$this->core_facility_url,
            'core_facility_email'=>$this->core_facility_email
        );
    }
}
