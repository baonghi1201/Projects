<?php
require_once "pdo.php";
require_once "util.php";
session_start();

if(isset($_POST['profile_id'])){
    $sql='SELECT FROM profile WHERE profile_id= :id';
    $stmt=$pdo->prepare($sql);
    $stmt->execute(array(':id'=>$_POST['profile_id']));
    $data=$stmt->fetch(PDO::FETCH_ASSOC);
    $user_id=$data['id'];
    $profile_id=$_SESSION['profile_id'];
}

if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

//Handle incoming data
if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline'])
    && isset($_POST['summary'])) {

    $msg=validateProfile();
    if(is_string($msg)){
        $_SESSION['error'] = $msg;
        header('Location: add.php');
        return;
    }

//Validate position data
    $msg=validatePos();
    if(is_string($msg)){
        $_SESSION['error']=$msg;
        header('Location:add.php');
        return;
    }

    $msg=validateEdu();
    if(is_string($msg)){
        $_SESSION['error']=$msg;
        header('Location:add.php');
        return;
    }

    $sql = "INSERT INTO profile (user_id, first_name, last_name, email, headline, summary)
              VALUES (:uid,:fn, :ln, :em, :he, :su)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary']));
    $profile_id=$pdo->lastInsertId();

// Insert position entry
    $rank=1;
    for($n=1; $n<=10; $n++){
        if(!isset($_POST['year'.$n])) continue;
        if(!isset($_POST['desc'.$n])) continue;
        $year=$_POST['year'.$n];
        $desc=$_POST['desc'.$n];

        $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');

        $stmt->execute(array(
                ':pid' => $profile_id,
                ':rank' => $rank,
                ':year' => $year,
                ':desc' => $desc)
        );

        $rank++;
    }

// Insert education entry
    $rank=1;
    for($n=1; $n<=10; $n++){
        if(!isset($_POST['edu_year'.$n])) continue;
        if(!isset($_POST['edu_school'.$n])) continue;
        $edu_year=$_POST['edu_year'.$n];
        $edu_school=$_POST['edu_school'.$n];

        $stmt = $pdo->prepare('SELECT * FROM institution WHERE name=:xyz');

        $stmt->execute(array(':xyz'=>$edu_school));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $institution_id = $row['institution_id'];
        } else {
            $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES ( :name)');

            $stmt->execute(array(
                ':name' => $edu_school,
            ));
            $institution_id = $pdo->lastInsertId();
        }

        $stmt = $pdo->prepare('INSERT INTO Education
    (profile_id, institution_id, year, rank)
    VALUES ( :pid, :institution, :edu_year, :rank)');


        $stmt->execute(array(
                ':pid' => $profile_id,
                ':institution' => $institution_id,
                ':edu_year' => $edu_year,
                ':rank' => $rank)
        );

        $rank++;
    }
    
    $_SESSION['success']='Record Added';
    header('Location:index.php');
    return;

}
?>
<!DOCTYPE html>
<html>
<head>
<title>DANG BAO NGHI NGUYEN</title>
<?php require_once "head.php"; ?>
</head>
<div>
<h1 style="font-size: large"> Add New Entry </h1>
<form method="post">
    <p>
        <lable for=first_name">First Name:</lable>
        <input type="text" name="first_name">
    </p>
   <p>
        <lable for="last_name">Last Name:</lable>
        <input type="text" name="last_name">
   </p>
    <p>
        <lable for="email">Email:</lable>
        <input type="text" name="email">
    </p>
    <p>
        <lable for="headline">Headline:</lable>
        <input type="text" name="headline" size="30">
    </p>
    <p>
        <label for="summary">Summary:</label><br/>
        <textarea name="summary" rows="5" cols="30"></textarea>
    </p>
    <p>
        Education: <input type="submit" id="addEdu" value="+">
    <div id="edu_fields">
    </div>

    </p>
    <p>
        Position: <input type="submit" id="addPos" value="+"></p>
        <div id="position_fields">
        </div>
    <p>
        <input type="submit" value="Add">
<!--        <input type="submit" name="cancel" value="Cancel">-->
        <a href="index.php"><input type="button" value="Cancel"></a>
    </p>
</form>
<script>
    countEdu=0;
    countPos=0;
    $(document).ready(function() {
        window.console && console.log('Document ready called');
        $('#addPos').click(function(event) {
            event.preventDefault();
            if (countPos >= 10) {
                alert("Maximum position reached");
                return;
            }
            countPos++;
            window.console && console.log("Adding position " + countPos);
            $('#position_fields').append(
                '<div id="position' + countPos + '">\
                <p> Year:<input type="text" name="year' + countPos + '" value=""/>\
                <input type="button" value="-" \
                onclick="$(\'#position' + countPos + '\').remove(); return false;"></p>\
                <textarea name="desc' + countPos + '" row="8" cols="80"></textarea>\
            </div>');
        });

            $('#addEdu').click(function (event) {
                event.preventDefault();
                if (countEdu >= 10) {
                    alert("Maximum of nine education entries exceeded");
                    return;
                }
                countEdu++;
                window.console && console.log("Adding education " + countEdu);

                $('#edu_fields').append(
                    '<div id="edu' + countEdu + '"> \
            <p>Year: <input type="text" name="edu_year' + countEdu + '" value="" /> \
            <input type="button" value="-" onclick="$(\'#edu' + countEdu + '\').remove();return false;"><br>\
            <p>School: <input type="text" size="80" name="edu_school' + countEdu + '" class="school" value="" />\
            </p></div>'
                );

                $('.school').autocomplete({
                    source: "school.php"
                });
        });
    });
</script>
</body>
</html>
