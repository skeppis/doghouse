<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make a comment</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include('php/nav.php'); ?>
    <div class="container">
        <?php
        include('connect.php');
        include('php/functions.php');

        // Lägger till kommentar
        if (isset($_POST['comment'])) {
            try {
                $id = getUserID($dbconn, $_SESSION['user']);
                $sql = "INSERT INTO dhComments (thread_id, text, user_id) VALUES (?,?,?)";
                $data = array($_GET['thread'], $_POST['comment'], $id);
                $stmt = $dbconn->prepare($sql);
                $stmt->execute($data);

                $str = 'Location: view.php?board=' . $_POST['board'];
                header($str);
            } catch (PDOException $f) {
                echo $f->getMessage();
            }
        }

        try {
            $sql = "SELECT board, title FROM dhThreads WHERE id=?";
            $stmt = $dbconn->prepare($sql);
            $data = array($_GET['thread']);
            $stmt->execute($data);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            $output = '<form action="" method="post">
            <h3>Making comment on thread: ' . htmlentities($res['title'])
                . '</h3>
            <h3>Logged in as: ' . $_SESSION['user'] . '</h3>
            <textarea required name="comment"></textarea><br>
            ';

            $output .= '<input type="hidden" name="board" value="' . htmlentities($res['board']) . '">
        <input type="submit" value="Submit"></form>';
            echo $output;
        } catch (PDOException $f) {
            echo $f->getMessage();
        }

        ?>

    </div>
</body>

</html>