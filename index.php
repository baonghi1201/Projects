<?php
require_once "pdo.php";
require_once "util.php";
session_start();

$stmt=$pdo->query('SELECT * FROM profile');
$profiles=$stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>DANG BAO NGHI NGUYEN</title>
    <?php require_once "head.php"?>
</head>
<body>
<h1>Nghi Nguyen's Resume Registry</h1>
<table border="1" >
<?php

    if ( isset($_SESSION['success']) ) {
        echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
        unset($_SESSION['success']);
    }
    if(isset($_SESSION['name']) && isset($_SESSION['user_id'])) {
        $stmt = $pdo->query('SELECT first_name, last_name, headline,profile_id FROM profile');
        echo ('<a href="add.php" style="font-size: large">Add New Entry</a><br/>');
        echo ('<p></p><a href="logout.php" style="font-size: large">Log Out</a></p>');
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr><td>";
            echo('<a href="view.php?profile_id='.$row['profile_id'].'"</a>');
            echo(htmlentities($row['first_name'] . " " . $row['last_name']));
            echo("</td><td>");
            echo(htmlentities($row['headline']));
            echo("</td><td>");
            echo('<a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a>' . " ");
            echo('<a href="delete.php?profile_id=' . $row['profile_id'] . '">Delete</a>');
            echo("</td></tr>");
        }

    } else {
            $stmt = $pdo->query('SELECT first_name, last_name, headline, profile_id FROM profile');
            echo('<a href="login.php">Please log in</a>');
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr><td>";
                echo('<a href="view.php?profile_id='.$row['profile_id'].'"</a>');
                echo(htmlentities($row['first_name'] . " " . $row['last_name']));
                echo("</td><td>");
                echo(htmlentities($row['headline']));
                echo("</td></tr>");
            }
        }
?>
</table>
</body>
</html>


