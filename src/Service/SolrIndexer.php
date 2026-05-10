<?php

namespace App\Service;

use App\Entity\Dataset;
use DateTimeInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SolrIndexer
{
  public function __construct(
    private readonly HttpClientInterface $httpClient,
    private readonly ParameterBagInterface $params
  ) {}

  public function reindexDataset(Dataset $dataset): void
  {
    $datasetUid = (int) $dataset->getDatasetUid();

    // Keep Solr in sync with published/non-archived state.
    if (!$dataset->getPublished() || $dataset->getArchived()) {
      $this->removeDatasetFromSolr($datasetUid);
      return;
    }

    $doc = $this->normalizeSolrDocument($dataset->serializeForSolr());
    $payload = json_encode($doc, JSON_THROW_ON_ERROR);

    $this->httpClient->request('POST', $this->getSolrJsonDocsUpdateUrl(), [
      'headers' => [
        'Content-Type' => 'application/json',
      ],
      'body' => $payload,
    ]);
  }

  public function reindexDatasetsForEntity(object $entity): int
  {
    $datasets = $this->collectAffectedDatasets($entity);

    foreach ($datasets as $dataset) {
      $this->reindexDataset($dataset);
    }

    return count($datasets);
  }

  public function removeDatasetFromSolr(int $datasetUid): void
  {
    $payload = json_encode(['delete' => ['id' => $datasetUid]], JSON_THROW_ON_ERROR);

    $this->httpClient->request('POST', $this->getSolrUpdateUrl(), [
      'headers' => [
        'Content-Type' => 'application/json',
      ],
      'body' => $payload,
    ]);
  }

  private function collectAffectedDatasets(object $entity): array
  {
    $byUid = [];

    if ($entity instanceof Dataset) {
      $byUid[(int) $entity->getDatasetUid()] = $entity;
      return array_values($byUid);
    }

    if (method_exists($entity, 'getDatasets')) {
      foreach ($entity->getDatasets() as $dataset) {
        if ($dataset instanceof Dataset) {
          $byUid[(int) $dataset->getDatasetUid()] = $dataset;
        }
      }
    }

    if (method_exists($entity, 'getDatasetAssociations')) {
      foreach ($entity->getDatasetAssociations() as $association) {
        if (method_exists($association, 'getDataset')) {
          $dataset = $association->getDataset();
          if ($dataset instanceof Dataset) {
            $byUid[(int) $dataset->getDatasetUid()] = $dataset;
          }
        }
      }
    }

    if (method_exists($entity, 'getDataset')) {
      $dataset = $entity->getDataset();
      if ($dataset instanceof Dataset) {
        $byUid[(int) $dataset->getDatasetUid()] = $dataset;
      }
    }

    return array_values($byUid);
  }

  private function normalizeSolrDocument(array $doc): array
  {
    if (!empty($doc['dataset_start_date']) && !empty($doc['dataset_end_date'])) {
      $startDate = (string) $doc['dataset_start_date'];
      $endDate = (string) $doc['dataset_end_date'];
      $endYear = ($endDate === 'Present') ? (int) date('Y') + 1 : (int) $endDate;
      $startYear = (int) $startDate;

      if ($startYear > 0 && $endYear >= $startYear) {
        $doc['dataset_years'] = [];
        for ($year = $startYear; $year <= $endYear; $year++) {
          $doc['dataset_years'][] = sprintf('%d-01-01T00:00:00Z', $year);
        }
      }
    }

    foreach ($doc as $field => $value) {
      if ($value instanceof DateTimeInterface) {
        $doc[$field] = $value->format('Y-m-d\\T00:00:00\\Z');
        continue;
      }

      if ($value === null) {
        $doc[$field] = '';
      }
    }

    return $doc;
  }

  private function getSolrCoreBaseUrl(): string
  {
    $solrCoreUrl = $this->params->get('solrsearchr_server');
    return rtrim((string) $solrCoreUrl, '/');
  }

  private function getSolrJsonDocsUpdateUrl(): string
  {
    return $this->getSolrCoreBaseUrl() . '/update/json/docs?commit=true&overwrite=true';
  }

  private function getSolrUpdateUrl(): string
  {
    return $this->getSolrCoreBaseUrl() . '/update?commit=true';
  }
}
