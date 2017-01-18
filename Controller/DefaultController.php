<?php

namespace Arii\I5Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AriiI5Bundle:Default:index.html.twig');
    }

    public function readmeAction()
    {
        return $this->render('AriiI5Bundle:Default:readme.html.twig');
    }

    public function jobs_toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiI5Bundle:Default:jobs_toolbar.xml.twig',array(), $response );
    }
        
    public function ribbonAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json; charset=utf-8');
        
        $portal = $this->container->get('arii_core.portal');        
        $Nodes=$portal->getNodesBy('vendor', 'iseries');  
        
        return $this->render('AriiI5Bundle:Default:ribbon.json.twig',array('Spoolers' => $Nodes ), $response );
    }
    
}
