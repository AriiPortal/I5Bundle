<?php

namespace Arii\I5Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessagesController extends Controller
{
    public function dspmsgAction()
    {
        return $this->render('AriiI5Bundle:Messages:dspmsg.html.twig');
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiI5Bundle:Messages:grid_toolbar.xml.twig',array(), $response );
    }

    public function dspmsg_gridAction()
    {
        $I5 = $this->container->get('arii_i5.exec');
        $jobs = $I5->Exec('DSPMSG QSYSOPR');     
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= "<rows>\n";
        $xml .= '<head>
            <afterInit>
                <call command="clearAll"/>
            </afterInit>
        </head>';
        $sys = '';
        $flag = 0;
        foreach (explode("\n",$jobs) as $j) {
//            CPI2404  99  INFORMATION  Attente d'une réponse dans file d'attente de messages QSYSOPR.
//                      EJOBOTOSY4 VABATCH    435288 QMHRCVPM     0000 03.09.15 11:45:59,718436 VABATCH
                    
//+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------
//   QPADEV0005   MUG         435839   MUG         INT    3  20       0,0    0    0,0      0    0,0  CMD-WRKJOB      DSPW          1            
            //    ([ \d]{4})  ([ \d]{4}) ([ \d]{10})
            if (preg_match("/^(\w{7})  (\d{2})  (.{11})  (.*)/",$j,$matches)) {
                $flag = 1;
                $keep = $matches;
            }
            elseif ($flag==1) {
                $xml .= '<row>';
                $flag=0;
                array_shift($keep);                
                foreach($keep as $m) {
                    $xml .= "<cell>".trim(utf8_encode($m))."</cell>";
                }
                //   
                if (preg_match("/^\s{22}(.{10}) (.{10}) (\d{6}) (.{10}) (.{6}) (.{24}) (.*)/",$j,$matches)) {
                    array_shift($matches);                
                    foreach($matches as $m) {
                        $xml .= "<cell>".trim(utf8_encode($m))."</cell>";
                    }
                }
                else {
                    $xml .= "<cell>".utf8_encode($j)."</cell>";
                    
                }
                $xml .= "</row>";
            }
        }
        $xml .= "</rows>\n";
        $response->setContent( $xml );
        return $response;
    }
    
    public function dspmsgd_gridAction()
    {
        $request = Request::createFromGlobals();
        $msg = $request->query->get( 'msg' );

        $I5 = $this->container->get('arii_i5.exec');
        $result = $I5->Exec("DSPMSGD '$msg'");     
        print '<pre>'.utf8_encode($result).'</pre>';
        exit();
    }

}
