<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DogHouse - Create post</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php
    include('php/nav.php');
    include('connect.php');
    include('php/upload.php');

    // Laddar upp inlägg
    if(isset($_POST['text']) && isset($_POST['title'])) {
        $filename = upload_file('image');

        try {
            $sql = "SELECT size FROM dhBoards WHERE name=?";
            $stmt = $dbconn->prepare($sql);
            $data = array($_POST['board']);
            $stmt->execute($data);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            $size = htmlentities($res['size']);

            if(isset($_SESSION['user'])) {
                $sql = "INSERT INTO dhThreads (title, text, user, filename, board) VALUES (?,?,?,?,?)";
                $data = array($_POST['title'], $_POST['text'], $_SESSION['user'], $filename, $_POST['board']);
            }
            else {
                $sql = "INSERT INTO dhThreads (title, text, filename, board) VALUES (?,?,?,?)";
                $data = array($_POST['title'], $_POST['text'], $filename, $_POST['board']);
            }

            $stmt = $dbconn->prepare($sql);
            $stmt->execute($data);

            // Uppdaterar antalet inlägg för tabellen för rum
            $sql = "UPDATE dhBoards SET size=? WHERE name=?";
            $data = array(($size + 1), $_POST['board']);
            $stmt = $dbconn->prepare($sql);
            $stmt->execute($data);

            $sql = "INSERT INTO dhVotes (votes) VALUES (0)";
            $data = array();
            $dbconn->exec($sql);

            $str = 'Location: view.php?board='.$_POST['board'];
            header($str);
        } catch (PDOException $f) {
            echo $f->getMessage();
        }
    }
    ?>
    <div class="container">
        <form action="" method="post" enctype="multipart/form-data">
            <p>Creating post in: <?php
                echo $_GET['board'];
            ?></p>
            <label for="title">Title:</label><input type="text" name="title" id="" required><br>
            <label for="text">Text:</label>
            <textarea name="text"></textarea><br>
            <?php
                // Avgör om användaren kan ladda upp filer eller ej tillsammans med inlägget
                if(isset($_SESSION['user'])) {
                    echo '<input type="file" name="image" id="">';
                }
                else {
                    echo 'Log in to upload images';
                }
            ?>
            <input type="hidden" name="board" value="<?php
                echo $_GET['board'];
            ?>">
            <br>
            <input type="submit" value="Create post">
        </form>
    </div>
</body>

</html>