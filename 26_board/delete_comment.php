<?php

session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$comment_id = (int)$_GET['id']; // url의 id 값 가져오기
$user_id = $_SESSION['user_id'];

//view_post.php로 돌아가려면 post_id도 필요하다. 그거 얻는 과정이다. 겸사겸사 검사도 하고
// $check_sql = "SELECT * FROM comments WHERE id='$comment_id'";
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
    // 댓글이 존재하지 않거나, 댓글 작성자가 현재 로그인한 사용자가 아니라면 view_post.php로 돌아간다.
    echo "<script>alert('댓글을 삭제할 권한이 없습니다.'); history.back();</script>";
    exit();
}

$post_id = $comment['post_id']; // 댓글이 달린 게시물의 ID

// $delete_sql = "DELETE FROM comments WHERE id='$comment_id'";
// $final_result = mysqli_query($connection, $delete_sql);
$stmt = mysqli_prepare(
    $connection,
    "DELETE FROM comments WHERE id=?"
);

mysqli_stmt_bind_param($stmt, "i", $comment_id);

$final_result = mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);

if($final_result) {
    header("Location: view_post.php?id=" . $post_id);
    exit();
} else {
    // echo "Error: " . $delete_sql . "<br>" . mysqli_error($connection);
    die("댓글 삭제에 실패했습니다.");
}

?>