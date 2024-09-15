<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php
            echo 'DogHouse - ' . $_GET['board'];
            ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php
    include('php/nav.php');
    ?>
    <div class="container">
        <div class="box">
            <a href="create.php?board=<?php
                                        echo $_GET['board'];
                                        ?>">
                <h3>Create a post</h3>
            </a>
        </div>
        <div class="box">

            <?php
            include('connect.php');
            include('php/functions.php');

            try {
                $sql = "SELECT * FROM dhThreads INNER JOIN dhVotes ON dhVotes.thread_id = dhThreads.id WHERE dhThreads.board=? ORDER BY dhVotes.votes DESC";
                $data = array($_GET['board']);
                $stmt = $dbconn->prepare($sql);
                $stmt->execute($data);

                $output = '';
                while ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $output .= '<div class="post"><div>';

                    if (isset($_SESSION['user'])) {
                        $output .=  '
                            <a onclick="upvote(' . htmlentities($res['id']) . ')"><img src="img/up.png" class="arrow"></a>
                            <p class="voteDisplay" id="voteDisplay' . htmlentities($res['id']) . '">' . htmlentities($res['votes']) . '</p>
                            <a onclick="downvote(' . htmlentities($res['id']) . ')"><img src="img/down.png" class="arrow"></a></div>';
                    } else {
                        $output .=  '
                            <img src="img/up.png" class="arrow">
                            <p class="voteDisplay" id="voteDisplay' . htmlentities($res['id']) . '">' . htmlentities($res['votes']) . '</p>
                            <img src="img/down.png" class="arrow"></div>';
                    }

                    if (htmlentities($res['filename']) != 'none') {
                        $output .= '<div class="img-div">
                        <img src="filer/' . htmlentities($res['filename']) . '" class="image">
                        </div>';
                    }

                    $sql = "SELECT id FROM dhUser WHERE name=?";
                    $data = array($res['user']);
                    $stmt3 = $dbconn->prepare($sql);
                    $stmt3->execute($data);
                    $res3 = $stmt3->fetch(PDO::FETCH_ASSOC);

                    
                    $output .= '<div class="post-content"><h3>' . htmlentities($res['title']) . '</h3>';

                    if($res['user'] != 'anonymous') {
                        $output .= '<a href="profile.php?view=' . htmlentities($res3['id']) . '">' .
                        htmlentities($res['user']) . '</a>';
                    } else {
                        $output .= htmlentities($res['user']);
                    }

                    $output .= ' @ ' . htmlentities($res['time']).'<p>'.htmlentities($res['text']) . '</p>';

                    $sql = "SELECT * FROM dhComments INNER JOIN dhUser ON dhUser.id = dhComments.user_id WHERE dhComments.thread_id=? ORDER BY dhComments.likes DESC";
                    $data = array($res['id']);
                    $stmt2 = $dbconn->prepare($sql);
                    $stmt2->execute($data);
                    $numRows = 0;

                    while ($res2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                        if ($numRows == 0) $output .= '<hr>';
                        $output .= '<div class="comment-div"><span id="commentVoteDisplay' . htmlentities($res2['this_id']) . '">
                        '.htmlentities($res2['likes']).'</span>';

                        if (isset($_SESSION['user'])) {
                            $output .=  '<a onclick="likeComment(' . htmlentities($res2['this_id']) . ')"><img src="img/up.png" class="arrow"></a>';
                        } else {
                            $output .=  '<img src="img/up.png" class="arrow">';
                        }

                        $output .= '<span class="comment"><strong> <a href="profile.php?view=' . htmlentities($res2['id']) . '" class="animated-underline">' .
                            htmlentities($res2['name']) . '</a> </strong> ' . htmlentities($res2['text']) . '</span></div><br>';
                        $numRows++;
                    }

                    if (isset($_SESSION['user'])) $output .= '<a href="comment.php?thread=' . htmlentities($res['id']) . '" class="blue-button">Comment on post</a>';

                    $output .= '</div></div><hr>';
                }
                echo $output;
            } catch (PDOException $f) {
                echo $f->getMessage();
            }
            ?>
        </div>
    </div>
    <script>
        function upvote(id) {
            let xlmhttp = new XMLHttpRequest()

            xlmhttp.onreadystatechange = () => {
                if (xlmhttp.readyState == 4 && xlmhttp.status == 200) {
                    let elementId = 'voteDisplay' + id;
                    document.getElementById(elementId).innerText = xlmhttp.responseText;
                }
            }

            xlmhttp.open("GET", "php/vote.php?up=" + id, true)
            xlmhttp.send()
        }

        function downvote(id) {
            let xlmhttp = new XMLHttpRequest()

            xlmhttp.onreadystatechange = () => {
                if (xlmhttp.readyState == 4 && xlmhttp.status == 200) {
                    let elementId = 'voteDisplay' + id;
                    document.getElementById(elementId).innerText = xlmhttp.responseText;
                }
            }

            xlmhttp.open("GET", "php/vote.php?down=" + id, true)
            xlmhttp.send()

        }

        function likeComment(id) {
            let xlmhttp = new XMLHttpRequest()

            xlmhttp.onreadystatechange = () => {
                if (xlmhttp.readyState == 4 && xlmhttp.status == 200) {
                    let elementId = 'commentVoteDisplay' + id;
                    document.getElementById(elementId).innerText = xlmhttp.responseText;
                }
            }

            xlmhttp.open("GET", "php/vote.php?likeComment=" + id, true)
            xlmhttp.send()
        }
    </script>
    <?php
    include('php/bg.php')
    ?>
</body>

</html>