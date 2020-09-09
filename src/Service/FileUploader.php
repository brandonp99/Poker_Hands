<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader{
  private $slugger;

  public function __construct(SluggerInterface $slugger){
    $this->targetDirectory = '%kernel.project_dir%/public/uploads/pokerHands';
    $this->slugger = $slugger;
  }

  public function upload(UploadedFile $file){
    $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    $safeFileName = $this->slugger->slug($originalFileName);
    $fileName = $safeFileName.'.'.$file->guessExtension();

    try{
      $file->move($this->getTargetDirectory(), $fileName);
    }catch(FileException $e){}

      return $fileName;
  }

  public function getTargetDirectory(){
    return $this->targetDirectory;
  }
}


 ?>
