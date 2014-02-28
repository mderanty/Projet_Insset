<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity */
class Status {
   
     /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $Id_status;

    /** @ORM\Column(type="string") */
    protected $Libelle_status;
    
   
   
    public function getId_status()
    {
        return $this->Id_status;
    }
    public function getLibelle_status()
    {
        return $this->Libelle_status;
    }

    public function setId_status($value)
    {
        $this->Id_status = $value;
    }
    public function setLibelle_status($value)
    {
        $this->Libelle_status = $value;
    }

}