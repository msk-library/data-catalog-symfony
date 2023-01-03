<?php
namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;



/**
 * An entity describing a person
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
 */
#[ORM\Table(name: 'person')]
#[ORM\Entity(repositoryClass: \App\Repository\PersonRepository::class)]
#[UniqueEntity('kid')]
#[UniqueEntity('full_name')]
class Person {
  #[ORM\Column(type: 'integer', name: 'person_id')]
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'AUTO')]
  protected $id;

  #[ORM\Column(type: 'string', length: 128, unique: true)]
  protected $full_name;

  #[ORM\Column(type: 'string', length: 128)]
  protected $slug;


  #[ORM\Column(type: 'string', length: 128, nullable: true)]
  protected $last_name;

  #[ORM\Column(type: 'string', length: 128, nullable: true)]
  protected $first_name;


  #[ORM\Column(type: 'string', length: 16, nullable: true, unique: true)]
  protected $kid;


  #[ORM\Column(type: 'string', length: 128, nullable: true, unique: true)]
  protected $orcid_id;


  #[ORM\Column(type: 'string', length: 1028, nullable: true)]
  protected $bio_url;


  #[ORM\Column(type: 'string', length: 256, nullable: true)]
  protected $email;


  #[ORM\Column(type: 'boolean', options: ['default' => false], nullable: true)]
  protected $works_here;


  #[ORM\OneToMany(targetEntity: 'PersonAssociation', mappedBy: 'person')]
  protected $datasetAssociations;


  /**
   * Get name for display
   *
   * @return string
   */
  public function getDisplayName() {
    return $this->full_name;
  }

    /**
     * Constructor
     */
    public function __construct()
    {
      $this->datasetAssociations = new ArrayCollection();
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
     * Set full_name
     *
     * @param string $fullName
     * @return Person
     */
    public function setFullName($fullName)
    {
        $this->full_name = strip_tags($fullName);

        return $this;
    }

    /**
     * Get full_name
     *
     * @return string 
     */
    public function getFullName()
    {
        return $this->full_name;
    }

    /**
     * Set last_name
     *
     * @param string $lastName
     * @return Person
     */
    public function setLastName($lastName)
    {
        $this->last_name = strip_tags($lastName);

        return $this;
    }

    /**
     * Get last_name
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Set first_name
     *
     * @param string $firstName
     * @return Person
     */
    public function setFirstName($firstName)
    {
        $this->first_name = strip_tags($firstName);

        return $this;
    }

    /**
     * Get first_name
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set kid
     *
     * @param string $kid
     * @return Person
     */
    public function setKid($kid)
    {
        $this->kid = $kid;

        return $this;
    }

    /**
     * Get kid
     *
     * @return string 
     */
    public function getKid()
    {
        return $this->kid;
    }

    /**
     * Set bio_url
     *
     * @param string $bioUrl
     * @return Person
     */
    public function setBioUrl($bioUrl)
    {
        $this->bio_url = strip_tags($bioUrl);

        return $this;
    }

    /**
     * Get bio_url
     *
     * @return string 
     */
    public function getBioUrl()
    {
        return $this->bio_url;
    }


    /**
     * Set email
     *
     * @param string $email
     * @return Person
     */
    public function setEmail($email)
    {
        $this->email = strip_tags($email);

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }


    /**
     * Set slug
     *
     * @param string $slug
     * @return Person
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
     * Set orcid_id
     *
     * @param string $orcidId
     * @return Person
     */
    public function setOrcidId($orcidId)
    {
        $this->orcid_id = $orcidId;

        return $this;
    }

    /**
     * Get orcid_id
     *
     * @return string 
     */
    public function getOrcidId()
    {
        return $this->orcid_id;
    }


    /**
     * Set works_here
     *
     * @param string $worksHere
     * @return Person
     */
    public function setWorksHere($worksHere)
    {
        $this->works_here = $worksHere;

        return $this;
    }


    /**
     * Get works_here
     *
     * @return string 
     */
    public function getWorksHere()
    {
        return $this->works_here;
    }


    public function getDatasetAssociations()
    {
      return $this->datasetAssociations->toArray();
    }

    public function addDatasetAssociation(PersonAssociation $assoc) 
    {
      if (!$this->datasetAssociations->contains($assoc)) {
        $this->datasetAssociations->add($assoc);
      }
      return $this;
    }

    public function removeDatasetAssociation(PersonAssociation $assoc)
    {
      if ($this->datasetAssociations->contains($assoc)) {
        $this->datasetAssociations->removeElement($assoc);
      }

      return $this;
    }

    public function getAssociatedDatasets()
    {
      return array_map(
        fn($association) => $association->getDataset(),
        $this->datasetAssociations->toArray()
      );
    }

    /**
     * Serialize all properties
     *
     * @return array
     */
    public function getAllProperties() {
      return ['full_name'=>$this->full_name, 'last_name'=>$this->last_name, 'first_name'=>$this->first_name, 'orcid_id'=>$this->orcid_id, 'bio_url'=>$this->bio_url, 'email'=>$this->email, 'works_here'=>$this->works_here];
    }

    public function isWorksHere(): ?bool
    {
        return $this->works_here;
    }
}
