<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use App\Entity\SearchResults;
use App\Entity\SearchState;
use App\Entity\Dataset;
use App\Service\SolrSearchr;
use App\Form\DatasetType;
use App\Form\ContactFormEmailType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Utils\Slugger;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Doctrine\ORM\EntityManagerInterface;
/**
  *  A controller handling the main search functionality, contact and About pages,
  *  dataset views, etc.
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
class GeneralController extends AbstractController
{
  public function __construct(private readonly Security $security, ParameterBagInterface $params) {
    $this->params = $params;
  }





  /**
   * Home page, performs searches and produces results pages
   *
   * @param Request The current HTTP request
   *
   * @return Response A Response instance
   */
  #[Route(path: '/', name: 'homepage')]
  #[Route(path: '/search', name: 'user_search_results')]
  public function indexAction(Request $request, SolrSearchr $solrsearchr ) {
    
    $currentSearch = new SearchState($request);

    //$solr = $this->get('SolrSearchr');
    $solrsearchr->setUserSearch($currentSearch);
    $resultsFromSolr = $solrsearchr->fetchFromSolr();

    $results = new SearchResults($resultsFromSolr);

    if ($results->numResults == 0) {
      return $this->render('default/no_results.html.twig', ['results' => $results, 'currentSearch'=>$currentSearch]);
    } else {
      return $this->render('default/results.html.twig',['results' => $results, 'currentSearch' => $currentSearch]);
    }
    
  }
  
  
  /**
   * Produce the About page, checking if we have an institution-
   * specific version.
   *
   * @param Request The current HTTP request
   *
   * @return Response A Response instance
   */
  #[Route(path: '/about', name: 'about')]
  public function aboutAction(Request $request) {

    if ($this->get('twig')->getLoader()->exists('institution/about.html.twig')) {
      return $this->render('institution/about.html.twig',[]); 
    }
    else {
      return $this->render('about.html.twig', []);
    }

  }


  /**
   * Produce How To Use the Catalog page, checking if we have an institution-
   * specific version.
   *
   * @param Request The current HTTP request
   *
   * @return Response A Response instance
   */
  #[Route(path: '/how-to-use-the-catalog', name: 'how_to_use_catalog')]
  public function howToUseTheCatalogAction(Request $request) {

    if ($this->get('twig')->getLoader()->exists('institution/how_to_use_catalog.html.twig')) {
      return $this->render('institution/how_to_use_catalog.html.twig',[]); 
    }
    else {
      return $this->render('how_to_use_catalog.html.twig', []);
    }

  }


  /**
   * Produce the FAQ page, checking if we have an institution-
   * specific version.
   *
   * @param Request The current HTTP request
   *
   * @return Response A Response instance
   */
  #[Route(path: '/frequently-asked-questions', name: 'faq')]
  public function faqAction(Request $request) {

    if ($this->get('twig')->getLoader()->exists('institution/faq.html.twig')) {
      return $this->render('institution/faq.html.twig',[]); 
    }
    else {
      return $this->render('faq.html.twig', []);
    }
  }


  /**
   * Produce the Contact Us page and send emails to the 
   * users specified in parameters.yml
   * NOTE: The setTo() and setFrom() methods are supposed
   * to accept arrays for multiple recipients, but this appears
   * not to work.
   *
   * @param Request The current HTTP request
   *
   * @return Response A Response instance
   */
  #[Route(path: '/contact-us', name: 'contact')]
  public function contactAction(Request $request, MailerInterface $mailer) {
    $contactFormEmail = new \App\Entity\ContactFormEmail();

    // Get email addresses and institution list from parameters.yml
    $emailTo = $this->params->get('contact_email_to');
    $emailFrom = $this->params->get('contact_email_from');
    $affiliations = $this->params->get('institutional_affiliation_options');
    $affiliationOptions = [];
    foreach ($affiliations as $key=>$value) {
      $affiliationOptions[$value] = $value;
    }
       
      
    $em = $this->getDoctrine()->getManager();
    $form = $this->createForm(ContactFormEmailType::class, $contactFormEmail, ['affiliationOptions'=>$affiliationOptions]);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $email = $form->getData();

      // save their submission to the database first
      $em->persist($email);
      $em->flush();

      $message = (new TemplatedEmail())
        ->subject('New Feedback about Data Catalog')
        ->from($emailFrom)
        ->to($emailTo)
        ->htmlTemplate('default/feedback_email.html.twig')
        ->context(['msg' => $email]);
      $mailer->send($message);

      return $this->render('default/contact_email_send_success.html.twig', ['form' => $form->createView()]);
    }

    return $this->render('default/contact.html.twig', ['form' => $form->createView()]);

  }


  /**
   * Produce the detailed pages for individual datasets
   *
   * @param string $dataset_uid The UID of the dataset to be viewed
   * @param Request The current HTTP request
   *
   * @return Response A Response instance
   */
  #[Route(path: '/dataset/{uid}', name: 'view_dataset')]
  public function viewAction($uid, EntityManagerInterface $em, Request $request) {
    $dataset = $em->getRepository(Dataset::Class)
      ->findOneBy(['dataset_uid'=>$uid]);

    // dataset not found
    if (!$dataset) {
      throw $this->createNotFoundException(
        'No dataset matching ID "' . $uid . '"'
      );
    }

    // if dataset archived
    if ($dataset->getArchived() && !$this->security->isGranted('ROLE_ADMIN')) {
      throw $this->createNotFoundException(
        'Sorry, this dataset is no longer available. Please try another search.'
      );
    }

    

		$view_access=true;

		if (!$dataset->getPublished() && !$this->security->isGranted('ROLE_ADMIN')) {
			
			$view_access=false;
			
			if ($request->get('tak') && !$dataset->getPublished()) {
	
				$tak=$this->getDoctrine()->getRepository('App:TempAccessKey')->findOneBy(['dataset_association'=>$uid, 'uuid'=>$request->get('tak')] );
			
				if (sizeof($tak)>0) {
					
					if (!$tak->getFirstAccess()) {
						
				    $em = $this->getDoctrine()->getManager();
						$tak->setFirstAccess(  new \DateTime() );
						$em->persist($tak);
						$em->flush();
						$view_access=true;
						
					} else {
					
						$tak_ttl="PT72H";
						if ($this->container->hasParameter('tak_ttl')) {
							$tak_ttl=$this->container->getParameter('tak_ttl');
						}					
						if (new \DateTime()<$tak->getFirstAccess()->modify($tak_ttl)) {
							$view_access=true;
						} else {
							throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException(
								'This temporary access link has expired.', null, 403);
						}
					
					}

				}
			
			}

		}


		if ($view_access == false) {

			throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException(
				'Sorry, you are not authorized to view this resource.', null, 403);
		
		}

		if ($dataset->getOrigin() == 'Internal') {
			// return $this->render('default/view_dataset_internal.html.twig', array(
			   return $this->render('view_dataset_external.html.twig', ['dataset' => $dataset]);
		} else {
			return $this->render('view_dataset_external.html.twig', ['dataset' => $dataset]);
		}
  
  }
  
	
		
}
