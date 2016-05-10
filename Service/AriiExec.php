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
        include('Crypt/RSA.php');
        
        $ssh = new \Net_SSH2($host);
        
        if (1) {
            $key = new \Crypt_RSA();
            $ret = $key->loadKey("-----BEGIN RSA PRIVATE KEY-----\nMIICWgIBAAKBgQCzRy01HoHIzBJJb/D8A/eTV3qiZxy0NIR97NE14rJblnJZT5Kg\noP2DvIRzlB0msL5cHQJ/qXYAoemHRDKqNZuj89+MYsBeZqNu3/DXdZLq9XJ8e2rb\nsGrDjHvCHEDWL0JIRFnRacem55+XsUsKTIs4tbcD6adMPIYJSQQ7oB/8AQIBIwKB\ngB67vptkUMNWLwVGY9NuZPSv6SMnnoVK1OJjHIzlCKH8iKGYnMsUSLd/ZynBnpjr\nGVGekrbMl+LZ7YTnHqDV/WxGoWEc3xiHE8/HwZwQZxP92K70inz8+6dGEagsrSqO\nQkdAPR/+qen7uQ9yXqj7WAoNFicPJ2cpo8kuEW33KywzAkEA4yH4jf0uNBFDUkR6\ni9DQC5bsgEloVezWnCsm6eIm5o5SGKPZ6Rpro/h3pq5qvPmCtjrZFnK0Dab9xkFr\n/F9lkwJBAMoQMqxYdnPz74Bto99o0PZrk2ikROwXR9eURi3B4bWGq9+mvN3OEQdE\n8JofGyq60LMlnFAkE7v49fYHziyaFJsCQHTPpGZHsVybKe/LcjlG0WULyhYXH7cp\nWG2SiQqRkFlQgf4LH5xz/Nf8IEcX3x9bv5DrEI8zrQ5V4Zko9bT93HcCQQCEyNDX\np9jP2tCWOWuwEa3jwwkY4PoXfQNTJuxJ9G/AbnDyDnwcup15zje1vKtz2dmaS+pg\njLyC1s2Ea4d8ZUC9AkAeUr/N+011K2zGTjxZnAFY/Ow348bomzddiJYAYA+76exV\n3wUYsjeDxqq8Km93+iMQ8DDNZIvoVcfYQW9BfDlf\n-----END RSA PRIVATE KEY-----   ");
            if (!$ret) {
                echo "loadKey failed\n";
                print "<pre>".$ssh->getLog().'</pre>';
                exit;
            }
        }
        elseif (isset($shell['password'])) {
            $key = $shell['password'];
        }
        else {
            $key = ''; // ?! possible ?
        }
        
        if (!$ssh->login($user, $key)) {
            exit("Login Failed ($user)");
        }

        return $ssh->exec("system '$command'");
    }
}
?>
