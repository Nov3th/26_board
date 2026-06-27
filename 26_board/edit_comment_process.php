<?php

session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$comment_id = (int)$_POST['comment_id'];
$post_id = (int)$_POST['post_id'];
$content = trim($_POST['content']);
$user_id = $_SESSION['user_id'];

if (empty($content)) {
    echo "댓글 내용을 입력해주세요.";
    exit();
}

// $check_sql = "SELECT * FROM comments WHERE id='$comment_id'"; //댓글 쓴 유저가 맞는지 확인
// $check_result = mysqli_query($connection, $check_sql);
// $comment = mysqli_fetch_assoc($check_result);
$stmt = mysqli_prepare(
    $connection,
    "SELECT * FROM comments WHERE id=?"
);

mysqli_stmt_bind_param($stmt, "i", $comment_id);
mysqli_stmt_execute($stmt);

$check_result = mysqli_stmt_get_result($stmt);
$comment = mysqli_fetch_assoc($check_result);

mysqli_stmt_close($stmt);


if(!$comment || $comment['author_id'] != $user_id) {
    echo "<script>alert('댓글을 수정할 권한이 없습니다.'); history.back();</script>";
    exit();
}

if ($comment['post_id'] != $post_id) {
    die("잘못된 요청입니다.");
}

// $update_sql = "UPDATE comments SET content='$content' WHERE id='$comment_id'";
// $result = mysqli_query($connection, $update_sql);
$stmt = mysqli_prepare(
    $connection,
    "UPDATE comments
     SET content=?
     WHERE id=?"
);

mysqli_stmt_bind_param(
    $stmt,
    "si",
    $content,
    $comment_id
);

$result = mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);


if($result){
    header("Location: view_post.php?id=" . $post_id);
    exit();
}else{
    // echo "Error: " . $update_sql . "<br>" . mysqli_error($connection);
    die("댓글 수정에 실패했습니다.");
}
?>