<?php

session_start();

if (!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html head>
    <meta charset="UTF-8">
    <title>로비</title>
</head>
<body>
    <h1>로비</h1>
    <p>안녕하세요, <?php echo $_SESSION['username']; ?>님!</p>
    <p><a href="index.php">자유 게시판으로 이동</a></p>
    <p><a href="guestbook_index.php">방명록 게시판으로 이동</a></p>
    <p><a href="logout.php">로그아웃</a></p>
</body>
</html>