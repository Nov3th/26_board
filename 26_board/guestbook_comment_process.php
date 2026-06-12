<?php

session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$post_id = $_POST['post_id']; // form에서 게시물 ID. comments 저장 + 돌아가기 에 사용됨.
$content = $_POST['content']; // 유저가 게시글에 쓰려는 댓글 내용
$author_id = $_SESSION['user_id']; // 지금 댓글 작성하는 작성자 누구인가

if (empty($content)) {
    echo "댓글 내용을 입력해주세요.";
    exit();
}

$sql = "INSERT INTO guestbook_comments (post_id, content, author_id) VALUES ('$post_id', '$content', '$author_id')";
$result = mysqli_query($connection, $sql);

if ($result) {
    header("Location: guestbook_view_post.php?id=$post_id");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($connection);
}

?>