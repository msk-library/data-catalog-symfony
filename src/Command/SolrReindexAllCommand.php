<?php

namespace App\Command;

use App\Entity\Dataset;
use App\Service\SolrIndexer;
use Throwable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:solr:reindex-all')]
class SolrReindexAllCommand extends Command
{
  public function __construct(
    private readonly ManagerRegistry $doctrine,
    private readonly SolrIndexer $solrIndexer
  ) {
    parent::__construct();
  }

  protected function configure(): void
  {
    $this
      ->addOption('job-id', null, InputOption::VALUE_REQUIRED, 'Unique job id')
      ->addOption('status-file', null, InputOption::VALUE_REQUIRED, 'Absolute path to JSON status file');
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $jobId = (string) $input->getOption('job-id');
    $statusFile = (string) $input->getOption('status-file');
    if ($jobId === '' || $statusFile === '') {
      return Command::INVALID;
    }

    $datasets = $this->doctrine->getRepository(Dataset::class)->findAll();
    $total = count($datasets);
    $startedAt = new \DateTimeImmutable('now');

    $status = [
      'jobId' => $jobId,
      'state' => 'running',
      'startedAt' => $startedAt->format(DATE_ATOM),
      'finishedAt' => null,
      'total' => $total,
      'processed' => 0,
      'indexed' => 0,
      'removed' => 0,
      'removedStale' => 0,
      'errors' => [],
      'message' => 'Reindex in progress',
    ];

    $this->writeStatus($statusFile, $status);

    try {
      $dbDatasetIds = [];
      foreach ($datasets as $dataset) {
        if ($dataset instanceof Dataset) {
          $dbDatasetIds[(int) $dataset->getDatasetUid()] = true;
        }
      }

      foreach ($datasets as $dataset) {
        if (!$dataset instanceof Dataset) {
          continue;
        }

        try {
          $isPublished = (bool) $dataset->getPublished();
          $isArchived = (bool) $dataset->getArchived();
          $this->solrIndexer->reindexDataset($dataset);

          if ($isPublished && !$isArchived) {
            $status['indexed']++;
          } else {
            $status['removed']++;
          }
        } catch (Throwable $e) {
          $status['errors'][] = [
            'datasetUid' => (int) $dataset->getDatasetUid(),
            'title' => (string) $dataset->getTitle(),
            'message' => $e->getMessage(),
          ];
        }

        $status['processed']++;
        $this->writeStatus($statusFile, $status);
      }

      try {
        $solrDatasetIds = $this->solrIndexer->fetchIndexedDatasetIds();
        foreach ($solrDatasetIds as $solrId) {
          if (!isset($dbDatasetIds[(int) $solrId])) {
            try {
              $this->solrIndexer->removeDatasetFromSolr((int) $solrId);
              $status['removedStale']++;
            } catch (Throwable $e) {
              $status['errors'][] = [
                'datasetUid' => (int) $solrId,
                'title' => 'Stale Solr document',
                'message' => $e->getMessage(),
              ];
            }
          }
        }
      } catch (Throwable $e) {
        $status['errors'][] = [
          'datasetUid' => null,
          'title' => 'Stale-document cleanup',
          'message' => $e->getMessage(),
        ];
      }

      $status['state'] = 'completed';
      $status['message'] = count($status['errors']) > 0
        ? 'Reindex completed with errors'
        : 'Reindex completed successfully';
      $status['finishedAt'] = (new \DateTimeImmutable('now'))->format(DATE_ATOM);
      $this->writeStatus($statusFile, $status);

      return Command::SUCCESS;
    } catch (Throwable $e) {
      $status['state'] = 'failed';
      $status['finishedAt'] = (new \DateTimeImmutable('now'))->format(DATE_ATOM);
      $status['message'] = 'Reindex failed unexpectedly';
      $status['errors'][] = [
        'datasetUid' => null,
        'title' => 'Fatal error',
        'message' => $e->getMessage(),
      ];
      $this->writeStatus($statusFile, $status);

      return Command::FAILURE;
    }
  }

  private function writeStatus(string $statusFile, array $status): void
  {
    $dir = dirname($statusFile);
    if (!is_dir($dir)) {
      mkdir($dir, 0775, true);
    }
    file_put_contents($statusFile, json_encode($status, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
  }
}
