<?php
require_once "pdo.php";
require_once "util.php";
session_start();
?>
<html>
<head><title>DANG BAO NGHI NGUYEN</title></head>
<?php require_once "head.php"?>
<div>
<?php
    $stmt = $pdo->prepare("SELECT first_name, last_name, email, headline, summary, profile_id
                            FROM profile where profile_id = :xyz");
    $stmt->execute(array(":xyz" => $_GET['profile_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);


    $stmt=$pdo->prepare("SELECT year, description FROM position WHERE profile_id=:abc");
    $stmt->execute(array(":abc"=>$_GET['profile_id']));
    $pos=$stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt=$pdo->prepare('SELECT * FROM education JOIN institution on Education.institution_id= Institution.institution_id
                WHERE profile_id=:pid');
    $stmt->execute(array(":pid"=>$_GET['profile_id']));
    $edu=$stmt->fetchALL(PDO::FETCH_ASSOC);

?>

    <p style="font-size: xx-large">Profile Details<br/></p>
    <p style="font-size: large">First Name: <?=htmlentities($row['first_name'])?></p>
    <p style="font-size: large">Last Name: <?=htmlentities($row['last_name']) ?></p>
    <p style="font-size: large">Email: <?=htmlentities($row['email']) ?></p>
    <p style="font-size: large">Headline: <?=htmlentities($row['headline']) ?></p>
    <p style="font-size: large">Summary: <?=htmlentities($row['summary']) ?></p>
    <p style="font-size: large">Education:
    <div id="edu_fields">
        <ul>
        <?php
        foreach($edu as $ed){
            echo '<li>'.$ed['year'].":".$ed['name'].'</li>';
        }
        ?>
        </ul>
    </div>
    </p>
    <p style="font-size: large">Positions:
    <div id="position_fields">
    <ul>
        <?php
         foreach($pos as $po){
             echo ('<li>'.$po['year'].':'.' '.$po['description'].'</li>');
         }
        ?>
    </ul></p>
    </div>

</body>
    <a href="index.php"><input type="submit" value="Done"></a>
</html>
