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
                $Connection['password']
            );
            if ($exit==0)
                return $message;
        }
        return "?!";
    }

    private function SSH($command,$host,$user,$auth_method,$password) {

        $host = "va400f1";
        $ssh = new \Net_SSH2($host);

        if (1) {
            $key = new \Crypt_RSA();
            $ret = $key->loadKey('MIICWgIBAAKBgQCzRy01HoHIzBJJb/D8A/eTV3qiZxy0NIR97NE14rJblnJZT5KgoP2DvIRzlB0msL5cHQJ/qXYAoemHRDKqNZuj89+MYsBeZqNu3/DXdZLq9XJ8e2rbsGrDjHvCHEDWL0JIRFnRacem55+XsUsKTIs4tbcD6adMPIYJSQQ7oB/8AQIBIwKBgB67vptkUMNWLwVGY9NuZPSv6SMnnoVK1OJjHIzlCKH8iKGYnMsUSLd/ZynBnpjrGVGekrbMl+LZ7YTnHqDV/WxGoWEc3xiHE8/HwZwQZxP92K70inz8+6dGEagsrSqOQkdAPR/+qen7uQ9yXqj7WAoNFicPJ2cpo8kuEW33KywzAkEA4yH4jf0uNBFDUkR6i9DQC5bsgEloVezWnCsm6eIm5o5SGKPZ6Rpro/h3pq5qvPmCtjrZFnK0Dab9xkFr/F9lkwJBAMoQMqxYdnPz74Bto99o0PZrk2ikROwXR9eURi3B4bWGq9+mvN3OEQdE8JofGyq60LMlnFAkE7v49fYHziyaFJsCQHTPpGZHsVybKe/LcjlG0WULyhYXH7cpWG2SiQqRkFlQgf4LH5xz/Nf8IEcX3x9bv5DrEI8zrQ5V4Zko9bT93HcCQQCEyNDXp9jP2tCWOWuwEa3jwwkY4PoXfQNTJuxJ9G/AbnDyDnwcup15zje1vKtz2dmaS+pgjLyC1s2Ea4d8ZUC9AkAeUr/N+011K2zGTjxZnAFY/Ow348bomzddiJYAYA+76exV3wUYsjeDxqq8Km93+iMQ8DDNZIvoVcfYQW9BfDlf');
            if (!$ret)
                return array(1,"Key failed ! ".$ssh->getLog());
        }
        else {
            $key = $password;
        }
        
        if (!$ssh->login($user, $key)) {
            return array(2,"Login failed ! ".$ssh->getLog());
        }

        return array(0,$ssh->exec("system '$command'"));
    }
    
}
?>
