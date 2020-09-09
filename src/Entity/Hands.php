<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

class Hands{
  private $handsFilename;
  public $hands;

  public function getHandsFileName(){
    return $this->handsFilename;
  }

  public function setHandsFileName($handsFilename)
  {
    $this->handsFilename = $handsFilename;

    return $this;
  }
}

 ?>
