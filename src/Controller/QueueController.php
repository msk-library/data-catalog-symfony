<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\SearchResults;
use App\Entity\SearchState;
use App\Entity\Dataset;
use App\Form\DatasetType;
use App\Utils\Slugger;
use Doctrine\ORM\EntityManagerInterface;


/**
 * A simple controller that returns information about unpublished datasets
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
class QueueController extends AbstractController
{
  /**
   * Get the number of unpublished items
   *
   * @return Response A Response instance
   */
  public function queueLengthAction(EntityManagerInterface $em) {

    $queueLength = $em->getRepository(Dataset::Class)
         ->countAllUnpublished();
    

    return $this->render('default/queue_notification.html.twig',['queueLength' => $queueLength, 'adminPage'=>true]);
    
  }


  /**
   * Produce a list of all the unpublished datasets
   *
   * @return Response A Response instance
   *
   * @Route("/admin/approval-queue", name="approval_queue")
   */
   public function viewApprovalQueueAction(EntityManagerInterface $em) {
     
     $approvalQueue = $em->getRepository(Dataset::Class)
          ->findAllUnpublished();

     return $this->render('default/approval_queue.html.twig',['approvalQueue' => $approvalQueue, 'adminPage'=>true]);

   }


  
}
