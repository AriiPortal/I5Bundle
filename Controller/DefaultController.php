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
    
    public function ribbonAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        
        return $this->render('AriiI5Bundle:Default:ribbon.json.twig',array(), $response );
    }
    
}
