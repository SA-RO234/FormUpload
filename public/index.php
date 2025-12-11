<?php
    include "header.php";
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Controller\CloudinaryController;

$root = dirname(__DIR__);
$dotenv = Dotenv::createImmutable($root);
$dotenv->load();

// Handle Upload Image to Cloudinary
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['uploaded_file'])) {
            try {
                $cloudinaryController = new CloudinaryController();
                $result = $cloudinaryController->uploadFileToCloud($_FILES['uploaded_file']['tmp_name'], $_ENV['FOLDER_NAME']);
                    echo '<script>alert("File uploaded successfully!");</script>';
            } catch (Exception $e) {
                echo '<script>alert("Upload failed: ' . addslashes($e->getMessage()) . '");</script>';
            }

    } else {
        echo '<script>alert("No file selected");</script>';
    }
}else if("GET" === $_SERVER['REQUEST_METHOD']){
    try {
        $cloudinaryController = new CloudinaryController();
        $allFiles = $cloudinaryController->getAllFilesFromCloud();
      
    } catch (Exception $e) {
        echo '<script>alert("Failed to retrieve files: ' . addslashes($e->getMessage()) . '");</script>';
    }

}

?>
<body class="bg-gray-100">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="w-64 bg-linear-to-b from-amber-50 to-amber-100 border-r border-gray-200 flex flex-col">
            <!-- Navigation -->
            <div class="flex-1 overflow-y-auto">
                <!-- Library Section -->
                <div class="px-4 py-4">
                    <p class="text-xs font-semibold text-gray-700 uppercase px-2 mb-3">Library</p>
                    <nav class="space-y-2">
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-blue-50 text-blue-600 font-medium">
                            <i class="fas fa-image w-5"></i>
                            <span>Photos</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-layer-group w-5"></i>
                            <span>Video</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-folder w-5"></i>
                            <span>File General(PDF, DOC, TXT)</span>
                        </a>
                    </nav>
                </div>

            </div>

            <!-- Settings -->
            <div class="px-4 py-4 border-t border-gray-200">
                <button class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 w-full">
                    <i class="fas fa-cog w-5"></i>
                    <span>Settings</span>
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col bg-white">
            <!-- Header -->
            <div class="border-b border-gray-200 p-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button class="p-2 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </button>
                    <h1 class="text-2xl font-bold text-gray-900">Photos</h1>
                </div>
                <div class="flex items-center gap-2">
                   
                 <!-- Open the modal using ID.showModal() method -->
<button type="button" class="btn btn-primary" onclick="my_modal_5.showModal()">Upload File</button>
<dialog id="my_modal_5" class="modal modal-bottom sm:modal-middle">
  <div class="modal-box items-center">
    <h3 class="font-bold pb-5 text-lg">Upload your file</h3>
     <form  enctype="multipart/form-data" method="post" action="index.php">
        <input type="file" class="file-input file-input-accent" name="uploaded_file" />
     
    <div class="modal-action">
      <button type="submit" class="btn btn-primary">Upload</button>
      <button class="btn btn-secondary" onclick="my_modal_5.close()" >Close</button>
    </div>
    
    </form>
  </div>
</dialog>
                   
        </div>
            </div>
            <!-- Subheader with Search and View Options -->
            <div class="px-6 py-4 flex items-center justify-between border-b border-gray-200">
               
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                        <input type="text" placeholder="Search Photos" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                  
                </div>
            </div>

            <!-- Photo Grid -->
            <div class="flex-1 overflow-y-auto p-6">
                 <div  class="grid grid-cols-5 gap-4">

              <?php 
          if(is_array($allFiles)){
            foreach($allFiles as $file){
                echo '<div class="bg-white rounded-lg shadow-md overflow-hidden">';
                echo '<img src="' . htmlspecialchars($file['secure_url']) . '" alt="' . htmlspecialchars($file['public_id']) . '" class="w-full h-48 object-cover">';
                echo '<div class="p-4">';
                echo '<h3 class="text-sm font-medium text-gray-900">' . htmlspecialchars($file['public_id']) . '</h3>';
                echo '</div>';
                echo '</div>';
            }
        }
              ?>
               </div>
            </div>
        </div>
    </div>
</body>
</html>
