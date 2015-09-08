<?php
namespace Arii\I5Bundle\Service;

class AriiExec {
    
    protected $session; 
    
    public function __construct(  \Arii\CoreBundle\Service\AriiSession $session) {
        $this->session = $session;
        set_include_path(get_include_path() . PATH_SEPARATOR . '../vendor/phpseclib');
        include('Net/SSH2.php');   
    }
    
    public function Exec($host,$user,$password,$command) {
        $ssh = new \Net_SSH2($host);
        if (!$ssh->login($user, $password)) {
            exit('Login Failed');
        }

        return $ssh->exec("system '$command'");
    }
}
?>
