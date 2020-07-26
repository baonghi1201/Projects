<?php
require_once "pdo.php";
require_once "util.php";
session_start();

// Guardian: Make sure that profile_id is present
if ( ! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}

if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) &&
    isset($_POST['headline']) && isset($_POST['summary']) && isset($_POST['profile_id'])){

    $msg=validateProfile();
    if (is_string($msg)){
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
        return;
    }

    $msg=validatePos();
    if (is_string($msg)) {
        $_SESSION['error'] =$msg;
        header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
        return;
    }

    $msg=validateEdu();
    if(is_string($msg)){
        $_SESSION['error']=$msg;
        header("Location:edit.php?profile_id=".$_REQUEST["profile_id"]);
    }

    $sql = "UPDATE profile SET first_name = :first, last_name=:last, email=:email, headline=:head,
            summary=:summary WHERE profile_id = :pid AND user_id=:uid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':first' => $_POST['first_name'],
        ':last' => $_POST['last_name'],
        ':email' =>$_POST['email'],
        ':head'=>$_POST['headline'],
        ':summary'=>$_POST['summary'],
        ':uid'=>$_SESSION['user_id'],
        ':pid' => $_REQUEST['profile_id']));

    $stmt=$pdo->prepare('DELETE FROM position WHERE profile_id=:pid');
    $stmt->execute(array(":pid"=>$_REQUEST['profile_id']));

    insertPosition($pdo, $_REQUEST['profile_id']);

    $stmt=$pdo->prepare('DELETE FROM education WHERE profile_id=:pid');
    $stmt->execute(array(":pid"=>$_REQUEST['profile_id']));

    insertEducation($pdo, $_REQUEST['profile_id']);

    $_SESSION['success'] = 'Record updated';
    header( 'Location: index.php' ) ;
    return;
}




$stmt = $pdo->prepare("SELECT first_name, last_name, email, headline, summary, profile_id  FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for user_id';
    header( 'Location: index.php' ) ;
    return;
}


// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

$schools=loadEdu($pdo, $_REQUEST['profile_id']);
$positions=loadPos($pdo, $_REQUEST['profile_id']);
$profile=loadPro($pdo, $_REQUEST['profile_id']);
$first = htmlentities($row['first_name']);
$last = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$head= htmlentities($row['headline']);
$sum = htmlentities($row['summary']);
$profile_id = $row['profile_id'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>DANG BAO NGHI NGUYEN</title>
    <?php require_once "head.php";?>
</head>
<body>
<p style="font-size: x-large">Edit User</p>
<form method="post">
    <p>First Name:
        <input type="text" name="first_name" value="<?= $first ?>"></p>
    <p>Last Name:
        <input type="text" name="last_name" value="<?= $last ?>"></p>
    <p>Email:
        <input type="text" name="email" value="<?= $em ?>"></p>
    <p>Headline:
        <input type="text" name="headline" value="<?= $head ?>"></p>
    <p>Summary:
        <input type="text" name="summary" value="<?= $sum ?>"></p>

    <?php

    $countEdu=0;
    echo('<p>Education: <input type="submit" id="addEdu" value="+">' . "\n");
    echo('<div id="edu_fields">');
    if (count($schools) > 0) {
        foreach ($schools as $school) {
            $countEdu++;
            echo('<div id="edu' . $countEdu . '">');
            echo
                '<p>Year: <input type="text" name="edu_year' . $countEdu . '" value="' . $school['year'] . '">
<input type="button" value="-" onclick="$(\'#edu' . $countEdu . '\').remove();return false;\"></p>
<p>School: <input type="text" size="80" name="edu_school' . $countEdu . '" class="school" 
value="' . htmlentities($school['name']) . '" />';
            echo "\n</div>\n";
        }
    }
    echo "</div></p>\n";

    $countPos = 0;
    echo ('<p>Position:<input type="submit" id="addPos" value="+">'."\n");
    echo ('<div id="position_fields">');
    if (count($positions)>0) {
        foreach ($positions as $position) {
            $countEdu++;
            echo ('<div id="position' . $countPos . '">');
            echo '<br>Year: <input type="text" name="year' . $countPos . '" value="' . htmlentities($position['year']) . '">
<input type="button" value="-" onclick="$(\'#position' . $countPos . '\').remove();return false;"><br>';
            echo '<textarea name="desc' . $countPos . '"rows="8" cols="80">' . "\n";
            echo htmlentities($position['description']) . "\n";
            echo "\n</textarea>\n</div>\n";

        }

    }
    ?>

    <p><input type="hidden" name="profile_id" value="<?= $profile_id ?>">
    <p><input type="submit" value="Save"/>
    <a href="index.php">Cancel</a></p>

<script>
    countEdu=0;
    countPos=0;
$(document).ready(function() {
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event) {
        event.preventDefault();
        if(countPos>=10){
            alert("Maximum position reached");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);

        $('#position_fields').append(
            '<div id="position' + countPos + '"> \
            <p>Year: <input type="text" name="year' + countPos + '" value="" /> \
            <input type="button" value="-" onclick="$(\'#position' + countPos + '\').remove();return false;"><br>\
            <textarea name="desc' + countPos + '" rows="8" cols="80"></textarea>\
            </div>');
    });

    $('#addEdu').click(function (event) {
        event.preventDefault();
        if(countEdu>=10){
            alert("Maximum of ten education entries reached");
            return;
        }
        countEdu++;
        window.console && console.log("Adding education" + countEdu);

        $('#edu_fields').append(
            '<div id="edu' + countEdu + '">\
            <p>Year: <input type="text" name="edu_year' + countEdu + '"value=""/>\
            <input type="button" value="-" onclick="$(\'#edu' + countEdu + '\').remove(); return false;"><br\>\
            <p>School:<input type="text" size="80" name="edu_school' + countEdu + '" class="school" value=""/>\
            </p></div>'
        );
        $('.school').autocomplete({
            source: "school.php"
        });

    });
});
</script>
</form>
</body>
</html>
