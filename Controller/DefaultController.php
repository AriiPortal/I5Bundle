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
    
    public function ribbonAction()
    {
        // On recupere la liste des base de données
        // si il y en a plus d'une, pour ats, on cree une liste de choix
        $session = $this->container->get('arii_core.session');
        $Spoolers = array();
        $n=0;
        foreach ($session->getSpoolers() as $d) {
            $n++;
            if ($d['type']!='i5') continue;
            $d['id'] = "SP$n";
            array_push($Spoolers,$d);
        }
        
        // Est ce que la database par defaut est en osjs
        $spooler = $session->getSpooler();
        if ($spooler['type']!='i5')
            $session->setSpooler($Spoolers[0]);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json; charset=utf-8');
        
        return $this->render('AriiI5Bundle:Default:ribbon.json.twig',array('Spoolers' => $Spoolers ), $response );
    }
    
}
