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

    public function wrkactjob_listAction()
    {
        // date de reference
        $session = $this->container->get('arii_core.session');
        
        // filtres
        $Filter = $session->getUserFilter();
        
        $I5 = $this->container->get('arii_i5.exec');
        $filter = str_replace('%','*',$Filter['job']);
        if ($filter=='*') {        
            $jobs = $I5->Exec('WRKACTJOB');     
        }
        else {
            $jobs = $I5->Exec('WRKACTJOB JOB('.$filter.')');     
        }
        
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
            if (preg_match("/^(\s*\w+)\s+(\w+)\s+(\d+)\s+(\w+)\s+(\w+)\s+(\d+)\s+(\d+)\s+([\d,\+].*?)  ([ \d]{4}) ([ \d,]{6}) ([ \d,]{6}) ([ \d,]{6}) (.{14})  (\w+)  ([ \d]{9})/",$j,$matches)) {
                if (substr($matches[1],2,1)!=' ') {                   
                    $sys = substr($matches[1],1);
                    $matches[1]='';
                }
                else {
                    $matches[1] = substr($matches[1],3);
                }
                
                if (trim($matches[1])=='') continue;
                
                $matches[0] = $sys; 
                switch ($matches[14]) {
                    case 'MSGW':
                        $xml .= "<row style='background-color: #fbb4ae;'>";
                        break;
                    case 'RUN':
                        $xml .= "<row style='background-color: #ffffcc;'>";
                        break;
                    default:
                        $xml .= "<row>";
                        break;
                }
                for($i=0;$i<5;$i++) {
                    $xml .= "<cell>".trim($matches[$i])."</cell>";
                }
                $xml .= "</row>";
            }
        }
        $xml .= "</rows>\n";
        $response->setContent( $xml );
        return $response;
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
print "WRKJOB '$job'";
        $I5 = $this->container->get('arii_i5.exec');
        $result = $I5->Exec("WRKJOB '$job'");     
        print '<pre>'.utf8_encode($result).'</pre>';
        exit();
    }

    public function dspjoblog_gridAction()
    {
        $request = Request::createFromGlobals();
        $job = $request->query->get( 'job' );

        $I5 = $this->container->get('arii_i5.exec');
        $result = $I5->Exec("DSPJOBLOG '$job'");
        // $result = utf8_decode(file_get_contents('c:\temp\dspjoblog.txt'));
        $n = -1;
        $sub = 'INFO';
        $a_voir = '';
                
        $DSP=array();        
        
        $result = str_replace("\r\n","\n",$result);        
        $Text = explode("\n",$result);

        if (!isset($Text[2])) {
            print "<pre>".utf8_encode($result)."</pre>";
            exit();
        }
        
        // Entête
        $Job['name']=substr($Text[1],35,10);
        $Job['user']=substr($Text[1],73,10);
        $Job['run']=substr($Text[1],119,6);
        $Job['desc']=substr($Text[2],35,10);
        $Job['lib']=substr($Text[2],73,10);
        
        $l = 3;
        while (isset($Text[$l])) {
            $line = $Text[$l];
            // pagination
            if (preg_match('/ Page \s+\d+/',$line)) {
                $l += 3;
                continue;
            }
            // IDMSG      TYPE                    GRV  DATE      HEURE             DE PGM     BIBLIO     INST     VERS PGM      BIBLIO     INST
            // CPC1163    Exécution               00   06.05.16  16:58:12,725010  QWTCCRLJ     QSYS        00CD     *EXT                    *N
            elseif (preg_match('/^IDMSG/',$line)) {
                $l += 1;
                continue;
            }
            elseif (preg_match('/^(.{10}) (.{23}) (.{2})   (\d{2}\.\d{2}\.\d{2})  (\d{2}:\d{2}:\d{2},\d{6})  (.{10})   (.{10})  (.{8}) (.{10})  (.{10})  (.*)/',$line,$matches)) {
                // decoupage du bloc precedent
                if (isset($DSP[$n]['bloc'])) {
                    $DSP[$n]['infos'] = $this->unbloc($DSP[$n]['bloc']);
                }
                $n++;
                $DSP[$n]['bloc'] = '';
                $DSP[$n]['id'] = trim($matches[1]);
                $DSP[$n]['type'] = utf8_encode(trim($matches[2]));
                $DSP[$n]['grv'] = trim($matches[3]);
                $DSP[$n]['date'] = $matches[4];
                $DSP[$n]['time'] = $matches[5];
                $DSP[$n]['de_pgm']  = trim($matches[6]);
                $DSP[$n]['de_lib'] = trim($matches[7]);
                $DSP[$n]['de_inst'] = trim($matches[8]);
                $DSP[$n]['a_pgm']  = trim($matches[9]);
                $DSP[$n]['a_lib'] = trim($matches[10]);
                $DSP[$n]['a_inst'] = trim($matches[11]);
                
                // bootstrap
                switch ($DSP[$n]['type']) {
                    case 'Echappement':
                        $DSP[$n]['color'] = 'danger';
                        break;
                    case 'Information':
                        $DSP[$n]['color'] = 'info';
                        break;
                    case 'Exécution':
                        $DSP[$n]['color'] = 'success';
                        break;
                    case 'Diagnostic':
                        $DSP[$n]['color'] = 'warning';
                        break;
                    case 'Demande':
                        $DSP[$n]['color'] = 'active';
                        break;
                    default:
                        $DSP[$n]['color'] = '';
                }
            }
            else {
                if (isset($DSP[$n]['bloc'])) {
                    $DSP[$n]['bloc'] .= ' '.trim($line)."\n";
                }
                else {
                    $a_voir .= $line;
                }
            }
            $l++;
        }
        // dernier bloc
        if (isset($DSP[$n]['bloc'])) {
            $DSP[$n]['infos'] = $this->unbloc($DSP[$n]['bloc']);
        }
        krsort($DSP);
//        print_r($DSP);
//        exit();
        return $this->render('AriiI5Bundle:Jobs:dspjoblog.html.twig',array('job' => $Job, 'dspjoblog' => $DSP) );
    }

    private function unbloc($bloc) {
        $Infos = array();
        $sub='';
        
        $bloc = utf8_encode(trim(str_replace(' Que faire . ',"\nQue faire . ",$bloc)));
        foreach (explode("\n",$bloc) as $line) {
            if (($p=strpos($line,'. :'))>0) {
                $sub = trim(substr($line,0,strpos($line,' .')));
                $Infos[$sub] = trim(substr($line,$p+3));
            }
            elseif (isset($Infos[$sub])) {
                $Infos[$sub] .= ' '.trim($line);
            }
        }
        // Cas particulier des listes"
        foreach (array('Que faire','Cause','Réponses possibles') as $k ) {
            if (isset($Infos[$k])) {
                $Infos[$k] = $this->Reponses(trim($Infos[$k]));
            }
        }
        return $Infos;
    }
    
    private function Reponses($text) {
        $Liste = array();
        $type='';
        
       if (($p = strpos($text,' -- '))>0) {
           // on recule pour trouver le debut
           do {
               $p--;
               $c = substr($text,$p,1);
           } while (!in_array($c,array(':','.')) and ($p>0));
           $debut = substr($text,0,$p+1);
           
           $fin = substr($text,$p+1);           
           // ondécoupe la fin
           $Liste = explode(' -- ',$fin);
           
           $text = $debut;
           // Si le premier est vide, c'est une liste simple, sinon choix multiple

           if ($Liste[0]=='') {
               $type='li';
               array_shift($Liste);
           }
           else {
               
               $type='dl';
               $car = trim(array_shift($Liste));
               $L = array();               
               $i=0;
               while (isset($Liste[$i])) {
                   $var = trim($Liste[$i]);
                   if (isset($Liste[$i+1]))
                       $L[$car] = substr($var,0,strlen($var)-1);
                   else 
                       $L[$car] = $var;
                   $car = substr($var,-1);
                   $i++;
               }
               $Liste=$L;
           }
       }
       return array('text'=>$text,'type' => $type,'list'=>$Liste);
    }
    
    public function dspjoblog_grid2Action()
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
