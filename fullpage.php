<?php
session_start();

/*******w******** 
    
    Name: Noah Yanga
    Date: April 22, 2024
    Description: Final Project

****************/

require('connect.php');

    // get full post
    $query = "SELECT * FROM pages WHERE page_id = :page_id LIMIT 1";
    $statement = $db->prepare($query);
    
    // Sanitize $_GET['id'] to ensure it's a number.
    $id = filter_input(INPUT_GET, 'page_id', FILTER_SANITIZE_NUMBER_INT);
    $content = filter_input(INPUT_GET, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $title = filter_input(INPUT_GET, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      
    
    
    // Bind the :id parameter in the query to the sanitized
    // $id specifying a binding-type of Integer.
    $statement->bindValue('page_id', $id, PDO::PARAM_INT);
    $statement->execute();


    // get comments
    $commentQuery = "SELECT * FROM comments WHERE comment_id = :comment_id";
    $commentStatement = $db->prepare($commentQuery);
    $commentStatement->bindValue('comment_id', $id, PDO::PARAM_INT);
    $commentStatement->execute();

    
    // Fetch the row selected by primary key id.
    // $row = $statement->fetch();

    // if ($_POST && !empty($_POST['content'])) {
    //     //  Sanitize user input to escape HTML entities and filter out dangerous characters.
    //     $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
    //     //  Build the parameterized SQL query and bind to the above sanitized values.
    //     $query = "INSERT INTO comments (content) VALUES (:content)";
    //     $statement = $db->prepare($query);

    //     //  Bind values to the parameters
    //     $statement->bindValue(':content', $content);

    //     //  Execute the INSERT.
    //     //  execute() will check for possible SQL injection and remove if necessary
    //     if($statement->execute()) {
    //         $row = $statement->fetch();
    //         echo "Thanks for adding your two cents!";
    //     }

    // }

    // add comment
    if ($_POST) {
        //  Sanitize user input to escape HTML entities and filter out dangerous characters.
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        //  Build the parameterized SQL query and bind to the above sanitized values.
        $query = "INSERT INTO comments (content) VALUES (:content)";
        $statement = $db->prepare($query);
        
        //  Bind values to the parameters
        $statement->bindValue(':content', $content);

        //  Execute the INSERT.
        //  execute() will check for possible SQL injection and remove if necessary
        if($statement->execute()) {
            echo "Thanks for adding your two cents!";
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
    <title>SneakerWorld</title>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
    <h1><a href="index.php">👟 SneakerWorld 👟</a></h1>

    <section class="posts">
        <?php while($row = $statement->fetch()) :?>
            <h3><a href="fullpage.php?id=<?= $row['page_id'] ?>"><?= htmlspecialchars($row['title']) ?></a></h3>
            <h4><?= htmlspecialchars($row['creation_date']) ?></h4>
            <h4><a href="edit.php?page_id=<?=$row['page_id'] ?>">edit</a></h4>
            <h4><?= htmlspecialchars($row['content']) ?></h4>
            <?php if (!empty($image)): ?>
                <img src='uploads/<?= $image ?>' alt='Page Image'>
            <?php endif; ?>
            <?php endwhile ?>
    </section>
    <hr>

    <section class="comments" enctype='multipart/form-data'>
        <h4>Leave a Comment Below</h4>
        <form method="post" action="fullpage.php" enctype='multipart/form-data'>
            <textarea id="content" name="content" rows="5" cols="100"></textarea><br>
            <input type="submit" value="Post">
        </form>
<?php echo "hi"?>
        <?php while($row = $commentStatement->fetch() ) : ?>
            <h3><?= htmlspecialchars($row['content'])?></h3>
            <h4><?= htmlspecialchars($row['creation_date']) ?></h4>
            <h4><a href="edit.php?page_id=<?= $row['comment_id'] ?>">edit</a></h4>
        <?php endwhile ?>
        
    </section>
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
     <footer>
        <p>&copy; 2024 SneakerWorld. No rights reserved.</p>
    </footer>
</body>
</html>