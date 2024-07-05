<?php
session_start();

/*******w******** 
    
    Name: Noah Yanga
    Date: April 22, 2024
    Description: Final Project

****************/

require('connect.php');
require('authenticate.php');

    // UPDATE quote if title, content and id are present in POST.
    if ($_POST && isset($_POST['update'])) {
        // Sanitize user input to escape HTML entities and filter out dangerous characters.
        $title  = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $id      = filter_input(INPUT_POST, 'page_id', FILTER_SANITIZE_NUMBER_INT);
        
        // Build the parameterized SQL query and bind to the above sanitized values.
        $query     = "UPDATE pages SET title = :title, content = :content WHERE page_id = :page_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':title', $title);
        $statement->bindValue(':content', $content);
        $statement->bindValue(':page_id', $id, PDO::PARAM_INT);
        
        // Execute the INSERT.
        $statement->execute();
        
        // Redirect after update.
        header("Location: index.php?id={$id}");
        exit;
    } 
    elseif ($_POST && isset($_POST['delete'])){
        $id = filter_input(INPUT_POST, 'page_id', FILTER_SANITIZE_NUMBER_INT);
        $query = "DELETE FROM pages WHERE page_id = :page_id";

        $statement = $db->prepare($query);
        $statement->bindValue(':page_id', $id, PDO::PARAM_INT);
        $statement->execute();

        // Redirect after delete.
        header("Location: index.php?id={$id}");
        exit;
    }

    elseif (isset($_GET['page_id'])) { // Retrieve quote to be edited, if id GET parameter is in URL.
        // Sanitize the id. Like above but this time from INPUT_GET.
        $id = filter_input(INPUT_GET, 'page_id', FILTER_SANITIZE_NUMBER_INT);
        
        // Build the parametrized SQL query using the filtered id.
        $query = "SELECT * FROM pages WHERE page_id = :page_id LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':page_id', $id, PDO::PARAM_INT);
        
        // Execute the SELECT and fetch the single row returned.
        $statement->execute();
        $quote = $statement->fetch();
    } else {
        $id = false; // False if we are not UPDATING or SELECTING.
    }

    // File Uploads
    // file_upload_path() - Safely build a path String that uses slashes appropriate for our OS.
    // Default upload path is an 'uploads' sub-folder in the current folder.
    function file_upload_path($original_filename, $upload_subfolder_name = 'uploads') {
       $current_folder = dirname(__FILE__);
       
       // Build an array of paths segment names to be joins using OS specific slashes.
       $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];
       
       // The DIRECTORY_SEPARATOR constant is OS specific.
       return join(DIRECTORY_SEPARATOR, $path_segments);
    }

    // file_is_an_image() - Checks the mime-type & extension of the uploaded file for "image-ness".
    function file_is_an_image($temporary_path, $new_path) {
        $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
        $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];
        
        $actual_file_extension   = pathinfo($new_path, PATHINFO_EXTENSION);
        $actual_mime_type        = getimagesize($temporary_path)['mime'];
        
        $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
        $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);
        
        return $file_extension_is_valid && $mime_type_is_valid;
    }
    
    $image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);
    $upload_error_detected = isset($_FILES['image']) && ($_FILES['image']['error'] > 0);

    if ($image_upload_detected) { 
        $image_filename        = $_FILES['image']['name'];
        $temporary_image_path  = $_FILES['image']['tmp_name'];
        $new_image_path        = file_upload_path($image_filename);
        if (file_is_an_image($temporary_image_path, $new_image_path)) {
            move_uploaded_file($temporary_image_path, $new_image_path);
        }
    }

echo "Logged in As: {$_SESSION['username']}";

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    <link rel="stylesheet" href="main.css">
    <title>Edit this Post!</title>
</head>
<body>
    <div class="container">
        <header class="text-center">
            <h1><a href="index.php">👟 SneakerWorld 👟</a></h1>
        </header>

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <form method="post" action="edit.php" enctype='multipart/form-data' class="form-horizontal">
                    <input type="hidden" name="page_id" value="<?= $quote['page_id'] ?>">

                    <div class="form-group">
                        <label for="title" class="control-label">Title:</label>
                        <input type="text" id="title" name="title" class="form-control" value="<?= $quote['title']?>">
                    </div>

                    <div class="form-group">
                        <label for="content" class="control-label">Content:</label>
                        <div id="content" name="content" class="form-control"><?= $quote['content'] ?></div>
                    </div>

                    <div class="form-group">
                        <label for='image' class="control-label">Image Filename:</label>
                        <input type='file' name='image' id='image' class="form-control">
                    </div>

                    <div class="form-group">
                        <input type='submit' name='submit' value='Upload Image' class="btn btn-primary">
                        <input type="submit" value="Update Post" name="update" class="btn btn-success">
                        <input type="submit" value="Delete" name="delete" class="btn btn-danger">
                    </div>
                </form>
            </div>
        </div>

        <footer class="text-center">
            <p>&copy; 2024 SneakerWorld. No rights reserved.</p>
        </footer>
    </div>

    <script>
        $(document).ready(function() {
            $('#title').summernote({
                placeholder: 'Enter title here',
                tabsize: 2,
                height: 50
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#content').summernote({
                placeholder: 'Enter content here',
                tabsize: 2,
                height: 200
            });
        });
    </script>
</body>
</html>
