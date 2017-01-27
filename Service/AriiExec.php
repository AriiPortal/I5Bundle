<?php
namespace Arii\I5Bundle\Service;

class AriiExec {
    
    protected $portal;
    protected $audit;
    protected $log;
    
    public function __construct(
            \Arii\CoreBundle\Service\AriiPortal $portal, 
            \Arii\CoreBundle\Service\AriiAudit $audit,  
            \Arii\CoreBundle\Service\AriiLog $log
    ) {
        $this->portal = $portal;
        $this->audit = $audit;
        $this->log = $log;

        set_include_path(get_include_path() . PATH_SEPARATOR . '../vendor/phpseclib');
        include('Net/SSH2.php');
        include('Crypt/RSA.php');
                
    }
    
    public function Exec($command) {
        
        // Noeud en cours
        $Node = $this->portal->getNode();

        // Recupérer les connections de ce noeud
        foreach ($Node['Connections'] as $Connection) {         
            list($exit,$message) = $this->SSH(
                $command,
                $Connection['interface'],
                $Connection['login'],
                $Connection['auth_method'],
                $Connection['password'],
                $Connection['key']
            );
            if ($exit==0)
                return $message;
        }
        return $message;
    }

    private function SSH($command,$host,$user,$auth_method,$password,$private_key) {

        $ssh = new \Net_SSH2($host);
        if ($auth_method=='password') {
            if (!$ssh->login($user, $password))
                return array(1,"Login failed ! ".$ssh->getLog());
        }
        else {
            $key = new \Crypt_RSA();
            if (!$key->loadKey($private_key))
                return array(2,"Key failed ! ".$ssh->getLog());
            if (!$ssh->login($user, $key))
                return array(3,"Login failed ! ".$ssh->getLog());
        }
        
        return array(0,$ssh->exec("system '$command'"));
    }
    
}
?>
