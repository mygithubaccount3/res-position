<?php
    require_once "pdo.php";

    session_start();

    if ( ! isset($_SESSION['user_id']) ) {
        die('Not logged in');
    }

    if ( isset($_POST['cancel'] ) ) {
        header("Location: index.php");
        return;
    }

    $stmt = $pdo->prepare('INSERT INTO Profile
        (user_id, first_name, last_name, email, headline, summary)
        VALUES ( :uid, :fn, :ln, :em, :he, :su)');
    $stmt1 = $pdo->prepare('INSERT INTO Position
            (profile_id, rank, year, description) 
        VALUES ( :pid, :rank, :year, :desc)');
    $rank = 0;
    /*print_r(ctype_alpha($_POST['first_name']) &&
            ctype_alpha($_POST['last_name']));*/

    if ( isset($_POST['add'] ) ) {# It is a good practice to put the 'All fields are required' check before the other checks (like is_numeric)

        if(isset($_POST['first_name']) &&
            isset($_POST['last_name']) &&
            isset($_POST['email']) &&
            isset($_POST['headline']) &&
            isset($_POST['summary']) &&
            ctype_alpha($_POST['first_name']) &&
            ctype_alpha($_POST['last_name']) &&
            preg_match("/^(?=.{1,60}$)[a-zA-Z]+([.+]?[a-zA-Z0-9]+)*@[a-zA-Z]+\.[a-zA-Z]+$/", $_POST['email'])) {
            if(strlen($_POST['first_name']) > 0) {
                $stmt->execute(array(
                    ':uid' => $_SESSION['user_id'],
                    ':fn' => $_POST['first_name'],
                    ':ln' => $_POST['last_name'],
                    ':em' => $_POST['email'],
                    ':he' => $_POST['headline'],
                    ':su' => $_POST['summary'])
                );
                $profile_id = $pdo->lastInsertId();
                for ($i=0; $i < 9; $i++) { 
                    if(isset($_POST['year' . $i]) && strlen($_POST['year' . $i]) > 0 && is_numeric($_POST['year' . $i]) &&
                       isset($_POST['desc' . $i]) && strlen($_POST['desc' . $i]) > 0 ) {
                        $stmt1->execute(array(
                            ':pid' => $profile_id,
                            ':rank' => $rank,
                            ':year' => $_POST['year' . $i],
                            ':desc' => $_POST['desc' . $i])
                        );
                        $rank++;
                    }
                }

                $_SESSION['success'] = "Record added";
                header("Location: index.php");
                return;
            } else {
                $_SESSION['error'] = "First name is required";
                header("Location: add.php");
                return;
            }

        } else {
            $_SESSION['error'] = "All values are required";
            header("Location: add.php");
            return;
        }
    }
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
    <?php
        if ( isset($_SESSION['error']) ) {
            echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
            unset($_SESSION['error']);
        }
    ?>
    <h1>Adding Profile</h1>
    <form method="post">
        <p>First Name:
        <input type="text" name="first_name" size="60"></p>
        <p>Last Name:
        <input type="text" name="last_name" size="60"></p>
        <p>Email:
        <input type="email" name="email" size="30"></p>
        <p>Headline:<br>
        <input type="text" name="headline" size="80"></p>
        <p>Summary:<br>
        <textarea name="summary" cols="80" rows="8"></textarea></p>
        <div>Positions:
        <button id="addPosition">+</button></div>
        <input type="submit" value="Add" name="add" />
        <input type="submit" value="Cancel" name="cancel"/>
    </form>
    <script>
        $(document).ready(() => {
            var i = 0;

            $('#addPosition').click((e) => {
                e.preventDefault();
                if(i < 9) {
                    $(`<div id="position${i}"><p>Year: <input type="text" name="year${i}" value=""><input type="button" value="-" onclick="$('#position${i}').remove();return false;"></p><textarea name="desc${i}" rows="8" cols="80"></textarea></div>`).insertBefore('#addPosition');
                    i++;
                } else {

                }
            })
        });
    </script>
</body>
</html>