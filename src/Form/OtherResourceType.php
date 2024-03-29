<?php
namespace App\Form;

use App\Entity\OtherResource;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Form builder for Other Resource entry
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
class OtherResourceType extends AbstractType {

  /**
   * Build the form
   *
   * @param FormBuilderInterface
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('resource_name',TextType::class,['label'=>false, 'required'=>true, 'attr'=>['placeholder'=>'* Resource Name/Type']]
      );
    $builder->add('resource_description',TextType::class,['label'=>false, 'required'=>false, 'attr'=>['placeholder'=>'Resource Description']]
      );
    $builder->add('resource_url',TextType::class,['label'=>false, 'required'=>true, 'attr'=>['placeholder'=>'* Resource URL']]
      );
  }

  /**
   * Set defaults
   *
   * @param OptionsResolver
   */
  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults(['data_class' => OtherResource::class]);
  }

  public function getName() {
    return 'OtherResource';
  }

}

