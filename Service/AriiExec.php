<?php
namespace Arii\I5Bundle\Service;

class AriiExec {
    
    protected $session;
    protected $audit;
    protected $log;
    
    public function __construct(
            \Arii\CoreBundle\Service\AriiSession $session, 
            \Arii\CoreBundle\Service\AriiAudit $audit,  
            \Arii\CoreBundle\Service\AriiLog $log
    ) {
        $this->session = $session;
        $this->audit = $audit;
        $this->log = $log;
    }
    
    public function Exec($command) {
        
        $engine = $this->session->getSpooler();

        if (!isset($engine[0]['shell'])) {
            print "?!";
            exit();
        }
        
        $shell = $engine[0]['shell'];
        $host = $shell['host'];
        $user = $shell['user'];
        $password = $shell['password'];
        
        $method = 'CURL';

        set_include_path(get_include_path() . PATH_SEPARATOR . '../vendor/phpseclib');
        include('Net/SSH2.php');
        
        $ssh = new \Net_SSH2($host);
        if (!$ssh->login($user, $password)) {
            exit('Login Failed');
        }

        return $ssh->exec("system '$command'");
    }
}
?>
