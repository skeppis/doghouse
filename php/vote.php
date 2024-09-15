<?php
include("../connect.php");
include('functions.php');
session_start();

function getInteraction($threadid, $conn)
{
    try {
        $interaction = 0;
        $userid = getUserID($conn, $_SESSION['user']);

        $sql = 'SELECT type FROM dhInteractions WHERE user_id=? AND thread_id=?';
        $data = array($userid, $threadid);
        $stmt = $conn->prepare($sql);
        $stmt->execute($data);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res !== false) {
            $interaction = $res['type'];
        }

        return $interaction;
    } catch (PDOException $f) {
        echo $f->getMessage();
    }
}

/**
 * Uppdaterar tabellen för inläggsröster
 * 
 * @param int $threadid - Id för inlägg
 * @param int $value - Värdet på rösten, dvs positiv eller negativ
 */
function updateVoteTable($threadid, $value, $dbconn)
{
    try {
        $sql = "SELECT votes FROM dhVotes WHERE thread_id=?";
        $data = array($threadid);
        $stmt = $dbconn->prepare($sql);
        $stmt->execute($data);
        $score = $stmt->fetch();
        $score = $score[0];

        $sql = "UPDATE dhVotes SET votes=? WHERE thread_id=?";
        $data = array(($score + $value), $threadid);
        $stmt = $dbconn->prepare($sql);
        $stmt->execute($data);

        $newScore = $score + $value;
        echo $newScore;
    } catch (PDOException $f) {
        echo $f->getMessage();
    }
}

/**
 * Registerar användarens interaktion med inlägg
 * 
 * @param int $uid - Användar-id
 * @param int $id - Inläggets id
 * @param int $type - Positiv, negativ eller neutral röst
 */
function addInteraction($uid, $id, $type, $conn)
{
    try {
        $sql = "INSERT INTO dhInteractions VALUES (?,?,?)";
        $data = array($uid, $id, $type);
        $stmt = $conn->prepare($sql);
        $stmt->execute($data);
    } catch (PDOException $f) {
        echo $f->getMessage();
    }
}

// Röstar upp inlägg
if (isset($_GET['up'])) {
    $id = $_GET['up'];
    $uid = getUserID($dbconn, $_SESSION['user']);

    // Lägger till, tar bort eller ändrar användarens röst
    // beroende på om användaren har upp eller nedröstat det tidigare
    if (getInteraction($id, $dbconn) == 0) {
        updateVoteTable($id, 1, $dbconn);
        addInteraction($uid, $id, 1, $dbconn);
    } else if (getInteraction($id, $dbconn) == -1) {
        try {
            $sql = "DELETE FROM dhInteractions WHERE user_id=? AND thread_id=?";
            $data = array($uid, $id);
            $stmt = $dbconn->prepare($sql);
            $stmt->execute($data);
        } catch (PDOException $f) {
            echo $f->getMessage();
        }
        updateVoteTable($id, 1, $dbconn);
    } else {
        try {
            $sql = "DELETE FROM dhInteractions WHERE user_id=? AND thread_id=?";
            $data = array($uid, $id);
            $stmt = $dbconn->prepare($sql);
            $stmt->execute($data);
        } catch (PDOException $f) {
            echo $f->getMessage();
        }
        updateVoteTable($id, -1, $dbconn);
    }
}

// Röstar ned inlägg
if (isset($_GET['down'])) {
    $id = $_GET['down'];
    $uid = getUserID($dbconn, $_SESSION['user']);

    if (getInteraction($id, $dbconn) == 0) {
        updateVoteTable($id, -1, $dbconn);
        addInteraction($uid, $id, -1, $dbconn);
    } else if (getInteraction($id, $dbconn) == 1) {
        try {
            $sql = "DELETE FROM dhInteractions WHERE user_id=? AND thread_id=?";
            $data = array($uid, $id);
            $stmt = $dbconn->prepare($sql);
            $stmt->execute($data);
        } catch (PDOException $f) {
            echo $f->getMessage();
        }
        updateVoteTable($id, -1, $dbconn);
    } else {
        try {
            $sql = "DELETE FROM dhInteractions WHERE user_id=? AND thread_id=?";
            $data = array($uid, $id);
            $stmt = $dbconn->prepare($sql);
            $stmt->execute($data);
        } catch (PDOException $f) {
            echo $f->getMessage();
        }
        updateVoteTable($id, 1, $dbconn);
    }
}

// Röstar upp inlägg
if (isset($_GET['likeComment'])) {
    $id = $_GET['likeComment'];
    $uid = getUserID($dbconn, $_SESSION['user']);
    $likes = 0;

    try {
        $sql = "SELECT value FROM dhCommentLikes WHERE user_id=? AND comment_id=?";
        $data = array($uid, $id);
        $stmt = $dbconn->prepare($sql);
        $stmt->execute($data);
        $count = $stmt->rowCount();

        if ($count == 0) {
            $sql = "INSERT INTO dhCommentLikes VALUES(?,?,?)";
            $data = array($uid, $id, 1);
            $stmt = $dbconn->prepare($sql);
            $stmt->execute($data);
        } else {
            $sql = "DELETE FROM dhCommentLikes WHERE user_id=? AND comment_id=?";
            $data = array($uid, $id);
            $stmt = $dbconn->prepare($sql);
            $stmt->execute($data);
        }

        $sql = "SELECT COUNT(*) FROM dhCommentLikes WHERE comment_id=?";
        $data = array($id);
        $stmt = $dbconn->prepare($sql);
        $stmt->execute($data);
        $res = $stmt->fetch(PDO::FETCH_NUM);
        $likes = $res[0];

        $sql = "UPDATE dhComments SET likes=? WHERE this_id=?";
        $data = array($likes, $id);
        $stmt = $dbconn->prepare($sql);
        $stmt->execute($data);
    } catch (PDOException $f) {
        echo $f->getMessage();
    }
    echo $likes;
}
