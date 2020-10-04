<?php
    require_once "pdo.php";

    session_start();

    /*if ( ! isset($_SESSION['name']) ) {
        die('Not logged in');
    }*/

    $profile = $pdo->query("SELECT * FROM profile WHERE profile_id=" . $_GET['profile_id']);
    $row = $profile->fetchAll(PDO::FETCH_ASSOC);
    $positions = $pdo->query("SELECT year, description FROM position WHERE profile_id=" . $_GET['profile_id']);
    $rows = $positions->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>
    <?php
        /*if ( isset($_SESSION['success']) ) {
            echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
            unset($_SESSION['success']);
        }*/
    ?>
    <h1>Profile Information</h1>
    <?php
        echo("<p>First name: " . htmlentities($row[0]['first_name']) . "</p>");
        echo("<p>Last name: " . htmlentities($row[0]['last_name']) . "</p>");
        echo("<p>Email: " . htmlentities($row[0]['email']) . "</p>");
        echo("<p>Headline:<br>" . htmlentities($row[0]['headline']) . "</p>");
        echo("<p>Summary:<br>" . htmlentities($row[0]['summary']) . "</p>");
        echo "<p>Positions:<br>";
        if (count($rows) > 0) {
            echo "<ul>";
            foreach ($rows as $row) {
                echo "<li>" . htmlentities($row['year']) . " - " . htmlentities($row['description']);
            }
            echo "</ul>";
        } else {
            echo "<p>No positions</p>";
        }
        
        ?>
        <a href="index.php">Done</a>
</body>
</html>