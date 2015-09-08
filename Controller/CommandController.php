<?php

namespace Arii\I5Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommandController extends Controller
{
    public function consoleAction()
    {
        return $this->render('AriiI5Bundle:Command:console.html.twig');
    }
    
    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiI5Bundle:Command:toolbar.xml.twig',array(), $response );
    }

    public function execAction()
    {
        $request = Request::createFromGlobals();
        $exec = $request->query->get( 'exec' );

        $I5 = $this->container->get('arii_i5.exec');
        $result = $I5->Exec('VA400Q1','autosys','oto6new',$exec);     
        print '<pre>'.utf8_encode($result).'</pre>';
        exit();
    }

}
