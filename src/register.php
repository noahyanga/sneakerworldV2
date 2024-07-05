<?php
session_start();

require('connect.php');

// Define variables and initialize with empty values
$username = $password = $confirmPassword = "";
$username_err = $password_err = $confirmPassword_err = "";

// Check if the form is submitted
if($_POST) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate username
    if(isset($_POST["username"]) && $_POST["username"] === ""){
        $username_err = "Please enter an email address.";
    } elseif(!filter_var($_POST["username"], FILTER_VALIDATE_EMAIL)){
        $username_err = "Please enter a valid email address.";
    }
    
    // Validate password
    if(isset($_POST["password"]) && $_POST["password"] === ""){
        $password_err = "Please enter a password.";     
    } elseif(isset($_POST["password"]) && strlen($_POST["password"]) < 6){
        $password_err = "Password must have at least 6 characters.";
    } else{
        $password = $_POST["password"];
    }
    
    // Validate confirm password
    if(isset($_POST["confirmPassword"]) && $_POST["confirmPassword"] === ""){
        $confirmPassword_err = "Please confirm password.";     
    } elseif(isset($_POST["password"]) && isset($_POST["confirmPassword"])
             && $_POST["password"] != $_POST["confirmPassword"]){

        $confirmPassword_err = "Password did not match.";
    } else{
        $confirmPassword = $_POST["confirmPassword"];
    }
    
    // If no errors, proceed with registration
    if(empty($username_err) && empty($password_err) && empty($confirmPassword_err)){
        $salt = bin2hex(random_bytes(16));

        $hashPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (user_name, password, salt) VALUES (:user_name, :password, :salt)";
        $statement = $db->prepare($query);

        $statement->bindValue(':user_name', $username);
        $statement->bindValue(':password', $hashPassword);
        $statement->bindValue(':salt', $salt);
        $statement->execute();

        header("Location: login.php"); // Redirect to login after successful registration
        exit();
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
    <title>Registration</title>
</head>
<body>
    <div class="container">
        <div class="text-center">
            <h1><a href="index.php">👟 SneakerWorld 👟</a></h1>
            <hr>
            <h1>Create New Account</h1>
        </div>

        <form method="post" action="" class="form-horizontal">
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">Email:</label>
                <div class="col-sm-10">
                    <input type="text" id="username" name="username" class="form-control" required>
                    <span class="text-danger"><?php echo $username_err; ?></span>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="col-sm-2 control-label">Password:</label>
                <div class="col-sm-10">
                    <input type="password" id="password" name="password" class="form-control" required>
                    <span class="text-danger"><?php echo $password_err; ?></span>
                </div>
            </div>

            <div class="form-group">
                <label for="confirmPassword" class="col-sm-2 control-label">Confirm Password:</label>
                <div class="col-sm-10">
                    <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required>
                    <span class="text-danger"><?php echo $confirmPassword_err; ?></span>
                </div>
            </div>

            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary">Register</button>
            </div>
        </form>

        <div class="text-center">
            <a href="login.php" class="btn btn-default">Back to Login</a>
        </div>
    </div>
</body>
</html>
