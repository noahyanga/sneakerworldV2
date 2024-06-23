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
        // generate salt
        $salt = bin2hex(random_bytes(16));

        // hash + salt password
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);

        // prepare query
        $query = "INSERT INTO users (user_name, password, salt) VALUES (:user_name, :password, :salt)";
        $statement = $db->prepare($query);

        // bind + execute
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
    <link rel="stylesheet" href="main.css">
    <title>Registration</title>
</head>
<body>

    <h1><a href="index.php">👟 SneakerWorld 👟</a></h1><hr>
    <h1>Create New Account</h1>
    <form method="post" action="">
        <div>
            <label for="username">Email:</label>
            <input type="text" id="username" name="username" required value ><span><?php echo $username_err; ?></span>
            <br><br>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required value><span><?php echo $password_err; ?></span>
            <br><br>
        </div>
        <div>
            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required value><span><?php echo $confirmPassword_err; ?></span>
        </div>
        <div>
            <input type="submit" value="Register"><br><br>
        </div>
    </form>
    <a href="login.php"><button>Back to Login</button></a>
</body>
</html>
