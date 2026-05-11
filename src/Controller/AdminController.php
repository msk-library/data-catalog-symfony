<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\SearchResults;
use App\Entity\SearchState;
use App\Entity\Dataset;
use App\Form\DatasetType;
use App\Utils\Slugger;
use Symfony\Component\Process\Process;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use OpenApi\Attributes as OA;


/**
 * A controller to handle the Admin section
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
class AdminController extends AbstractController
{
  private const SOLR_INDEX_JOBS_DIR = 'var/solr-index-jobs';
  private const SOLR_INDEX_START_TIMEOUT_SECONDS = 20;

  public function __construct(private readonly ParameterBagInterface $params) {}

  /**
   * Produce the main admin dashboard
   *
   * @param Request The current HTTP request
   *
   * @return Response A Response instance
   */
  #[Route(path: '/dashboard', name: 'admin_panel')]
  public function adminAction(Request $request)
  {
    return $this->render('default/admin-home.html.twig', ['adminPage' => true]);
  }


  /**
   * Produce the entity management page
   *
   * @param Request The current HTTP request
   *
   * @return Response A Response instance
   */
  #[Route(path: '/manage', name: 'admin_manage')]
  public function adminManageAction(Request $request)
  {


    return $this->render('default/admin-manage.html.twig', ['adminPage' => true]);
  }

  /**
   * Produce the user management page
   *
   * @param Request The current HTTP request
   *
   * @return Response A Response instance
   */
  #[Route(path: '/users', name: 'admin_users')]
  public function adminUsersAction(Request $request)
  {


    return $this->render('default/admin-users.html.twig', ['adminPage' => true]);
  }

  #[Route(path: '/admin/solr-index', name: 'admin_solr_index', methods: ['GET'])]
  #[OA\Get(
    path: '/admin/solr-index',
    tags: ['Solr Administration'],
    summary: 'View Solr index admin page',
    description: 'Display the Solr indexing admin dashboard with controls for starting a full reindex and monitoring progress.',
    responses: [
      new OA\Response(
        response: 200,
        description: 'Admin dashboard HTML page'
      ),
      new OA\Response(response: 403, description: 'Forbidden - Admin access required')
    ],
    security: [['admin_role' => []]]
  )]
  public function solrIndexAction(): Response
  {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $jobsDir = $this->getSolrJobsDir();
    $latestJobId = $this->findLatestJobId($jobsDir);

    return $this->render('default/admin-solr-index.html.twig', [
      'adminPage' => true,
      'latestJobId' => $latestJobId,
      'latestStatusUrl' => $latestJobId ? $this->generateUrl('admin_solr_index_status', ['jobId' => $latestJobId]) : null,
      'logUrlTemplate' => $this->generateUrl('admin_solr_index_log', ['jobId' => 'JOB_ID']),
    ]);
  }

  #[Route(path: '/admin/solr-index/start', name: 'admin_solr_index_start', methods: ['POST'])]
  #[OA\Post(
    path: '/admin/solr-index/start',
    tags: ['Solr Administration'],
    summary: 'Start full Solr reindex',
    description: 'Trigger a full reindex of all datasets and related entities into Solr. This is a background job that processes datasets incrementally. Returns the job ID for status tracking.',
    responses: [
      new OA\Response(
        response: 200,
        description: 'Reindex job started successfully',
        content: new OA\JsonContent(
          properties: [
            new OA\Property(property: 'jobId', type: 'string', description: 'Unique job identifier'),
            new OA\Property(property: 'statusUrl', type: 'string', description: 'Relative URL to check job status')
          ]
        )
      ),
      new OA\Response(response: 403, description: 'Forbidden - Admin access required'),
      new OA\Response(response: 409, description: 'Conflict - A reindex job is already running')
    ],
    security: [['admin_role' => []]]
  )]
  public function startSolrIndexAction(Request $request): JsonResponse
  {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $csrfToken = (string) $request->request->get('_token', '');
    if (!$this->isCsrfTokenValid('admin-solr-index', $csrfToken)) {
      return new JsonResponse(['error' => 'Invalid CSRF token'], 400);
    }

    $projectDir = rtrim((string) $this->params->get('kernel.project_dir'), '/');
    $jobsDir = $this->getSolrJobsDir();
    if (!is_dir($jobsDir)) {
      mkdir($jobsDir, 0775, true);
    }

    $runningJobs = glob($jobsDir . '/job-*.json') ?: [];
    foreach ($runningJobs as $jobFile) {
      $data = json_decode((string) @file_get_contents($jobFile), true);
      if (!is_array($data)) {
        continue;
      }

      if (($data['state'] ?? '') === 'starting' && $this->isStartTimedOut($data)) {
        $data['state'] = 'failed';
        $data['finishedAt'] = (new \DateTimeImmutable('now'))->format(DATE_ATOM);
        $data['message'] = 'Worker startup timed out before progress was reported.';
        $data['errors'][] = [
          'datasetUid' => null,
          'title' => 'Job monitor',
          'message' => 'Worker startup timed out before progress was reported.',
        ];
        $this->writeStatusFile($jobFile, $data);
      }

      if (($data['state'] ?? '') === 'running' || ($data['state'] ?? '') === 'starting') {
        $existingJobId = (string) ($data['jobId'] ?? '');
        return new JsonResponse([
          'error' => 'A reindex job is already running.',
          'jobId' => $existingJobId !== '' ? $existingJobId : null,
          'statusUrl' => $existingJobId !== '' ? $this->generateUrl('admin_solr_index_status', ['jobId' => $existingJobId]) : null,
        ], 409);
      }
    }

    $jobId = bin2hex(random_bytes(8));
    $statusFile = $jobsDir . '/job-' . $jobId . '.json';
    file_put_contents($statusFile, json_encode([
      'jobId' => $jobId,
      'state' => 'starting',
      'startedAt' => (new \DateTimeImmutable('now'))->format(DATE_ATOM),
      'message' => 'Starting reindex job',
      'total' => 0,
      'processed' => 0,
      'indexed' => 0,
      'removed' => 0,
      'removedStale' => 0,
      'pid' => null,
      'errors' => [],
    ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

    $console = $projectDir . '/bin/console';
    $logFile = $jobsDir . '/job-' . $jobId . '.log';
    $phpExecutable = 'php';

    // Detach worker from web request lifecycle; write stdout/stderr to per-job log file.
    $launchCmd = sprintf(
      'nohup %s %s app:solr:reindex-all --job-id=%s --status-file=%s >> %s 2>&1 & echo $!',
      escapeshellarg($phpExecutable),
      escapeshellarg($console),
      escapeshellarg($jobId),
      escapeshellarg($statusFile),
      escapeshellarg($logFile)
    );
    $launcher = Process::fromShellCommandline($launchCmd, $projectDir);
    $launcher->setTimeout(15);
    $launcher->run();

    $launchedPid = (int) trim($launcher->getOutput());
    $statusData = [
      'jobId' => $jobId,
      'state' => 'running',
      'startedAt' => (new \DateTimeImmutable('now'))->format(DATE_ATOM),
      'finishedAt' => null,
      'message' => 'Reindex worker started',
      'total' => 0,
      'processed' => 0,
      'indexed' => 0,
      'removed' => 0,
      'removedStale' => 0,
      'pid' => $launchedPid > 0 ? $launchedPid : null,
      'errors' => [],
    ];
    $this->writeStatusFile($statusFile, $statusData);

    usleep(300000);
    if (!$launcher->isSuccessful() || $launchedPid <= 0 || !$this->isPidRunning($launchedPid)) {
      $startupError = trim($launcher->getErrorOutput() . "\n" . $launcher->getOutput());
      if ($startupError === '') {
        $startupError = 'Detached reindex worker failed to start.';
      }

      file_put_contents($logFile, $startupError . "\n", FILE_APPEND);
      $failed = $this->readStatusFile($statusFile) ?? $statusData;
      $failed['state'] = 'failed';
      $failed['finishedAt'] = (new \DateTimeImmutable('now'))->format(DATE_ATOM);
      $failed['message'] = 'Failed to start reindex worker';
      $failed['errors'][] = [
        'datasetUid' => null,
        'title' => 'Worker startup',
        'message' => $startupError,
      ];
      $this->writeStatusFile($statusFile, $failed);
    }

    return new JsonResponse([
      'jobId' => $jobId,
      'statusUrl' => $this->generateUrl('admin_solr_index_status', ['jobId' => $jobId]),
    ]);
  }

  #[Route(path: '/admin/solr-index/status/{jobId}', name: 'admin_solr_index_status', methods: ['GET'])]
  #[OA\Get(
    path: '/admin/solr-index/status/{jobId}',
    tags: ['Solr Administration'],
    summary: 'Get reindex job status',
    description: 'Retrieve the current status and progress of a Solr reindex job. Returns counters for total, processed, indexed, and removed documents.',
    parameters: [
      new OA\Parameter(
        name: 'jobId',
        in: 'path',
        description: 'Job ID returned from reindex start endpoint',
        required: true,
        schema: new OA\Schema(type: 'string')
      )
    ],
    responses: [
      new OA\Response(
        response: 200,
        description: 'Job status',
        content: new OA\JsonContent(
          properties: [
            new OA\Property(property: 'jobId', type: 'string'),
            new OA\Property(property: 'state', type: 'string', enum: ['starting', 'running', 'completed', 'failed']),
            new OA\Property(property: 'total', type: 'integer', description: 'Total datasets to process'),
            new OA\Property(property: 'processed', type: 'integer', description: 'Datasets processed so far'),
            new OA\Property(property: 'indexed', type: 'integer', description: 'Datasets successfully indexed'),
            new OA\Property(property: 'removed', type: 'integer', description: 'Datasets removed from Solr'),
            new OA\Property(property: 'removedStale', type: 'integer', description: 'Stale documents removed from Solr'),
            new OA\Property(property: 'pid', type: 'integer', description: 'Process ID of background worker'),
            new OA\Property(property: 'errors', type: 'array', items: new OA\Items(type: 'string')),
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'startedAt', type: 'string', format: 'date-time'),
            new OA\Property(property: 'finishedAt', type: 'string', format: 'date-time')
          ]
        )
      ),
      new OA\Response(response: 404, description: 'Job not found')
    ],
    security: [['admin_role' => []]]
  )]
  public function solrIndexStatusAction(string $jobId): JsonResponse
  {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $safeJobId = preg_replace('/[^a-f0-9]/i', '', $jobId);
    $statusFile = $this->getSolrJobsDir() . '/job-' . $safeJobId . '.json';
    if (!is_file($statusFile)) {
      return new JsonResponse(['error' => 'Job not found'], 404);
    }

    $data = json_decode((string) file_get_contents($statusFile), true);
    if (!is_array($data)) {
      return new JsonResponse(['error' => 'Invalid status payload'], 500);
    }

    if (($data['state'] ?? '') === 'starting' && $this->isStartTimedOut($data)) {
      $data['state'] = 'failed';
      $data['finishedAt'] = (new \DateTimeImmutable('now'))->format(DATE_ATOM);
      $data['message'] = 'Worker startup timed out before progress was reported.';
      $data['errors'][] = [
        'datasetUid' => null,
        'title' => 'Job monitor',
        'message' => 'Worker startup timed out before progress was reported.',
      ];
      $this->writeStatusFile($statusFile, $data);
    }

    if (($data['state'] ?? '') === 'running' && isset($data['pid']) && (int) $data['pid'] > 0 && !$this->isPidRunning((int) $data['pid'])) {
      $data['state'] = 'failed';
      $data['finishedAt'] = (new \DateTimeImmutable('now'))->format(DATE_ATOM);
      $data['message'] = 'Worker process is no longer running before completion.';
      $data['errors'][] = [
        'datasetUid' => null,
        'title' => 'Job monitor',
        'message' => 'Worker process is no longer running before completion.',
      ];
      $this->writeStatusFile($statusFile, $data);
    }

    $response = new JsonResponse($data);
    $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    $response->headers->set('Pragma', 'no-cache');

    return $response;
  }

  #[Route(path: '/admin/solr-index/log/{jobId}', name: 'admin_solr_index_log', methods: ['GET'])]
  #[OA\Get(
    path: '/admin/solr-index/log/{jobId}',
    tags: ['Solr Administration'],
    summary: 'Get reindex job log',
    description: 'Retrieve the last 200 lines of the reindex job log output for debugging and monitoring.',
    parameters: [
      new OA\Parameter(
        name: 'jobId',
        in: 'path',
        description: 'Job ID returned from reindex start endpoint',
        required: true,
        schema: new OA\Schema(type: 'string')
      )
    ],
    responses: [
      new OA\Response(
        response: 200,
        description: 'Last lines of job log',
        content: new OA\JsonContent(
          properties: [
            new OA\Property(property: 'jobId', type: 'string'),
            new OA\Property(property: 'log', type: 'string', description: 'Last 200 lines of log output')
          ]
        )
      ),
      new OA\Response(response: 404, description: 'Job not found')
    ],
    security: [['admin_role' => []]]
  )]
  public function solrIndexLogAction(string $jobId): JsonResponse
  {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $safeJobId = preg_replace('/[^a-f0-9]/i', '', $jobId);
    $logFile = $this->getSolrJobsDir() . '/job-' . $safeJobId . '.log';
    if (!is_file($logFile)) {
      $response = new JsonResponse([
        'jobId' => $safeJobId,
        'logTail' => '',
      ]);
      $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
      $response->headers->set('Pragma', 'no-cache');

      return $response;
    }

    $response = new JsonResponse([
      'jobId' => $safeJobId,
      'logTail' => $this->readLogTail($logFile, 200),
    ]);
    $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    $response->headers->set('Pragma', 'no-cache');

    return $response;
  }

  private function getSolrJobsDir(): string
  {
    $projectDir = rtrim((string) $this->params->get('kernel.project_dir'), '/');
    return $projectDir . '/' . self::SOLR_INDEX_JOBS_DIR;
  }

  private function findLatestJobId(string $jobsDir): ?string
  {
    $statusFiles = glob($jobsDir . '/job-*.json') ?: [];
    if (count($statusFiles) === 0) {
      return null;
    }

    usort($statusFiles, static function (string $a, string $b): int {
      return filemtime($b) <=> filemtime($a);
    });

    $latest = basename($statusFiles[0]);
    if (preg_match('/^job-([a-f0-9]+)\.json$/i', $latest, $matches)) {
      return $matches[1];
    }

    return null;
  }

  private function readLogTail(string $logFile, int $maxLines): string
  {
    $lines = @file($logFile, FILE_IGNORE_NEW_LINES);
    if ($lines === false || count($lines) === 0) {
      return '';
    }

    $tail = array_slice($lines, -1 * max(1, $maxLines));
    return implode("\n", $tail);
  }

  private function writeStatusFile(string $statusFile, array $data): void
  {
    file_put_contents($statusFile, json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
  }

  private function readStatusFile(string $statusFile): ?array
  {
    $raw = @file_get_contents($statusFile);
    if ($raw === false || $raw === '') {
      return null;
    }

    $data = json_decode($raw, true);
    return is_array($data) ? $data : null;
  }

  private function isStartTimedOut(array $data): bool
  {
    $startedAt = $data['startedAt'] ?? null;
    if (!is_string($startedAt) || $startedAt === '') {
      return true;
    }

    try {
      $started = new \DateTimeImmutable($startedAt);
    } catch (\Throwable $e) {
      return true;
    }

    $threshold = (new \DateTimeImmutable('now'))->modify('-' . self::SOLR_INDEX_START_TIMEOUT_SECONDS . ' seconds');
    return $started < $threshold;
  }

  private function isPidRunning(int $pid): bool
  {
    if ($pid <= 0) {
      return false;
    }

    if (function_exists('posix_kill')) {
      return @posix_kill($pid, 0);
    }

    $probe = new Process(['ps', '-p', (string) $pid, '-o', 'pid=']);
    $probe->setTimeout(2);
    $probe->run();
    return $probe->isSuccessful() && trim($probe->getOutput()) !== '';
  }
}
