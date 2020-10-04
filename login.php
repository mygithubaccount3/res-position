<?php
    require_once 'pdo.php';

    session_start();

    if ( isset($_POST['cancel'] ) ) {
        header("Location: index.php");
        return;
    }

    $salt = 'XyZzy12*_';
    // $stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // Pw is php123

    if ( isset($_POST['email']) && isset($_POST['pass']) ) {
        if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
            $_SESSION['error'] = "Email and password are required";
            header("Location: login.php");
            return;
        } else {
            if(preg_match("/^(?=.{1,60}$)[a-zA-Z]+([.+]?[a-zA-Z0-9]+)*@[a-zA-Z]+\.[a-zA-Z]+$/", $_POST['email'])) {
                $check = hash('md5', $salt.$_POST['pass']);
                $stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = :em AND password = :pw');
                $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ( $row !== false ) {
                    error_log("Login success ".$_POST['email']);
                    $_SESSION['user_id'] = $row['user_id'];
                    header("Location: index.php");
                    return;
                } else {
                    error_log("Login fail ".$_POST['email']." $check");
                    $_SESSION['error'] = "Incorrect password";
                    header("Location: login.php");
                    return;
                }
            } else {
                $_SESSION['error'] = "Email must have an at-sign (@)";
                header("Location: login.php");
                return;
            }
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once "bootstrap.php"; ?>
        <title>Vladyslav Honcharov 5290f86d</title>
    </head>
    <body>
        <div class="container">
            <h1>Please Log In</h1>
            <?php
                if ( isset($_SESSION['error']) ) {
                    echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
                    unset($_SESSION['error']);
                }
            ?>
            <form method="POST">
                <label for="nam">User Name</label>
                <input type="text" name="email" id="nam"><br/>
                <label for="id_1723">Password</label>
                <input type="text" name="pass" id="id_1723"><br/>
                <input type="submit" value="Log In">
                <input type="submit" name="cancel" value="Cancel">
            </form>
            <!-- Hint: The password is the four character sound a cat
            makes (all lower case) followed by 123. -->
        </div>
    </body>
</html>
