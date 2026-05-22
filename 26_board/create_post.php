<!-- 글 쓰기 -->
<?php

session_start();

if (!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>글쓰기</title>
    </head>
    <body>
        <a href="index.php">목록으로 돌아가기</a>
        <h1>글쓰기</h1>
        <!-- enctype 속성은 파일 업로드를 가능하게 해주는 속성. -->
        <!-- multipart/form-data는 파일 업로드를 처리하기 위한 인코딩 방식으로, 폼 데이터를 여러 부분으로 나누어 전송한다. -->
        <form action="create_post_process.php" enctype="multipart/form-data" method="post">
            <label for="title">제목:</label>
            <input type="text" id="title" name="title"><br><br>

            <label for="content">내용:</label>
            <textarea id="content" name="content"></textarea><br><br>

            <label for="file">파일:</label>
            <input type="file" id="file" name="upload_file"><br><br>

            <input type="submit" value="글쓰기">
        </form>
    </body>
</html>