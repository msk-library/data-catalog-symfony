<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\SearchResults;
use AppBundle\Entity\SearchState;
use AppBundle\Entity\Dataset;
use AppBundle\Form\Type\DatasetType;
use AppBundle\Utils\Slugger;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


class XSLController extends Controller
{
    public function indexAction(){

        //date_default_timezone_set('UTC');

        $xml = '<?xml version="1.0" encoding="UTF-8" ?>';
        $xml .= '<?xml-stylesheet type="text/xsl" href="xsl/oaitohtml.xsl"?>';
        $xml .= '<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">';
        $xml .= '<responseDate>' . date('Y-m-d\TH:i:s\Z') . '</responseDate>';
        $xml .= '</OAI-PMH>';

        $response = new Response();
        $response->setContent($xml);
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }
} 
