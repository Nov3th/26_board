<?php

session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$comment_id = $_POST['comment_id'];
$post_id = $_POST['post_id'];
$content = $_POST['content'];
$user_id = $_SESSION['user_id'];

if (empty($content)) {
    echo "댓글 내용을 입력해주세요.";
    exit();
}

$check_sql = "SELECT * FROM guestbook_comments WHERE id='$comment_id'"; //댓글 쓴 유저가 맞는지 확인
$check_result = mysqli_query($connection, $check_sql);
$comment = mysqli_fetch_assoc($check_result);

if(!$comment || $comment['author_id'] != $user_id) {
    echo "<script>alert('댓글을 수정할 권한이 없습니다.'); history.back();</script>";
    exit();
}

$update_sql = "UPDATE guestbook_comments SET content='$content' WHERE id='$comment_id'";
$result = mysqli_query($connection, $update_sql);

if($result){
    header("Location: guestbook_view_post.php?id=" . $post_id);
    exit();
}else{
    echo "Error: " . $update_sql . "<br>" . mysqli_error($connection);
}
?>