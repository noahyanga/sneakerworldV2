<?php
session_start();

/*******w******** 
    
    Name: Noah Yanga
    Date: April 22, 2024
    Description: Final Project

****************/

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // If not logged in, redirect to login page with a message
    header("Location: login.php?message=Please login to post");
    exit();
}


require ('connect.php');

if ($_POST) {
    //  Sanitize user input to escape HTML entities and filter out dangerous characters.
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    //  Build the parameterized SQL query and bind to the above sanitized values.
    $query = "INSERT INTO pages (title, content) VALUES (:title, :content)";
    $statement = $db->prepare($query);


    //  Bind values to the parameters
    $statement->bindValue(':title', $title);
    $statement->bindValue(':content', $content);


    //  Execute the INSERT.
    //  execute() will check for possible SQL injection and remove if necessary
    if ($statement->execute()) {
        echo "Thanks for adding your two cents!";
    }

}

// File Uploads
// file_upload_path() - Safely build a path String that uses slashes appropriate for our OS.
// Default upload path is an 'uploads' sub-folder in the current folder.
function file_upload_path($original_filename, $upload_subfolder_name = 'uploads')
{
    $current_folder = dirname(__FILE__);

    // Build an array of paths segment names to be joins using OS specific slashes.
    $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];

    // The DIRECTORY_SEPARATOR constant is OS specific.
    return join(DIRECTORY_SEPARATOR, $path_segments);
}

// file_is_an_image() - Checks the mime-type & extension of the uploaded file for "image-ness".
function file_is_an_image($temporary_path, $new_path)
{
    $allowed_mime_types = ['image/gif', 'image/jpeg', 'image/png'];
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];

    $actual_file_extension = pathinfo($new_path, PATHINFO_EXTENSION);
    $actual_mime_type = getimagesize($temporary_path)['mime'];

    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_type_is_valid = in_array($actual_mime_type, $allowed_mime_types);

    return $file_extension_is_valid && $mime_type_is_valid;
}

$image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);
$upload_error_detected = isset($_FILES['image']) && ($_FILES['image']['error'] > 0);

if ($image_upload_detected) {
    $image_filename = $_FILES['image']['name'];
    $temporary_image_path = $_FILES['image']['tmp_name'];
    $new_image_path = file_upload_path($image_filename);
    if (file_is_an_image($temporary_image_path, $new_image_path)) {
        move_uploaded_file($temporary_image_path, $new_image_path);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <link rel="stylesheet" href="main.css">
    <title>Create a Post</title>
</head>

<body>
    <div class="container">
        <div class="text-center">
            <h1><a href="index.php">👟 SneakerWorld 👟</a></h1>
            <hr>
        </div>

        <form method="post" action="post.php" enctype='multipart/form-data' class="form-horizontal">
            <div class="form-group">
                <label for="title" class="col-sm-2 control-label">Title of Post:</label>
                <div class="col-sm-10">
                    <input type="text" id="title" name="title" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label for="content" class="col-sm-2 control-label">Speak Your Mind:</label>
                <div class="col-sm-10">
                    <textarea id="content" name="content" class="form-control" rows="10" required></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="image" class="col-sm-2 control-label">Image Filename:</label>
                <div class="col-sm-10">
                    <input type="file" name="image" id="image" class="form-control">
                </div>
            </div>
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary">Post</button>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            $('#title').summernote({
                placeholder: 'Enter title here',
                tabsize: 2,
                height: 50
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#content').summernote({
                placeholder: 'Enter content here',
                tabsize: 2,
                height: 200
            });
        });
    </script>
    <footer class="text-center">
        <p>&copy; created by Noah Yanga</p>
    </footer>
</body>

</html>