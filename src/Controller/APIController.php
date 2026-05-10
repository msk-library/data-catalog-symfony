<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Form\DatasetViaApiType;
use App\Entity\Dataset;
use App\Service\SolrIndexer;
use App\Utils\Slugger;
use Throwable;
use OpenApi\Attributes as OA;


/**
 * A controller for producing JSON output
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
class APIController extends AbstractController
{

  /**
   *  We have several pseudo-entities that all relate back to the Person
   *  entity. We'll check this array so we know if we encounter one of them.
   */
  public $personEntities = ['Author', 'LocalExpert', 'CorrespondingAuthor'];

  public function __construct(
    private readonly Security $security,
    private readonly SolrIndexer $solrIndexer
  ) {}

  /**
   * Produce the JSON output
   *
   * @param string $slug The slug of a dataset, or "all"
   * @param string $_format The output format desired
   * @param Request $request The current HTTP request
   *
   * @return Response A Response instance 
   */
  #[Route(path: '/api/Dataset/{uid}.{_format}', name: 'json_output_datasets', defaults: ['uid' => 'all', '_format' => 'json'], methods: ['GET'])]
  #[OA\Get(
    path: '/api/Dataset/{uid}',
    tags: ['Datasets'],
    summary: 'Get dataset(s)',
    description: 'Retrieve published, non-archived dataset(s) in JSON format. Use "all" as the uid to retrieve all datasets or specify a dataset UID for a single dataset.',
    parameters: [
      new OA\Parameter(
        name: 'uid',
        in: 'path',
        description: 'Dataset UID or "all" for all datasets',
        required: true,
        schema: new OA\Schema(type: 'string', default: 'all')
      ),
      new OA\Parameter(
        name: 'output_format',
        in: 'query',
        description: 'Output format: default (entity format), solr (Solr format), or complete (full dataset with relationships)',
        required: false,
        schema: new OA\Schema(type: 'string', enum: ['default', 'solr', 'complete'], default: 'default')
      )
    ],
    responses: [
      new OA\Response(
        response: 200,
        description: 'Successful response with dataset(s)',
        content: new OA\JsonContent(
          type: 'array',
          items: new OA\Items(
            type: 'object',
            properties: [
              new OA\Property(property: 'dataset_uid', type: 'string'),
              new OA\Property(property: 'title', type: 'string'),
              new OA\Property(property: 'summary', type: 'string'),
              new OA\Property(property: 'published', type: 'boolean'),
              new OA\Property(property: 'archived', type: 'boolean')
            ]
          )
        )
      ),
      new OA\Response(response: 404, description: 'Dataset not found')
    ]
  )]
  public function APIDatasetGetAction($uid, $_format, Request $request)
  {

    $em = $this->getDoctrine()->getManager();
    $qb = $em->createQueryBuilder();

    if ($uid == "all") {
      $datasets = $qb->select('d')
        ->from('App:Dataset', 'd')
        ->where('d.archived = 0 OR d.archived IS NULL')
        ->andWhere('d.published = 1')
        ->getQuery()->getResult();
    } else {
      $datasets = $qb->select('d')
        ->from('App:Dataset', 'd')
        ->where('d.dataset_uid = :uid')
        ->andWhere('d.published = 1')
        ->andWhere('d.archived = 0 OR d.archived IS NULL')
        ->setParameter('uid', $uid)
        ->getQuery()->getResult();
    }

    $output_format = $request->get('output_format', 'default');

    switch ($output_format) {
      case "default":
        // default will use the entity's jsonSerialize() method
        $content = $datasets;
        break;
      case "solr":
        // for Solr
        $content = [];
        foreach ($datasets as $dataset) {
          $content[] = $dataset->serializeForSolr();
        }
        break;
      case "complete":
        $content = [];
        foreach ($datasets as $dataset) {
          $content[] = $dataset->serializeComplete();
        }
        break;
      default:
        // default will use the entity's jsonSerialize() method
        $content = $datasets;
    }

    if ($_format == "json") {
      $response = new Response();
      $response->setContent(json_encode($content, JSON_THROW_ON_ERROR));
      $response->headers->set('Content-Type', 'application/json');

      return $response;
    }
  }


  /** 
   * Ingest dataset via API
   *
   * @param Request The current HTTP request
   *
   * @return Response A Response instance
   */
  #[Route(path: '/api/Dataset', methods: ['POST'])]
  #[OA\Post(
    path: '/api/Dataset',
    tags: ['Datasets'],
    summary: 'Create a new dataset',
    description: 'Ingest a new dataset via API. Requires ROLE_API_SUBMITTER permission. New datasets start as unpublished and must be reviewed by administrators before appearing in the catalog.',
    requestBody: new OA\RequestBody(
      required: true,
      content: new OA\JsonContent(
        type: 'object',
        properties: [
          new OA\Property(property: 'title', type: 'string', description: 'Dataset title'),
          new OA\Property(property: 'summary', type: 'string', description: 'Brief description of the dataset'),
          new OA\Property(property: 'dataset_uid', type: 'string', description: 'Unique dataset identifier (auto-generated if not provided)')
        ],
        required: ['title', 'summary']
      )
    ),
    responses: [
      new OA\Response(
        response: 201,
        description: 'Dataset successfully created',
        content: new OA\JsonContent(
          properties: [
            new OA\Property(property: 'message', type: 'string', example: 'Dataset Successfully Added')
          ]
        )
      ),
      new OA\Response(response: 401, description: 'Unauthorized - user does not have ROLE_API_SUBMITTER'),
      new OA\Response(response: 422, description: 'Validation error - invalid dataset data')
    ],
    security: [['api_submitter' => []]]
  )]
  public function APIDatasetPostAction(Request $request)
  {
    $submittedData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
    $dataset = new Dataset();
    $em = $this->getDoctrine()->getManager();
    $userCanSubmit = $this->security->isGranted('ROLE_API_SUBMITTER');

    $datasetUid = $em->getRepository('App:Dataset')
      ->getNewDatasetId();
    $dataset->setDatasetUid($datasetUid);

    if ($userCanSubmit) {
      $form = $this->createForm(new DatasetViaApiType($userCanSubmit, $datasetUid), $dataset, ['csrf_protection' => false]);
      $form->submit($submittedData);
      if ($form->isSubmitted() && $form->isValid()) {
        $dataset = $form->getData();
        // enforce that all datasets ingested via the API will start out unpublished
        $dataset->setPublished(false);
        $addedEntityName = $dataset->getTitle();
        $slug = Slugger::slugify($addedEntityName);
        $dataset->setSlug($slug);

        $em->persist($dataset);
        foreach ($dataset->getAuthorships() as $authorship) {
          $authorship->setDataset($dataset);
          $em->persist($authorship);
        }
        $em->flush();

        try {
          $this->solrIndexer->reindexDataset($dataset);
        } catch (Throwable $e) {
          // Keep API write successful even if Solr is temporarily unavailable.
        }

        return new Response('Dataset Successfully Added', 201);
      } else {
        $errors = $form->getErrorsAsString();
        $response = new Response(json_encode($errors, JSON_THROW_ON_ERROR), 422);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
      }
    } else {
      return new Response('Unauthorized', 401);
    }
  }


  /**
   * Ingest other entities via API
   *
   * @param string $entityName The name of the new entity
   * @param Request the current HTTP request
   *
   * @return Response A Response instance
   */
  #[Route(path: '/api/{entityName}', methods: ['POST'])]
  #[OA\Post(
    path: '/api/{entityName}',
    tags: ['Entities'],
    summary: 'Create a new entity',
    description: 'Ingest a new entity (Author, LocalExpert, CorrespondingAuthor, Publication, CoreFacility, OncoTreeCancerType, etc.) via API. Requires ROLE_API_SUBMITTER permission. Users cannot be added via API.',
    parameters: [
      new OA\Parameter(
        name: 'entityName',
        in: 'path',
        description: 'Entity type name',
        required: true,
        schema: new OA\Schema(type: 'string', example: 'Author')
      )
    ],
    requestBody: new OA\RequestBody(
      required: true,
      description: 'Entity data fields depending on entityName type',
      content: new OA\JsonContent(
        type: 'object'
      )
    ),
    responses: [
      new OA\Response(response: 201, description: 'Entity successfully created'),
      new OA\Response(response: 401, description: 'Unauthorized - user does not have ROLE_API_SUBMITTER'),
      new OA\Response(response: 403, description: 'Forbidden - Users cannot be added via API'),
      new OA\Response(response: 422, description: 'Validation error')
    ],
    security: [['api_submitter' => []]]
  )]
  public function APIEntityPostAction($entityName, Request $request)
  {
    $submittedData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

    if ($entityName == 'User') {
      return new Response('Users cannot be added via API', 403);
    } else {
      $addTemplate = 'add.html.twig';
    }

    $userCanSubmit = $this->security->isGranted('ROLE_API_SUBMITTER');

    //prefix with namespaces so it can be called dynamically
    if (in_array($entityName, $this->personEntities)) {
      $newEntity = \App\Entity\Person::class;
    } else {
      $newEntity = 'App\Entity\\' . $entityName;
    }
    $newEntityFormType = 'App\Form\\' . $entityName . "Type";

    $em = $this->getDoctrine()->getManager();
    if ($userCanSubmit) {
      $form = $this->createForm(
        new $newEntityFormType(),
        new $newEntity(),
        ['csrf_protection' => false]
      );
      $form->submit($submittedData);
      if ($form->isSubmitted() && $form->isValid()) {
        $entity = $form->getData();

        // Create a slug using each entity's getDisplayName method
        $addedEntityName = $entity->getDisplayName();
        $slug = Slugger::slugify($addedEntityName);
        $entity->setSlug($slug);

        $em->persist($entity);
        $em->flush();

        return new Response($entityName . ': "' . $addedEntityName . '" successfully added.', 201);
      } else {
        $errors = $form->getErrorsAsString();
        $response = new Response(json_encode($errors, JSON_THROW_ON_ERROR), 422);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
      }
    } else {
      return new Response('Unauthorized', 401);
    }
  }


  /**
   * List related entities 
   *
   * @param string $slug The slug of an entity, or "all"
   * @param string $_format The output format desired
   * @param Request $request The current HTTP request
   *
   * @return Response A Response instance 
   */
  #[Route(
    path: '/api/{entityName}/{slug}.{_format}',
    name: 'json_output_related',
    defaults: ['slug' => 'all', '_format' => 'json'],
    requirements: ['entityName' => '(?!documentation(?=/|$))[A-Za-z][A-Za-z0-9_]*'],
    methods: ['GET']
  )]
  #[OA\Get(
    path: '/api/{entityName}/{slug}',
    tags: ['Entities'],
    summary: 'Get entity or entities',
    description: 'Retrieve entity data by slug or retrieve all entities if slug is "all". For Publications, the slug is the SynapseID. Users cannot be fetched via API.',
    parameters: [
      new OA\Parameter(
        name: 'entityName',
        in: 'path',
        description: 'Entity type name (Author, Publication, CoreFacility, OncoTreeCancerType, etc.)',
        required: true,
        schema: new OA\Schema(type: 'string')
      ),
      new OA\Parameter(
        name: 'slug',
        in: 'path',
        description: 'Entity slug or "all" for all entities (SynapseID for Publications)',
        required: true,
        schema: new OA\Schema(type: 'string', default: 'all')
      ),
      new OA\Parameter(
        name: 'output_format',
        in: 'query',
        description: 'Output format (json)',
        required: false,
        schema: new OA\Schema(type: 'string', default: 'json')
      )
    ],
    responses: [
      new OA\Response(
        response: 200,
        description: 'Successful response with entity data',
        content: new OA\JsonContent(
          type: 'array',
          items: new OA\Items(type: 'object')
        )
      ),
      new OA\Response(response: 403, description: 'Forbidden - Users cannot be fetched via API')
    ]
  )]
  public function APIEntityGetAction($entityName, $slug, $_format, Request $request)
  {
    if ($entityName == 'User') {
      return new Response('Users cannot be fetched via API', 403);
    }

    $em = $this->getDoctrine()->getManager();
    $qb = $em->createQueryBuilder();
    if (in_array($entityName, $this->personEntities)) {
      $entity = \App\Entity\Person::class;
    } else {
      $entity = 'App\Entity\\' . $entityName;
    }

    if ($slug == "all") {
      $entities = $qb->select('e')
        ->from($entity, 'e')
        ->getQuery()->getResult();
    } else if ($entityName == 'Publication') {
      $entities = $qb->select('e')
        ->from($entity, 'e')
        ->where('e.synapseid = :synapseid')
        ->setParameter('synapseid', $slug)
        ->getQuery()->getResult();
    } else {
      $entities = $qb->select('e')
        ->from($entity, 'e')
        ->where('e.slug = :slug')
        ->setParameter('slug', $slug)
        ->getQuery()->getResult();
    }
    for ($i = 0; $i < (is_countable($entities) ? count($entities) : 0); $i++) {
      $entities[$i] = $entities[$i]->getAllProperties();
    }

    if ($_format == "json") {
      $response = new Response();
      $response->setContent(json_encode($entities, JSON_THROW_ON_ERROR));
      $response->headers->set('Content-Type', 'application/json');

      return $response;
    }
  }
}
