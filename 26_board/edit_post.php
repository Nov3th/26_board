<?php

session_start();

include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$post_id = $_GET['id'];

$sql = "SELECT * FROM posts WHERE id='$post_id'";
$result = mysqli_query($connection, $sql);
//mysqli_fetch_assoc 함수는 쿼리 결과에서 한 행을 연관 배열로 가져오는 함수
$post = mysqli_fetch_assoc($result);

if($post['author_id'] != $_SESSION['user_id']) {
    echo "권한이 없습니다.";
    exit();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>게시물 수정</title>
    </head>
    <body>
        <h1>게시물 수정</h1>
        <form action="edit_post_process.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $post['id']; ?>">

            <label for="title">제목:</label><br>
            <input type="text" id="title" name="title" value="<?php echo $post['title']; ?>" required><br><br>

            <label for="content">내용:</label><br>
            <textarea id="content" name="content" rows="10" cols="50" required><?php echo $post['content']; ?></textarea><br><br>
            
            <label for="file">첨부파일:</label><br>
                <!-- 현재 파일 보여준다. -->
                <?php
                $file_sql = "SELECT * FROM attachments WHERE post_id='{$post['id']}'";
                $file_result = mysqli_query($connection, $file_sql);

                if(mysqli_num_rows($file_result) > 0) {
                    echo "<div><strong>기존 첨부파일:</strong><br>";
                    while($file = mysqli_fetch_assoc($file_result)) {
                        echo "- " . $file['original_name'] . " (서버에 안전하게 보관 중)<br>";
                    }
                    echo "</div><br>";
                }
                ?>
            <input type="file" id="file" name="upload_file"><br><br>

            <input type="submit" value="수정하기">
        </form>
    </body>
</html>