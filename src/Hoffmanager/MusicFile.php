<?php

namespace Hoffmanager;
use PerrysLambda\StringProperty;

class MusicFile extends StringProperty
{

    public function __construct($filename)
    {
        $utf8file = iconv('ISO-8859-1', 'UTF-8', $filename);
        parent::__construct($utf8file, 'UTF-8');
    }

    public function getExtension()
    {
        $pos = $this->lastIndexOf('.');
        if($pos>=0)
        {
            return $this->substr($pos)->toString();
        }
        return null;
    }

    public function getReal()
    {
        return realpath($this->toString());
    }

    public function getEntropy()
    {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }

}
