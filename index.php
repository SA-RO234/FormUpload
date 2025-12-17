<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Controller\CloudinaryController;

// Project root is THIS directory
$root = __DIR__;

$dotenv = Dotenv::createImmutable($root);
$dotenv->load();


// Handle API requests (GET for images list)
if(isset($_GET['api']) && $_GET['api'] === 'getImages'){
    header('Content-Type: application/json');
    try {
        $cloudinaryController = new CloudinaryController();
        $files = $cloudinaryController->getAllFilesFromCloud();
        echo json_encode(['status' => 'success', 'data' => $files]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}

if(isset($_GET['api']) && $_GET['api'] === 'getGeneralFiles'){
    header('Content-Type: application/json');
    try {
        $cloudinaryController = new CloudinaryController();
        $files = $cloudinaryController->getAllGeneralFilesFromCloud($_GET['folder'] ?? null);
        echo json_encode(['status' => 'success', 'data' => $files]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}

// Handle DELETE request FIRST before any output
if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
    header('Content-Type: application/json');
    parse_str(file_get_contents("php://input"), $deleteVars);
    if (isset($deleteVars['public_id'])) {
        try {
            $cloudinaryController = new CloudinaryController();
            $result = $cloudinaryController->deleteFileFromCloud($deleteVars['public_id']);
            echo json_encode(['status' => 'success', 'message' => 'File deleted successfully']);
            exit;
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Delete failed: ' . $e->getMessage()]);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No public_id provided']);
        exit;
    }
}
// Now include header for page rendering
include "header.php";
?>
<script>
// Load images from Cloudinary in real-time
function loadImages() {
    fetch('index.php?api=getImages')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                renderGallery(data.data);
            } else {
                console.error('API Error:', data.message);
                renderGallery([]);
            }
        })
        .catch(error => {
            console.error('Error loading images:', error);
            renderGallery([]);
        });
}
//  Show all general files (pdf, docx, etc)
function loadGeneralFiles(folder = null) {
    let url = 'index.php?api=getGeneralFiles';
    if (folder) {
        url += `&folder=${encodeURIComponent(folder)}`;
    }
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                renderGallery(data.data);
            } else {
                console.error('API Error:', data.message);
                renderGallery([]);
            }
        })
        .catch(error => {
            console.error('Error loading general files:', error);
            renderGallery([]);
        });
}   

// Render gallery from image data
function renderGallery(files) {
    const gallery = document.querySelector('#all-gallery');
    if (!gallery) {
        console.error('Gallery container not found');
        return;
    }
    
    gallery.innerHTML = ''; // Clear existing images
    
    if (!files || files.length === 0) {
        gallery.innerHTML = '<p class="text-gray-500">No images found</p>';
        return;
    }
    
    files.forEach(file => {
        const div = document.createElement('div');
        div.className = 'bg-white relative flex justify-center items-center flex-col rounded-lg shadow-md overflow-hidden';
        div.innerHTML = `
            <button onclick="deleteFile('${file.public_id}')" class="absolute cursor-pointer top-5 right-2 bg-white bg-opacity-75 rounded-full p-[5px_10px] hover:bg-opacity-100 z-10">
                <i class="fas fa-trash text-gray-600"></i>
            </button>
            <img src="${file.secure_url}" alt="${file.public_id}" class="w-full h-48 object-cover">
            <div class="p-4">
                <h3 class="text-sm font-medium text-gray-900">${file.public_id}</h3>
            </div>
        `;
        gallery.appendChild(div);
    });
}

function deleteFile(publicId) {
    fetch('index.php', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'public_id': publicId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            // Reload images from Cloudinary to show real-time changes
            loadImages();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Delete Error:', error);
        alert('Failed to delete file: ' + error.message);
    });
}

// Load images when page loads
document.addEventListener('DOMContentLoaded', loadImages);
</script>
<?php

// Handle Upload Image to Cloudinary
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['uploaded_file'])) {
            try {
                $cloudinaryController = new CloudinaryController();
                $result = $cloudinaryController->uploadFileToCloud($_FILES['uploaded_file']['tmp_name'], $_ENV['FOLDER_NAME']);
                echo '<script>alert("File uploaded successfully!"); my_modal_5.close(); loadImages();</script>';
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
                        <a href="" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
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
                 <div id="all-gallery" class="grid grid-cols-5 gap-4">
                     <!-- Images will be loaded here by JavaScript -->
                 </div>
            </div>
        </div>
    </div>
</body>
</html>
