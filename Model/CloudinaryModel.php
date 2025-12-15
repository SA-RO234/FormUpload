<?php
namespace App\Model;
use App\Config\CloudinaryConfig;
use Exception;
class CloudinaryModel{
    private $configCloud;
    
    public function __construct()
    {
        $config = new CloudinaryConfig();
        $this->configCloud = $config->cloudinary;
    }

    public function uploadFile($filePath, $folder)
    {
        try {
            return $this->configCloud->uploadApi()->upload($filePath, ['folder' => $folder]); 
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getAllFiles(){
        try {
            $image = $this->configCloud->adminApi()->assets([
                'resource_type' => 'image',
                'type' => 'upload',
                'max_results' => 100
            ]);

          if(is_object($image)){
              $imgResult =  json_decode(json_encode($image), true);
              return $imgResult['resources'];
          }
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    //  Delete File from Cloudinary
    public function deleteFile($publicId){
        try {
            return $this->configCloud->uploadApi()->destroy($publicId, ['resource_type' => 'image']);
        } catch (Exception $e) {
            return $e->getMessage();    
        }
    }
}