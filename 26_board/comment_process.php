<?php

session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$post_id = (int)$_POST['post_id']; // form에서 게시물 ID. comments 저장 + 돌아가기 에 사용됨.
$content = trim($_POST['content']); // 유저가 게시글에 쓰려는 댓글 내용
$author_id = $_SESSION['user_id']; // 지금 댓글 작성하는 작성자 누구인가

// ======================
// 게시금 존재 확인

$stmt = mysqli_prepare(
    $connection,
    "SELECT id FROM posts WHERE id=?"
);

mysqli_stmt_bind_param($stmt, "i", $post_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (!mysqli_fetch_assoc($result)) {
    die("존재하지 않는 게시글입니다.");
}

mysqli_stmt_close($stmt);

// ======================

if (empty($content)) {
    echo "댓글 내용을 입력해주세요.";
    exit();
}

// $sql = "INSERT INTO comments (post_id, content, author_id) VALUES ('$post_id', '$content', '$author_id')";
// $result = mysqli_query($connection, $sql);
$stmt = mysqli_prepare(
    $connection,
    "INSERT INTO comments
    (post_id, content, author_id)
    VALUES (?, ?, ?)"
);

mysqli_stmt_bind_param(
    $stmt,
    "isi",
    $post_id,
    $content,
    $author_id
);

$result = mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);

if ($result) {
    header("Location: view_post.php?id=$post_id");
    exit();
} else {
    die("댓글 작성에 실패했습니다.");
}

?>