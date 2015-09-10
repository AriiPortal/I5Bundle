<?php

namespace Arii\I5Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JobsController extends Controller
{
    public function wrkactjobAction()
    {
        return $this->render('AriiI5Bundle:Jobs:wrkactjob.html.twig');
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiI5Bundle:Jobs:grid_toolbar.xml.twig',array(), $response );
    }

    public function wrkusrjobAction()
    {
        return $this->render('AriiI5Bundle:Jobs:wrkusrjob.html.twig');
    }

    public function wrkactjob_gridAction()
    {
        $I5 = $this->container->get('arii_i5.exec');
        $jobs = $I5->Exec('WRKACTJOB');     
        
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
        foreach (explode("\n",$jobs) as $j) {
//+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------
//   QPADEV0005   MUG         435839   MUG         INT    3  20       0,0    0    0,0      0    0,0  CMD-WRKJOB      DSPW          1            
            //    ([ \d]{4})  ([ \d]{4}) ([ \d]{10})
            if (preg_match("/^(\s*\w+)\s+(\w+)\s+(\d+)\s+(\w+)\s+(\w+)\s+(\d+)\s+(\d+)\s+([\d,].*?)  ([ \d]{4}) ([ \d,]{6}) ([ \d,]{6}) ([ \d,]{6}) (.{14})  (\w{4})  ([ \d]{9})/",$j,$matches)) {
                switch ($matches[14]) {
                    case 'MSGW':
                        $xml .= "<row style='background-color: #fbb4ae;'>";
                        break;
                    default:
                        $xml .= "<row>";
                        break;
                }
                if (substr($matches[1],2,1)!=' ') {                   
                    $sys = substr($matches[1],1);
                    $matches[1]='';
                }
                else {
                    $matches[1] = substr($matches[1],3);
                }
                $matches[0] = $sys;
                foreach($matches as $m) {
                    $xml .= "<cell>".trim($m)."</cell>";
                }
                $xml .= "</row>";
            }
        }
        $xml .= "</rows>\n";
        $response->setContent( $xml );
        return $response;
    }

    public function wrkusrjob_gridAction()
    {
        $I5 = $this->container->get('arii_i5.exec');
        $jobs = $I5->Exec('WRKUSRJOB VABATCH');     
        
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
        foreach (explode("\n",$jobs) as $j) {
//+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------
//     EAIAJOURCT   VABATCH      346673   BATCH     OUTQ
            //         (.{6})   (.{8}) (.{16})  (.{16})    
            if (preg_match("/^   (.{10})   (.{10})   (.{6})   (.{8})  (.*)/",$j,$matches)) {
                if ($matches[1]=='          ') continue;
                array_shift($matches);                
                $xml .= "<row>";
                foreach($matches as $m) {                    
                    $xml .= "<cell>".trim($m)."</cell>";
                }
                $xml .= "</row>";
            }
        }
        $xml .= "</rows>\n";
        $response->setContent( $xml );
        return $response;
    }

    public function dspjob_grid2Action()
    {
        $I5 = $this->container->get('arii_i5.exec');
        $jobs = $I5->Exec('DSPLOG');     
        print "<pre>";
        print_r($jobs);
        print "</pre>";
        exit();
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
        foreach (explode("\n",$jobs) as $j) {
            if (preg_match("/^(\s*\w+)\s+(\w+)\s+(\d+)\s+(\w+)\s+(\w+)\s+(\d+)\s+(\d+)\s+([\d,]+)  (.{14})  (\w{4})\s+(\d*)/",$j,$matches)) {
                $xml .= "<row>";
                if (substr($matches[1],2,1)!=' ') {                   
                    $sys = substr($matches[1],1);
                    $matches[1]='';
                }
                else {
                    $matches[1] = substr($matches[1],3);
                }
                $matches[0] = $sys;
                foreach($matches as $m) {
                    $xml .= "<cell>$m</cell>";
                }
                $xml .= "</row>";
            }
        }
        $xml .= "</rows>\n";
        $response->setContent( $xml );
        return $response;
    }

    public function wrkjob_gridAction()
    {
        $request = Request::createFromGlobals();
        $job = $request->query->get( 'job' );

        $I5 = $this->container->get('arii_i5.exec');
        $result = $I5->Exec("WRKJOB '$job'");     
        print '<pre>'.utf8_encode($result).'</pre>';
        exit();
    }

    public function dspjob_gridAction()
    {
        $request = Request::createFromGlobals();
        $job = $request->query->get( 'job' );

        $I5 = $this->container->get('arii_i5.exec');
        $result = $I5->Exec("DSPJOB '$job'");     
        print '<pre>'.utf8_encode($result).'</pre>';
        exit();
    }

    public function dspjoblog_gridAction()
    {
        $request = Request::createFromGlobals();
        $job = $request->query->get( 'job' );

        $I5 = $this->container->get('arii_i5.exec');
        $result = $I5->Exec("DSPJOBLOG '$job'");     
        print '<pre>'.$this->screen(utf8_encode($result)).'</pre>';
        exit();
    }

    public function wrksbmjob_gridAction()
    {
        $request = Request::createFromGlobals();
        $job = $request->query->get( 'job' );

        $I5 = $this->container->get('arii_i5.exec');
        $result = $I5->Exec("WRKSBMJOB 'QJVQEXEC'");     
        print '<pre>'.utf8_encode($result).'</pre>';
        exit();
    }

    private function screen($text)
    {        
        return $text;
    }
}
