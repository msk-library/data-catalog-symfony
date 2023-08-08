<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


/**
 * Form builder for dataset
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
class DatasetType extends AbstractType {

  protected $years;
  protected $yearsIncludingPresent;
  
  /**
   * Build the form
   *
   * @param FormBuilderInterface
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    //identifying information
    $builder->add('dataset_uid', 'text', ['disabled' => true, 'data'     => $this->datasetUid, 'label'    => 'Dataset ID']);
    $builder->add('title', 'text', ['required' => true, 'label'    => 'Dataset Title']);
    $builder->add('dataset_alternate_titles', 'collection', ['type'      => new DatasetAlternateTitleType(), 'required' => false, 'label'     => 'Alternate Titles', 'by_reference'=>false, 'prototype' => true, 'allow_delete' => true, 'allow_add' => true]);
    $builder->add('doi', TextType::class, ['required' => false, 'attr'=>['rows'=>'7', 'placeholder'=>'ex. 10.1158/2159-8290.CD-12-0095'], 'label'    => 'DOI']);
    if ($this->userIsAdmin) {
      $builder->add('origin','choice',['required'=> true, 'label'   => 'Origin', 'choices' => ['Internal'=>'Internal', 'External'=>'External'], 'expanded'=>true]);
    }
    $builder->add('description', 'textarea', ['required' => true, 'attr'=>['rows'=>'7', 'placeholder'=>'Please provide a brief description of the dataset'], 'label'    => 'Description']);
    if ($this->userIsAdmin) {
      $builder->add('published', 'choice', ['required' => true, 'expanded' => true, 'label'    => 'Published to Data Catalog?', 'choice_list'=> new ChoiceList([true, false], ['Yes', 'Not yet'])]);
    }



    if ($this->userIsAdmin) {
      $builder->add('publishers', EntityType::class, ['class'   => \App\Entity\Publisher::class, 'property'=> 'publisher_name', 'required' => false, 'query_builder'=> fn(EntityRepository $er) => $er->createQueryBuilder('u')->orderBy('u.publisher_name','ASC'), 'attr'=>['style'=>'width:100%'], 'multiple' => true, 'by_reference'=>false, 'label'     => 'Publishers']);
      $builder->add('access_restrictions', 'entity', ['class'    => \App\Entity\AccessRestriction::class, 'property' => 'restriction', 'attr'=>['style'=>'width:100%'], 'query_builder'=> fn(EntityRepository $er) => $er->createQueryBuilder('u')->orderBy('u.restriction','ASC'), 'required' => false, 'by_reference'=>false, 'multiple' => true, 'label'     => 'Access Restrictions']);
    }
    $builder->add('access_instructions', 'textarea', ['attr'=>['rows'=>'7', 'placeholder'=>'Provide any information on restrictions or conditions for gaining access to data'], 'label'    => 'Access Instructions']);
  
    //accession information
    $builder->add('data_locations', 'collection', ['type'      => new DataLocationType(), 'required' => false, 'by_reference'=>false, 'label'     => 'Data Location', 'prototype' => true, 'allow_delete' => true, 'allow_add' => true]);
    if ($this->userIsAdmin) {
        $builder->add('pubmed_search', 'text', ['required' => false, 'label'    => 'PubMed Search URL']);
    }
    if ($this->userIsAdmin) {
      $builder->add('date_archived', 'date', ['years'  => $this->years, 'required' => false, 'label'    => 'Date Archived']);
    }
    $builder->add('other_resources', 'collection', ['type'      => new OtherResourceType(), 'required' => false, 'by_reference'=>false, 'label'     => 'Other Resources', 'prototype' => true, 'allow_delete' => true, 'allow_add' => true]);

    //technical details
    $builder->add('dataset_formats', 'entity', ['class'   => \App\Entity\DatasetFormat::class, 'property'=> 'format', 'query_builder'=> fn(EntityRepository $er) => $er->createQueryBuilder('u')->orderBy('u.format','ASC'), 'required' => false, 'attr'    => ['id'=>'dataset_subject_population_ages', 'style'=>'width:100%'], 'multiple' => true, 'by_reference'=>false, 'label'     => 'Dataset Format']);
    $builder->add('dataset_size', 'text', ['required' => false, 'label'    => 'Dataset Size']);
    $builder->add('data_collection_instruments', 'entity', ['class'   => \App\Entity\DataCollectionInstrument::class, 'property'=> 'data_collection_instrument_name', 'required' => false, 'attr'=>['style'=>'width:100%', 'placeholder'=>''], 'multiple' => true, 'by_reference'=>false, 'label'     => 'Data Collection Instruments']);
    $builder->add('data_types', 'entity', ['class'   => \App\Entity\DataType::class, 'property' => 'data_type', 'required' => false, 'query_builder'=> fn(EntityRepository $er) => $er->createQueryBuilder('u')->orderBy('u.data_type','ASC'), 'attr'=>['style'=>'width:100%'], 'multiple' => true, 'by_reference'=>false, 'label'     => 'Data Types']);

    //people and relations
    $builder->add('publications', 'entity', ['class' => \App\Entity\Publication::class, 'property'=>'citation', 'required' => false, 'attr'=>['style'=>'width:100%'], 'multiple' => true, 'by_reference'=>false, 'label'     => 'Publications describing the collection or use of the dataset']);
    $builder->add('awards', 'entity', ['class'   => \App\Entity\Award::class, 'property'=> 'award', 'required' => false, 'attr'    => ['id'=>'dataset_awards', 'style'=>'width:100%'], 'multiple' => true, 'by_reference'=>false, 'label'     => 'Grants']);
    if ($this->userIsAdmin) {
      $builder->add('related_datasets', 'collection', ['type'      => new DatasetRelationshipType(), 'required' => false, 'by_reference'=>false, 'prototype' => true, 'label'     => 'Related Datasets', 'allow_delete' => true, 'allow_add' => true]);
     }
    //content information
    $builder->add('authorships', 'collection', ['class' => \App\Entity\PersonAssociation::class, 'prototype' => true, 'required'=>false, 'by_reference'=>false, 'label'=>'Authors', 'allow_delete'=>true, 'allow_add'=>true]);
    $builder->add('corresponding_authors', 'entity', ['class' => \App\Entity\Person::class, 'property'=>'full_name', 'required'=>false, 'attr'=>['style'=>'width:100%'], 'multiple'=>true, 'by_reference'=>false, 'label'=>'Corresponding Authors']);
    $builder->add('local_experts', 'entity', ['class' => \App\Entity\Person::class, 'property'=>'full_name', 'required'=>false, 'attr'=>['style'=>'width:100%'], 'multiple'=>true, 'by_reference'=>false, 'label'=>'Local Experts']);
    $builder->add('subject_domains', 'entity', ['class' => \App\Entity\SubjectDomain::class, 'property'=>'subject_domain', 'required' => false, 'query_builder'=> fn(EntityRepository $er) => $er->createQueryBuilder('u')->orderBy('u.subject_domain','ASC'), 'attr'=>['style'=>'width:100%'], 'multiple' => true, 'by_reference'=>false, 'label'     => 'Subject Domains']);
    $builder->add('subject_start_date', 'choice', ['choices'  => $this->yearsIncludingPresent, 'required' => false, 'label'    => 'Year Data Collection Started']);
    $builder->add('subject_end_date', 'choice', ['choices'  => $this->yearsIncludingPresent, 'required' => false, 'label'    => 'Year Data Collection Ended']);
    $builder->add('subject_genders', 'entity', ['class'      => \App\Entity\SubjectGender::class, 'property'   => 'subject_gender', 'multiple'   => true, 'expanded'   => true, 'required' => false, 'by_reference'=>false, 'label'     => 'Subject Genders']);
    $builder->add('subject_sexes', 'entity', ['class'      => \App\Entity\SubjectSex::class, 'property'   => 'subject_sex', 'multiple'   => true, 'expanded'   => true, 'required' => false, 'by_reference'=>false, 'label'     => 'Subject Sexes']);
    $builder->add('subject_population_ages', 'entity', ['class'   => \App\Entity\SubjectPopulationAge::class, 'property'=> 'age_group', 'required' => false, 'query_builder'=> fn(EntityRepository $er) => $er->createQueryBuilder('u')->orderBy('u.age_group','ASC'), 'attr'=>['style'=>'width:100%'], 'multiple' => true, 'by_reference'=>false, 'label'     => 'Subject Population Age']);
    $builder->add('subject_geographic_areas', 'entity', ['class'   => \App\Entity\SubjectGeographicArea::class, 'attr'=>['style'=>'width:100%'], 'property'=> 'geographic_area_name', 'query_builder'=> fn(EntityRepository $er) => $er->createQueryBuilder('u')->orderBy('u.geographic_area_name','ASC'), 'required' => false, 'multiple'=> true, 'by_reference'=>false, 'label'     => 'Subject Geographic Areas']);
    $builder->add('subject_geographic_area_details', 'entity', ['class'   => \App\Entity\SubjectGeographicAreaDetail::class, 'attr'=>['style'=>'width:100%'], 'query_builder'=> fn(EntityRepository $er) => $er->createQueryBuilder('u')->orderBy('u.geographic_area_detail_name','ASC'), 'property'=> 'geographic_area_detail_name', 'required' => false, 'multiple'=> true, 'by_reference'=>false, 'label'     => 'Subject Geographic Area Details']);
    $builder->add('study_types', 'entity', ['class'   => \App\Entity\StudyType::class, 'property'=> 'study_type', 'required' => false, 'query_builder'=> fn(EntityRepository $er) => $er->createQueryBuilder('u')->orderBy('u.study_type','ASC'), 'multiple' => true, 'attr'=>['style'=>'width:100%'], 'by_reference'=>false, 'label'     => 'Study Type']);
    $builder->add('subject_keywords', 'entity', ['class'   => \App\Entity\SubjectKeyword::class, 'property'=> 'keyword', 'required' => false, 'query_builder'=> fn(EntityRepository $er) => $er->createQueryBuilder('u')->orderBy('u.keyword','ASC'), 'multiple' => true, 'attr'=>['style'=>'width:100%'], 'by_reference'=>false, 'label'     => 'Subject Keywords']);

    if ($this->userIsAdmin) {
      $builder->add('erd_url', 'text', ['required' => false, 'label'    => 'ERD URL']);
      $builder->add('library_catalog_url', 'text', ['required' => false, 'label'    => 'Library Catalog URL']);
      $builder->add('licensing_details', 'textarea', ['required' => false, 'label'    => 'Licensing Details']);
      $builder->add('license_expiration_date', 'date', ['required' => false, 'label'    => 'License Expiration Date']);
      $builder->add('subscriber', 'text', ['required' => false, 'label'    => 'Subscriber']);
    }


    $builder->add('save',SubmitType::class,["label"=>"Submit", 'attr'=>['class'=>'spacer']]);
     

  }

  public function getName() {
    return 'dataset';
  }

  public function __construct(protected $userIsAdmin = false, protected $datasetUid = 0) {
    $this->years = range(date('Y'),1790);
    $yearList = range(date('Y'),1790);
    array_unshift($yearList, "Present");
    $this->yearsIncludingPresent = array_combine($yearList, $yearList);
  }

  /**
   * Set defaults
   *
   * @param OptionsResolver
   */
  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults(['data_class' => \App\Entity\Dataset::class]);
    $resolver->setDefaults([
      'data_class' => Dataset::class,
      'affiliationOptions' => null,
      // enable/disable CSRF protection for this form
      'csrf_protection' => false,
      // the name of the hidden HTML field that stores the token
      //'csrf_field_name' => '_token',
      // an arbitrary string used to generate the value of the token
      // using a different string for each form improves its security
      //'csrf_token_id'   => 'dataset_form',
    ]);
  }

}
