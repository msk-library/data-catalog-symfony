<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Form builder for Contact Us form
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
 */
class ContactFormEmailType extends AbstractType {

  protected $options;
  protected $affiliationOptions;

  /**
   * Build institutional affiliation options list
   * 
   * @param array $options
   */
  public function __construct(array $options = []) {
  }

  /**
   * Build the form
   *
   * @param FormBuilderInterface
   * @param array $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('first_name', TextType::class, array(
      'label'=> 'First Name',
      'label_attr'=>array(),
    ));
     $builder->add('last_name', TextType::class, array(
      'label'=> 'Last Name',
      'label_attr'=>array(),
    ));
    $builder->add('affiliation', TextType::class, array(
      'label'=>'Affiliation',
      'label_attr'=>array(),
    ));
    $builder->add('department', TextType::class, array(
      'label'=> 'Department',
      'label_attr'=>array('class'=>'no-asterisk'),
    ));
    $builder->add('email_address', EmailType::class, array(
      'label'=> 'E-mail',
      'label_attr'=>array(),
    ));
       
    $builder->add('reason', ChoiceType::class, array(
      'label_attr'=>array(),
      'choices' =>array(
        'Suggest a dataset record for inclusion' => 'Suggest a dataset record for inclusion',
        'Request a correction to a dataset record' => 'Request a correction to a dataset record',
        'Request a presentation' => 'Request a presentation',
        'General inquiry'    => 'General inquiry or comments',
      )
    ));
    $builder->add('message_body', TextareaType::class, array(
      'attr' => array('rows'=>'5'),
      'label_attr'=>array(),
      'label'=>'Please provide some details about your question/comment',
    ));
    $builder->add('checker', TextType::class, array(
      'required'=>false,
      'attr'=>array('class'=>'checker'),
      'label_attr'=>array('class'=>'no-asterisk checker')));
    $builder->add('save',SubmitType::class,array('label'=>'Send'));

  }




  /**
   * Set defaults
   *
   * @param OptionsResolver
   */
  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults(array(
        'data_class' => 'App\Entity\ContactFormEmail',
        'affiliationOptions' => null,
    ));
  }


  public function getName() {
    return 'contactFormEmail';
  }

}

