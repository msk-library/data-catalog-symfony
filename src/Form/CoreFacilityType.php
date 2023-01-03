<?php
namespace App\Form;

use App\Entity\CoreFacility;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Form builder for Core Facility entry
 *
 *   Custom entity added by MSK for specific institutional use
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
class CoreFacilityType extends AbstractType {

  /**
   * Build form
   *
   * @param FormBuilderInterface
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('core_facility_name', TextType::class, ['label'=> 'Core Facility Name', 'label_attr'=>['class'=>'required']]);
    $builder->add('core_facility_url', TextType::class, ['label'=> 'Core Facility URL', 'label_attr'=>['class'=>'no-asterisk']]);
    $builder->add('core_facility_email', TextType::class, ['label'=> 'Core Facility Email', 'label_attr'=>['class'=>'no-asterisk']]);
    $builder->add('save',SubmitType::class,['label'=>'Submit']);
  }

  /**
   * Set default
   *
   * @param OptionsResolverInterface
   */
  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults(['data_class' => CoreFacility::class]);
  }

  public function getName() {
    return 'coreFacility';
  }

}