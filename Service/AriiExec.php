<?php
namespace Arii\I5Bundle\Service;

class AriiExec {
    
    protected $host;
    protected $user;
    protected $password;
    
    public function __construct( $I5) {
        $this->host = $I5['host'];
        $this->user = $I5['user'];
        $this->password = $I5['password'];
    }
    
    public function Exec($command) {
        set_include_path(get_include_path() . PATH_SEPARATOR . '../vendor/phpseclib');
        include('Net/SSH2.php');
        
        $ssh = new \Net_SSH2($this->host);
        if (!$ssh->login($this->user, $this->password)) {
            exit('Login Failed');
        }

        return $ssh->exec("system '$command'");
    }
}
?>
