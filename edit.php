<?php
    require_once "pdo.php";
    session_start();

    if(isset($_POST['update'])) {
        if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) &&
             isset($_POST['headline']) && isset($_POST['summary']) ) {

            if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1) {
                $_SESSION['error'] = 'Missing data';
                header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
                return;
            }

            if ( strpos($_POST['email'],'@') === false ) {
                $_SESSION['error'] = 'Bad data';
                header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
                return;
            }

            $sql = "UPDATE profile SET first_name = :first_name,
                    last_name = :last_name,
                    email = :email, headline = :headline, summary = :summary
                    WHERE profile_id = :profile_id AND user_id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':first_name' => $_POST['first_name'],
                ':last_name' => $_POST['last_name'],
                ':email' => $_POST['email'],
                ':headline' => $_POST['headline'],
                ':summary' => $_POST['summary'],
                ':profile_id' => $_REQUEST['profile_id'],
                ':user_id' => $_SESSION['user_id']));
            $stmt1 = $pdo->prepare('DELETE FROM position
                WHERE profile_id=:pid');
            $stmt1->execute(array( ':pid' => $_REQUEST['profile_id']));

            $rank = 0;
            for($i=0; $i<9; $i++) {
                if ( ! isset($_POST['year'.$i]) ) continue;
                if ( ! isset($_POST['desc'.$i]) ) continue;
                $year = $_POST['year'.$i];
                $desc = $_POST['desc'.$i];

                $stmt = $pdo->prepare('INSERT INTO Position
                    (profile_id, rank, year, description)
                VALUES ( :pid, :rank, :year, :desc)');
                $stmt->execute(array(
                    ':pid' => $_REQUEST['profile_id'],
                    ':rank' => $rank,
                    ':year' => $year,
                    ':desc' => $desc)
                );
                $rank++;
            }
            $_SESSION['success'] = 'Record updated!!!!';
            header( 'Location: index.php' ) ;
            return;
        }
    }

    if ( ! isset($_SESSION['user_id']) ) {
      $_SESSION['error'] = "Missing user_id";
      header('Location: index.php');
      return;
    }

    if (!isset($_REQUEST['profile_id']) || strlen($_REQUEST['profile_id']) <= 0) {
        $_SESSION['error'] = 'Bad value for user_id or profile_id';
        header( 'Location: index.php' ) ;
        return;
    }

    $stmtProfile = $pdo->prepare("SELECT * FROM profile where user_id = :user_id AND profile_id = :profile_id");
    $stmtCurrentProfilePos = $pdo->prepare("SELECT year, description FROM position where profile_id = :profile_id ORDER BY rank");
    $stmtProfile->execute(array(":user_id" => $_SESSION['user_id'], ":profile_id" => $_REQUEST['profile_id']));
    $stmtCurrentProfilePos->execute(array(":profile_id" => $_REQUEST['profile_id']));
    $rowProfile = $stmtProfile->fetch(PDO::FETCH_ASSOC);
    $positions = [];
    while ( $row = $stmtCurrentProfilePos->fetch(PDO::FETCH_ASSOC) ) {
        $positions[] = $row;
    };

    if ( isset($_SESSION['error']) ) {
        echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
        unset($_SESSION['error']);
    }

    $fn = htmlentities($rowProfile['first_name']);
    $ln = htmlentities($rowProfile['last_name']);
    $e = htmlentities($rowProfile['email']);
    $h = htmlentities($rowProfile['headline']);
    $s = htmlentities($rowProfile['summary']);
    /*$user_id = $row['user_id'];*/
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">

    <link rel="stylesheet" 
        href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
        integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" 
        crossorigin="anonymous">

    <script
      src="https://code.jquery.com/jquery-3.2.1.js"
      integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
      crossorigin="anonymous"></script>
    <title></title>
</head>
<body>
    <h1>Edit User</h1>
    <form method="post">
        <p>First Name:
        <input type="text" name="first_name" value="<?= $fn ?>"></p>
        <p>Last Name:
        <input type="text" name="last_name" value="<?= $ln ?>"></p>
        <p>Email:
        <input type="text" name="email" value="<?= $e ?>"></p>
        <p>Headline:
        <input type="text" name="headline" value="<?= $h ?>"></p>
        <p>Summary:
        <input type="text" name="summary" value="<?= $s ?>"></p>
        <div>Positions:
            <?php
                $i = 0;
                foreach ($positions as $ro) {
                    echo "<div id='position" . $i . "'><p>Year: <input type='text' name='year" . $i . "' value='" . $ro['year'] . "'><input type='button' value='-' onclick='$('#position" . $i . "').remove();return false;'></p><textarea name='desc" . $i . "' rows='8' cols='80'>". $ro['description'] . "</textarea></div>";
                    $i++;
                }
            ?>
                <button id="addPosition">+</button></div>
        <!-- <input type="hidden" name="user_id" value="<?= $user_id ?>"> -->
        <p><input type="submit" value="Save" name="update" />
        <a href="index.php">Cancel</a></p>
    </form>
<script>
        $(document).ready(() => {
            var i = <?=$i ?>;

            $('#addPosition').click((e) => {
                e.preventDefault();
                if(i < 9) {
                    $(`<div id="position${i}"><p>Year: <input type="text" name="year${i}" value=""><input type="button" value="-" onclick="$('#position${i}').remove();return false;"></p><textarea name="desc${i}" rows="8" cols="80"></textarea></div>`).insertBefore('#addPosition');
                    i++;
                } else {
                    alert("Max count of positions is reached");
                }
            })
        });
    </script>
</body>
</html>
