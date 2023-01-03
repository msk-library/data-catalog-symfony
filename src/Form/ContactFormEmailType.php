<?php
namespace App\Form;

use App\Entity\ContactFormEmail;
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
   */
  public function __construct(array $options = []) {
  }

  /**
   * Build the form
   *
   * @param FormBuilderInterface
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('first_name', TextType::class, ['label'=> 'First Name', 'label_attr'=>[]]);
     $builder->add('last_name', TextType::class, ['label'=> 'Last Name', 'label_attr'=>[]]);
    $builder->add('affiliation', TextType::class, ['label'=>'Affiliation', 'label_attr'=>[]]);
    $builder->add('department', TextType::class, ['label'=> 'Department', 'label_attr'=>['class'=>'no-asterisk']]);
    $builder->add('email_address', EmailType::class, ['label'=> 'E-mail', 'label_attr'=>[]]);
       
    $builder->add('reason', ChoiceType::class, ['label_attr'=>[], 'choices' =>['Suggest a dataset record for inclusion' => 'Suggest a dataset record for inclusion', 'Request a correction to a dataset record' => 'Request a correction to a dataset record', 'Request a presentation' => 'Request a presentation', 'General inquiry'    => 'General inquiry or comments']]);
    $builder->add('message_body', TextareaType::class, ['attr' => ['rows'=>'5'], 'label_attr'=>[], 'label'=>'Please provide some details about your question/comment']);
    $builder->add('checker', TextType::class, ['required'=>false, 'attr'=>['class'=>'checker'], 'label_attr'=>['class'=>'no-asterisk checker']]);
    $builder->add('save',SubmitType::class,['label'=>'Send']);

  }




  /**
   * Set defaults
   *
   * @param OptionsResolver
   */
  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults(['data_class' => ContactFormEmail::class, 'affiliationOptions' => null]);
  }


  public function getName() {
    return 'contactFormEmail';
  }

}

