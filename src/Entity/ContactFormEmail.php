<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Entity to describe emails submitted via the contact form
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
#[ORM\Table(name: 'contact_form_emails')]
#[ORM\Entity(repositoryClass: \App\Repository\ContactFormEmailRepository::class)]
class ContactFormEmail {
  #[ORM\Column(type: 'integer', name: 'email_id')]
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'AUTO')]
  protected $id;
 
  #[ORM\Column(type: 'string', length: 128)]
  protected $first_name;
  
  #[ORM\Column(type: 'string', length: 128)]
  protected $last_name;

  #[ORM\Column(type: 'string', length: 128)]
  protected $affiliation;

  #[ORM\Column(type: 'string', length: 128, nullable: true)]
  protected $department;

  #[ORM\Column(type: 'string', length: 128)]
  protected $email_address;

  #[ORM\Column(type: 'string', length: 128)]
  protected $reason;


  #[ORM\Column(type: 'string', length: 128, nullable: true)]
  #[Assert\Blank]
  protected $checker;


  #[ORM\Column(type: 'string', length: 1028)]
  protected $message_body;


  /**
   * Get name for display
   *
   * @return string
   */
  public function getDisplayName() {
    return $this->first_name;
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
     * Set first_name
     *
     * @param string $firstName
     * @return ContactFormEmail
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;

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
     * Set last_name
     *
     * @param string $lastName
     * @return ContactFormEmail
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;

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
  

    /** Get full_name
    *
    *  @return string
    */
    public function getFullName()
    {
        return ($this->first_name . " " . $this->last_name);
    }
   
    /**
     * Set affiliation
     *
     * @param string $affiliation
     * @return ContactFormEmail
     */
    public function setAffiliation($affiliation)
    {
        $this->affiliation = $affiliation;

        return $this;
    }

    /**
     * Get affiliation
     *
     * @return string 
     */
    public function getAffiliation()
    {
        return $this->affiliation;
    }

    /**
     * Set department
     *
     * @param string $department
     * @return ContactFormEmail
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return string 
     */
    public function getDepartment()
    {
        return $this->department;
    }

  
   /**
     * Set email_address
     *
     * @param string $emailAddress
     * @return ContactFormEmail
     */
    public function setEmailAddress($emailAddress)
    {
        $this->email_address = $emailAddress;

        return $this;
    }

    /**
     * Get email_address
     *
     * @return string 
     */
    public function getEmailAddress()
    {
        return $this->email_address;
    }

    /**
     * Set reason
     *
     * @param string $reason
     * @return ContactFormEmail
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return string 
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set message_body
     *
     * @param string $messageBody
     * @return ContactFormEmail
     */
    public function setMessageBody($messageBody)
    {
        $this->message_body = $messageBody;

        return $this;
    }

    /**
     * Get message_body
     *
     * @return string 
     */
    public function getMessageBody()
    {
        return $this->message_body;
    }

    /**
     * Set checker
     *
     * @param string $checker
     * @return ContactFormEmail
     */
    public function setChecker($checker)
    {
        $this->checker = $checker;

        return $this;
    }

    /**
     * Get checker
     *
     * @return string 
     */
    public function getChecker()
    {
        return $this->checker;
    }
}
