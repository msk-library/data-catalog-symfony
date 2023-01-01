<?php
namespace App\Form;

use App\Entity\DataCollectionInstrument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Form builder for Data Collection Instrument entry
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
class DataCollectionInstrumentType extends AbstractType {

  /**
   * Build the form
   *
   * @param FormBuilderInterface
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('data_collection_instrument_name', TextType::class, ['label'=>'Instrument Name']);
    $builder->add('url', TextType::class, ['label'=>'URL of Instrument Description', 'required'=>false]);
    $builder->add('notes', TextAreaType::class, ['label'=>'Notes', 'required'=>false]);
    $builder->add('save',SubmitType::class,['label'=>'Submit']);
  }

  /**
   * Set defaults
   *
   * @param OptionsResolver
   */
  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults(['data_class' => DataCollectionInstrument::class]);
  }

  public function getName() {
    return 'dataCollectionInstrument';
  }

}

