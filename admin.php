<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin page</title>
</head>

<body>
    <?php
    include('php/functions.php');

    if (isset($_SESSION['user']) == 'admin') {
        include('connect.php');

        if(isset($_GET['deleteuser'])) {
            $sql = "DELETE FROM dhUser WHERE id=?";
            $data = array($_GET['deleteuser']);
            $stmt = $dbconn->prepare($sql);
            $stmt->execute($data);
        }

        if(isset($_GET['deletethread'])) {
            try {
                $sql = "SELECT board FROM dhThreads WHERE id=?";
                $data = array($_GET['deletethread']);
                $stmt = $dbconn->prepare($sql);
                $stmt->execute($data);
                while($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $board = new Board($res['board']);
                    $board->setNewSize($dbconn);
                    $board->editSelf($dbconn);
                }
                
                $sql = "DELETE FROM dhThreads WHERE id=?";
                $data = array($_GET['deletethread']);
                $stmt = $dbconn->prepare($sql);
                $stmt->execute($data);

                $sql = "DELETE FROM dhVotes WHERE thread_id=?";
                $data = array($_GET['deletethread']);
                $stmt = $dbconn->prepare($sql);
                $stmt->execute($data);
                
                $sql = "DELETE FROM dhInteractions WHERE thread_id=?";
                $data = array($_GET['deletethread']);
                $stmt = $dbconn->prepare($sql);
                $stmt->execute($data);

                $sql = "DELETE FROM dhComments WHERE thread_id=?";
                $data = array($_GET['deletethread']);
                $stmt = $dbconn->prepare($sql);
                $stmt->execute($data);

            }catch (PDOException $f) {
                echo $f->getMessage();
            }
        }

        if(isset($_GET['deleteall'])) {
            try {
                $sql = "DROP TABLE dhThreads";
                $stmt = $dbconn->prepare($sql);
                $stmt->execute();
            
                $sql = "DROP TABLE dhBoards";
                $stmt = $dbconn->prepare($sql);
                $stmt->execute();
            
                $sql = "DROP TABLE dhVotes";
                $stmt = $dbconn->prepare($sql);
                $stmt->execute();

                $sql = "DROP TABLE dhUser";
                $stmt = $dbconn->prepare($sql);
                $stmt->execute();

                $sql = "DROP TABLE dhComments";
                $stmt = $dbconn->prepare($sql);
                $stmt->execute();

                $sql = "DROP TABLE dhInteractions";
                $stmt = $dbconn->prepare($sql);
                $stmt->execute();
                } catch (PDOException $f) {
                    echo $f->getMessage();
                }
        }
        $sql = "SELECT * FROM dhComments";
        $stmt = $dbconn->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        print_r($res);

        $sql = "SELECT * FROM dhCodes";
        $stmt = $dbconn->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        print_r($res);

        $sql = "SELECT * FROM dhUser";
        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        $output = '<table><tr><td><h1>Id</h1></td><td><h1>Name</h1></td></tr>';
        while ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $output .= '<tr><td>' . htmlentities($res['id']) . '</td><td>' . htmlentities($res['name'])
                . '</td><td><a href="admin.php?deleteuser=' . htmlentities($res['id']) . '">Delete user</a></td></tr>';
        }
        echo $output;

        $sql = "SELECT * FROM dhThreads";
        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        $output = '<table><tr><td><h1>Id</h1></td><td><h1>Title</h1></td></tr>';
        while ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $output .= '<tr><td>' . htmlentities($res['id']) . '</td><td>' . htmlentities($res['title'])
                . '</td><td><a href="admin.php?deletethread=' . htmlentities($res['id']) . '">Delete thread</a></td></tr>';
        }
        echo $output;
        echo '<br><br><a href="admin.php?deleteall=true">Delete everything</a>';

    } else {
        echo 'You dont have access to this page';
    }
    ?>
</body>

</html>