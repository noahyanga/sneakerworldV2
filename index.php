<?php
session_start();

/*******w******** 
    
    Name: Noah Yanga
    Date: April 20, 2024
    Description: Final Project

****************/

require('connect.php');

//$resultsPerPage = 5;

//// Determine the current page number
//if(isset($_GET['page'])) {
    //$currentPage = $_GET['page'];
//} else {
    //$currentPage = 1;
//}

    // fetch brands
    $query_brands = "SELECT brand FROM sneakers";
    $statement_brands = $db->prepare($query_brands);
    $statement_brands->execute();
    $brands = $statement_brands->fetchAll(PDO::FETCH_COLUMN);


// search bar
if (isset($_POST['search'])) {
    $keyword = $_POST['keyword'];

    // Build the SQL query to search for pages
    $query = "SELECT * FROM pages WHERE title LIKE :keyword OR content LIKE :keyword";
    $statement = $db->prepare($query);
    $keyword = '%' . $keyword . '%'; // Add wildcards to search for partial matches
    $statement->bindParam(':keyword', $keyword);
    $statement->execute();

    // $countQuery = "SELECT COUNT(*) AS total FROM pages";
    // $countStatement = $db->prepare($countQuery);
    // $countStatement->execute();
    // $totalResults = $countStatement->fetch(PDO::FETCH_ASSOC)['total'];

    // // calculate total number of pages
    // $totalPages = $totalResults / $resultsPerPage;

    // // Ensure currentPage is within valid range
    // if ($currentPage < 1) {
    //     $currentPage = 1;
    // } elseif ($currentPage > $totalPages) {
    //     $currentPage = $totalPages;
    // }
} elseif(isset($_POST['brand'])){
     $selected_brand = urldecode($_POST['brand']);

        // Build the SQL query to filter posts by brand
        $query = "SELECT * FROM pages WHERE brand = :brand";
        $statement = $db->prepare($query);
        $statement->bindValue(':brand', $selected_brand, PDO::PARAM_STR);
        $quote = $statement->fetch();

} else  {
    $query = "SELECT brand FROM sneakers";
    $statement = $db->prepare($query);
    $statement->execute();
    $brands = $statement->fetchAll(PDO::FETCH_COLUMN);

    // $selectedBrand = $_POST['brand'];
    // $query = "SELECT * FROM pages WHERE brand = :brand";


    // fetch all pages
    $query = "SELECT * FROM pages";
    $statement = $db->prepare($query);
    $statement->execute();
}

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
        <p class="description">Where sneakerheads gather to discuss the hottest topics!</p>
        <nav>
            <ul>
                <?php if(isset($_SESSION['username'])) : ?>
                    <?php echo "Logged in as: {$_SESSION['username']}"; ?> 
                    <li><a href="logout.php">Logout</a></li>
                <?php else : ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>

                <li><a href="post.php">Create New Post</a></li>
                <li>
                    <form method="post" action="index.php" enctype='multipart/form-data'>
                       <input type="text" name="keyword" placeholder="Search...">
                       <input type="submit" name="search" value="Search">

                        <select name="brand" >
                            <option value="index.php">List of Brands</option>
                            <?php foreach ($brands as $brand) : ?>
                                <option value="index.php?brand=<?= urlencode($brand) ?>"><?php echo $brand; ?></option>
                            <?php endforeach; ?>
                        </select>
                   </form>
                </li>
            </ul>
        </nav>
        <main>
    <section class="center">
        <h2>Recent posts</h2>
        <ul>
            <!-- Fetch each table row in turn. Each $row is a table row hash. -->
            <?php while($row = $statement->fetch() ) : ?>
                <h3><a href="fullpage.php?id=<?= $row['page_id'] ?>"><?= htmlspecialchars($row['title']) ?></a></h3>
                <h4><?= htmlspecialchars($row['creation_date']) ?></h4>
                <h4><a href="edit.php?page_id=<?= $row['page_id'] ?>">edit</a></h4>
            <?php
            // truncate content to 200 characters
                $content = htmlspecialchars($row['content']);
                if (strlen($content) > 200) :
                    $truncatedContent = substr($content, 0, 200) . '...';
                ?>
                    <p><?= $truncatedContent ?> <a href="fullpage.php?id=<?= $row['page_id'] ?>">Read Full Post...</a></p>
                <?php else : ?>
                    <p><?= $content ?></p>
                <?php endif; ?>
                <hr>
            <?php endwhile ?>
        </ul>

    </section>
</main>
        
        <footer>
            <p>&copy; 2024 SneakerWorld. No rights reserved.</p>
        </footer>
    </body>
</html>