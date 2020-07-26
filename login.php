<?php
require_once "pdo.php";
session_start();

if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

if (isset($_POST['email']) && isset($_POST['pass'])){
    unset($_SESSION['email']);
    $salt='XyZzy12*_';
    $check = hash('md5', $salt.$_POST['pass']);

    $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND pass = :pw');

    $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ( $row !== false ) {

        $_SESSION['name'] = $row['name'];

        $_SESSION['user_id'] = $row['user_id'];

        if (isset($_SESSION['name']) && isset($_SESSION['user_id'])){
            $_SESSION['pass'] = $_POST['pass'];
            $_SESSION['success'] = 'Successfully Logged In';
            header('Location:index.php');
            return;
        }else {
            $_SESSION['error']= 'Incorrect Password';
            header('Location:login.php');
            return;
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title> DANG BAO NGHI NGUYEN </title>
</head>
<body>
<script>
    function doValidate(){
        console.log('Validating...');

        try {

            pw = document.getElementById('id_1201').value;

            console.log("Validating pw="+pw);

            if (pw == null || pw == "") {

                alert("Both fields must be filled out");

                return false;

            }

            return true;

        } catch(e) {

            return false;

        }

        return false;

    }
</script>
<h1 style="font-size: xx-large">Please Log In</h1>
<form method="post">
    <p>
        <label for="email">Email</label>
        <input type="text" name="email"><br/>
        <label for="password">Password</label>
        <input type="password" name="pass" id="id_1201">
    </p>
    <p>
        <input type="submit" onclick="return doValidate();" value="Log In">
        <a href="index.php"><input type="button" value="Cancel"></a>
    </p>
</form>
</body>
</html>