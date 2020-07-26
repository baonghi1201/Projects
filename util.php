<?php

// Validate Profile
function validateProfile(){
    if (strlen($_POST['first_name']) == 0 || strlen($_POST['last_name'])==0 || strlen($_POST['headline']) ==0
        || strlen($_POST['summary']) == 0 || strlen($_POST['email']) == 0){
        return "All fields are required";
    }
    if ( strpos($_POST['email'],'@') === false ){
        return "Email address must contain @";
    }
    return true;
}


// Validate Position
function validatePos(){
    for($n=1; $n<=10; $n++){
        if(!isset($_POST['year'.$n])) continue;
        if(!isset($_POST['desc'.$n])) continue;
        $year=$_POST['year'.$n];
        $desc=$_POST['desc'.$n];
        if(strlen($year)==0 || strlen($desc)==0){
            return "All field are required";
        }
        if (! is_numeric($year)){
            return "Position must be numeric";
        }
    }
    return True;
}

// Validate Education
function validateEdu(){
    for ($n=1; $n<=10; $n++){
        if(!isset($_POST['edu_year'.$n])) continue;
        if(!isset($_POST['edu_school'.$n])) continue;

        $edu_year=$_POST['edu_year'.$n];
        $edu_school=$_POST['edu_school'.$n];
        if(strlen($edu_year)==0 || strlen($edu_school) == 0){
            return "All fields are required";
        }
        if(!is_numeric($edu_year)){
            return "Education year must be numeric";
        }
    }
    return true;
}

// Loading Profile
function loadPro($pdo,$profile_id){
    $stmt=$pdo->prepare('SELECT * FROM profile WHERE profile_id=:prof');
    $stmt->execute(array(':prof'=>$profile_id));
    $profile_id=$stmt->fetch();
    return $profile_id;
}

// Loading Position
function loadPos($pdo, $profile_id){
    $stmt=$pdo->prepare('SELECT * FROM position WHERE profile_id=:prof ORDER BY rank');
    $stmt->execute(array(":prof"=>$profile_id));
    $positions=array();
    while ($row=$stmt->fetch(PDO::FETCH_ASSOC)){
        $positions[]=$row;
    }
    return $positions;
}

// Loading Education
function loadEdu($pdo, $profile_id){
    $stmt=$pdo->prepare('SELECT year,name FROM Education join Institution on Education.institution_id = Institution.institution_id
    WHERE profile_id=:prof ORDER BY rank');
    $stmt->execute(array(':prof'=>$profile_id));
    $education=$stmt->fetchAll(PDO::FETCH_ASSOC);
    return $education;
}

// Insert Position
function insertPosition($pdo, $profile_id)
{
    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year' . $i])) continue;
        if (!isset($_POST['desc' . $i])) continue;


        $year = $_POST['year' . $i];
        $desc = $_POST['desc' . $i];

        $stmt = $pdo->prepare('INSERT INTO Position
    (profile_id, rank, year, description)
    VALUES ( :pid, :rank, :year, :desc)');

        $stmt->execute(array(
                ':pid' => $profile_id,
                ':rank' => $rank,
                ':year' => $year,
                ':desc' => $desc)
        );

        $rank++;
    }
}

// Insert Education
function insertEducation($pdo, $profile_id)
{
    $rank = 1;
    for ($n = 1; $n <= 9; $n++) {
        if (!isset($_POST['edu_year' . $n])) continue;
        if (!isset($_POST['edu_school' . $n])) continue;
        $year = $_POST['edu_year' . $n];
        $school = $_POST['edu_school' . $n];

        print_r($year.$school) ;

        $institution_id = false;

        $stmt = $pdo->prepare('SELECT institution_id FROM
    Institution WHERE name = :name;');
        $stmt->execute(array(':name' => $school));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row != false) $institution_id = $row['institution_id'];

        if ($institution_id === false) {
            $stmt = $pdo->prepare('INSERT INTO Institution
    (name) VALUES (:name)');
            $stmt->execute(array(':name' => $school));
            $institution_id = $pdo->lastInsertId();
        }

        $stmt = $pdo->prepare('INSERT INTO Education
    (profile_id, rank, year, institution_id)
    VALUES ( :pid, :rank, :year, :iid)');
        $stmt->execute(array(
                ':pid' => $profile_id,
                ':rank' => $rank,
                ':year' => $year,
                ':iid' => $institution_id)
        );

        $rank++;
    }
}

