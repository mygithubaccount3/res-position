<?php
    require_once 'pdo.php';
    session_start();

    $isLogged = false;

    if(isset($_SESSION['user_id']) && strlen($_SESSION['user_id']) > 0) {
        $isLogged = true;
    }

    $rows = $pdo->query('SELECT * from profile');
    $profiles = $rows->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Vladyslav Honcharov 5290f86d</title>
    </head>
    <body>
        <h1>Vlad Honcharov's Resume Registry</h1>
        <?php
            if ( isset($_SESSION['error']) ) {
                echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
                unset($_SESSION['error']);
            }
            if ( isset($_SESSION['success']) ) {
                echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
                unset($_SESSION['success']);
            }
            if ( !$isLogged ) {
                echo "<a href='login.php'>Please log in</a>";
            } else {
                echo "<a href='logout.php'>Logout</a>";
            }
            echo('<table border="1"><tr><th>Name</th><th>Headline</th><th>Action</th></tr>'."\n"); //hide actions when logged out
            $stmt = $pdo->query("SELECT * FROM profile");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($rows) === 0) {
                echo "No Rows Found";
            } else {
                foreach ($rows as $row) {
                    echo "<tr><td>";
                    echo("<a href='view.php?profile_id=" . $row['profile_id'] . "'>" . htmlentities($row['first_name']) . ' ' . htmlentities($row['last_name']) . "</a>");
                    echo("</td><td>");
                    echo(htmlentities($row['headline']));
                    echo("</td><td>");
                    echo("<a href='edit.php?profile_id=" . $row['profile_id'] . "'>Edit</a> <a href='delete.php?profile_id=" . $row['profile_id'] . "'>Delete</a>");
                    echo("</td></tr>\n");
                }
            }
            echo "</table>";
            if ($isLogged) {
                echo "<a href='add.php'>Add New Entry</a>";
            }
        ?>
    </body>
</html>
