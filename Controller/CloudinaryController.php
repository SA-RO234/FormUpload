<?php 
Namespace App\Controller;
use App\Model\CloudinaryModel;

class CloudinaryController{
     private $cloudModel;

     public function __construct() {
         $this->cloudModel = new CloudinaryModel();
     }

    //  Upload File
    public function uploadFileToCloud($filePath , $folder){
             return  $this->cloudModel->uploadFile($filePath , $folder);     
    }

    // get All Files

    public function getAllFilesFromCloud(){
        return $this->cloudModel->getAllFiles();
    }
}