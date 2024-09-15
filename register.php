<?php
session_start();
?>
<!DOCTYPE html>
<html lang="sv">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php
    include('php/nav.php');
    ?>
    <form action="" method="post" id="register">
        <?php
        include('connect.php');
        include('php/functions.php');

        if (isset($_GET['verify'])) {
            try {
                $sql = "SELECT reg FROM dhCodes WHERE code=?";
                $data = array($_GET['verify']);
                $stmt = $dbconn->prepare($sql);
                $stmt->execute($data);
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                $time = new Datetime($res['reg']);
                $time2 = new \DateTime('now', new DateTimeZone('Europe/Stockholm'));
                $diff = $time->diff($time2);

                if ($diff->format('%i') <= 15) {
                    $sql = "UPDATE dhUser SET code=0 WHERE code=?";
                    $data = array($_GET['verify']);
                    $stmt = $dbconn->prepare($sql);
                    $stmt->execute($data);

                    $sql = "DELETE FROM dhCodes WHERE code=?";
                    $data = array($_GET['verify']);
                    $stmt = $dbconn->prepare($sql);
                    $stmt->execute($data);

                    echo '<h2>Success!</h2>
                    <a href="register.php?action=1">Log in</a>';
                } else {
                    echo 'Unvalid verification link';
                }
            } catch (PDOException $c) {
                echo $c;
            }
        }

        if (isset($_POST['registerUsername'])) {
            $username = $_POST['registerUsername'];
            $mail = $_POST['registerMail'];
            $pass = password_hash($_POST['registerPassword'], PASSWORD_DEFAULT);
            $code = rand(100000, 250000000);

            function register($dbconn, $username, $mail, $pass, $code) {
                $sql = "INSERT INTO dhUser (name, email, password, code) VALUES (?,?,?,?)";
                $stmt = $dbconn->prepare($sql);
                $data = array($username, $mail, $pass, $code);
                $stmt->execute($data);
                $sql = "INSERT INTO dhCodes (user_id, code) VALUES (?,?)";
                $stmt = $dbconn->prepare($sql);
                $data = array(getUserID($dbconn, $username), $code);
                $stmt->execute($data);
                $link = 'https://labb.vgy.se/~allansm/wspprojekt/register.php?verify=' . $code;
                $message = 'Click here to complete DogHouse verification: '.$link;
                mail($mail, 'Verification', $message);
                echo 'A verification link has been sent to: ' . $mail;
            }

            try {
                $sql = "SELECT * FROM dhUser WHERE email=?";
                $data = array($mail);
                $stmt = $dbconn->prepare($sql);
                $stmt->execute($data);
                $res = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($stmt->rowCount() == 0) {
                    $sql = "SELECT * FROM dhUser WHERE name=?";
                    $stmt = $dbconn->prepare($sql);
                    $stmt->execute(array($username));

                    if ($stmt->rowCount() == 0) {
                        register($dbconn, $username, $mail, $pass, $code);
                    } else {
                        echo 'Username already exists.';
                    }
                } else if($res['code'] != 0){
                    $sql = "DELETE FROM dhUser WHERE email=?";
                    $stmt = $dbconn->prepare($sql);
                    $stmt->execute(array($mail));

                    $sql = "DELETE FROM dhCodes WHERE code=?";
                    $data = array($res['code']);
                    $stmt = $dbconn->prepare($sql);
                    $stmt->execute($data);

                    register($dbconn, $username, $mail, $pass, $code);
                } else {
                    echo 'Email has already been used before.';
                }
            } catch (PDOException $c) {
                echo $c;
            }
        }

        if (isset($_POST['LogInUsername'])) {
            $name = $_POST['LogInUsername'];
            $pass = $_POST['LogInPassword'];

            try {
                $sql = "SELECT password, code FROM dhUser WHERE name=?";
                $stmt = $dbconn->prepare($sql);
                $data = array($name);
                $stmt->execute($data);

                if ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if (password_verify($pass, $res['password'])) {
                        if($res['code'] == 0) {
                            $_SESSION['user'] = $name;
                            header('Location: index.php');
                        }
                        else {
                            echo 'Account has not been verified, check your mail!';
                        }
                    } else {
                        echo 'Wrong password. Try again';
                    }
                } else {
                    echo "User not found";
                }
            } catch (PDOException $c) {
                echo $c;
            }
        }

        if (isset($_POST['newPassName'])) {
            $name = $_POST['newPassName'];
            $mail = $_POST['newMail'];
            $newPass = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);

            try {
                $sql = "UPDATE dhUser SET code=?, password=? WHERE name=? AND email=?";
                $code = rand(100000, 250000000);
                $stmt = $dbconn->prepare($sql);
                $stmt->execute(array($code, $newPass, $name, $mail));

                $link = 'http://labb.vgy.se/~allansm/wspprojekt/register.php?verify=' . $code;
                mail($mail, 'Verification', $link);
                echo 'A verification link has been sent to: ' . $mail;
            } catch (PDOException $c) {
                echo $c;
            }
        }

        if (isset($_GET['action']) && $_GET['action'] == 1) {
            echo '
                <h1>Log in</h1>
                <input type="text" name="LogInUsername" id="" placeholder="Username" required><br>
                <input type="password" name="LogInPassword" id="" placeholder="Password" required><br>
                <input type="submit" value="Log in" class="submit"><br>
                <a href="register.php?action=4">Forgot password?</a>
            ';
        }

        if (isset($_GET['action']) && $_GET['action'] == 2) {
            echo '
            <h1>Register</h1>
            <input type="text" name="registerUsername" id="" placeholder="Username" required><br>
            <input type="email" name="registerMail" id="" placeholder="Mail" required><br>
            <input type="password" name="registerPassword" id="" placeholder="Password" required><br>
            <input type="submit" value="Register" class="submit">
            ';
        }

        if (isset($_GET['action']) && $_GET['action'] == 3) {
            session_destroy();
            header('Location: index.php');
        }

        if (isset($_GET['action']) && $_GET['action'] == 4) {
            echo '
            <h1>Change password</h1>
            <input type="text" name="newPassName" id="" placeholder="Username" required><br>
            <input type="email" name="newMail" id="" placeholder="Email" required><br>
            <input type="password" name="newPassword" id="" placeholder="New password" required><br>
            <input type="submit" value="Change password">';
        }
        ?>

    </form>
    <script>

    </script>
    <?php
    include('php/bg.php')
    ?>
</body>

</html>