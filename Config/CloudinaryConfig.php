<?php
namespace App\Config;
use Dotenv\Dotenv;
use Cloudinary\Cloudinary;

class CloudinaryConfig{
    public $cloudinary;

    public function __construct(){
        $root = dirname(__DIR__); 

        if (file_exists($root . DIRECTORY_SEPARATOR . '.env')) {
            $dotenv = Dotenv::createImmutable($root);
            $dotenv->load();
        }   
        
        $this->cloudinary = new Cloudinary([
            "cloud" => [
                "cloud_name" => $_ENV['CLOUD_NAME'],
                "api_key"    => $_ENV['API_KEY'],
                "api_secret" => $_ENV['API_SECRET'],
            ]
        ]);
    }

    
}
