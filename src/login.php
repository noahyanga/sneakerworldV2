<?php
session_start();

require('connect.php');


// Check if the form is submitted
if($_POST) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $login_error = "";

    // get user details from database
    $query = "SELECT user_name, password FROM users WHERE user_name = :username LIMIT 1";
    $statement = $db->prepare($query);

    $statement->bindValue(':username', $username);
    $statement->execute();

    $user = $statement->fetch(PDO::FETCH_ASSOC);

    // admin login

            $hashPassword = $user['password'];


            if(password_verify($password, $hashPassword)){
                $_SESSION['username'] = $username;
                header("Location: index.php");
                exit();
                } else {
                    $login_error = "Invalid username or password";
                    echo $password;
                    echo $hashPassword;
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
    <link rel="stylesheet" href="main.css">
    <title>Login</title>
</head>
<body>
    <div class="container">
        <section class="center text-center">
            <h1><a href="index.php">👟 SneakerWorld 👟</a></h1>
            <hr>
            <h1>Login</h1>

            <?php if(isset($login_error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $login_error; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="" class="form-horizontal">
                <div class="form-group">
                    <label for="username" class="col-sm-2 control-label">Username:</label>
                    <div class="col-sm-10">
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password" class="col-sm-2 control-label">Password:</label>
                    <div class="col-sm-10">
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </div>
            </form>
            <a href="register.php" class="btn btn-success">Register New Account</a>
        </section>
    </div>
</body>
</html>
