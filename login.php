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
    <link rel="stylesheet" href="main.css">
    <title>Login</title>
</head>
<body>
    <section class="center">
        <h1><a href="index.php">👟 SneakerWorld 👟</a></h1><hr>
        <h1>Login</h1>
        

        <?php if(isset($login_error)): ?>
            <p><?php echo $login_error; ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>
            <input type="submit" value="Login"><br><br>
        </form>
        <a href="register.php"><button>Register New Account</button></a>
    </section>
</body>
</html>
