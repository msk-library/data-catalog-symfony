<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class OAIController extends Controller
{
    /**
   * Takes incoming OAI requests and provides appropriate response
   *
   * @param Request The current HTTP request
   *
   * @return Response A Response instance
   *
   * @Route("/oai/", name="oai_base")
   * 
   */
    public function indexAction(Request $request){
        date_default_timezone_set('UTC');

        $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'];
        $base_identifier = $_SERVER['SERVER_NAME'];

        if(isset($_GET['identifier'])){
            $dataset_uid = explode(':', $_GET['identifier']);
            $dataset_uid = (int)end($dataset_uid);
            $results = $this->getDoctrine()->getRepository('AppBundle:Dataset')->findBy(['dataset_uid'=>$dataset_uid]);
        }
        else{
            $results = $this->getDoctrine()->getRepository('AppBundle:Dataset')->findAll();
        }
        //dump($results);

        $template = '';
        $verb = isset($_GET['verb'])?trim($_GET['verb']):'Identify';
        switch($verb){
            case 'Identify':
                $template = 'oai_identify.xml.twig';
                break;
            case 'ListRecords':
                $template = 'oai_list_records.xml.twig';
                break;
            case 'GetRecord':
                $template = 'oai_list_records.xml.twig';
                break;
            case 'ListSets':
                $template = 'oai_list_sets.xml.twig';
                break;
            case 'ListMetadataFormats':
                    $template = 'oai_list_metadata_formats.xml.twig';
                    break;
            case 'ListIdentifiers':
                $template = 'oai_list_identifiers.xml.twig';
                break;
        }
        $response = new Response(
            $this->renderView('oai_base.xml.twig', array(
                'oai_template' => $template,
                'timestamp' => date('Y-m-d\TH:i:s\Z'),
                'base_url' => $base_url,
                'base_identifier' => $base_identifier,
                'publisher' => '',
                'results' => $results
            ))
        );
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }
} 
