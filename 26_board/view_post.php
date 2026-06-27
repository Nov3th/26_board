<!-- 게시글 보기, 수정, 삭제, 댓글 보이게 -->
<?php

session_start();

include 'db.php';

if (!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

// =========================
// 1. 게시글 조회
// =========================

$post_id = (int)$_GET['id']; // URL에서 게시물 ID를 가져온다.
// $sql = "SELECT * FROM posts WHERE id='$post_id'"; // 해당 ID의 게시물을 조회하는 SQL 쿼리
// $result = mysqli_query($connection, $sql);
// // 쿼리 결과에서 한 행을 연관 배열로 가져온다. 게시물의 제목, 내용, 작성자, 작성일 등을 $post 배열에 저장한다.
// // 자료형을 미리 정해두지 않는다.
// $post = mysqli_fetch_assoc($result);
// $post = [
//     'id' => 5,
//     'title' => 'PHP 공부 중입니다',
//     'content' => 'PHP는 서버 사이드 스크립트 언어입니다.',
//     'author_id' => 1,
//     'created_at' => '2026-05-21'
// ]; 이런식으로 저장되어 있다고 한다?
$stmt = mysqli_prepare(
    $connection,
    "SELECT * FROM posts WHERE id=?"
);

mysqli_stmt_bind_param($stmt, "i", $post_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$post = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$post) {
    die("존재하지 않는 게시글입니다.");
}
// =========================
// 1. 작성자 조회
// =========================

$id = $post['author_id']; // 게시물 작성자 이름을 표기하기 위함.
// $sql_user = "SELECT username FROM users WHERE id='$id'"; // 작성자 이름을 가져오기 위한 SQL 쿼리
// $result_user = mysqli_query($connection, $sql_user);
// $user = mysqli_fetch_assoc($result_user); // 작성자 이름을 가져온다
$stmt = mysqli_prepare(
    $connection,
    "SELECT username FROM users WHERE id=?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result_user = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result_user);

mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>게시물 상세보기</title>
    </head>
    <body>
        <a href="index.php">목록으로 돌아가기</a> <!-- 게시물 상세보기 페이지에서 게시물 목록 페이지로 돌아가는 링크를 제공한다. -->
        <h1><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8');?></h1> <!-- 게시물의 제목을 출력한다. -->
        <small> <!-- 게시물의 작성자와 작성일을 작은 글씨로 출력한다. -->
            작성자:
            <a href="index.php?writer=<?php echo htmlspecialchars($user['username']); ?>">
                <?php echo htmlspecialchars($user['username']); ?>
            </a>
            | 작성일: <?php echo $post['created_at']; ?>
        </small>
        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p> <!-- 게시물의 내용을 출력한다. -->
        <br><br>

        <?php
        // 해당 게시물에 첨부된 파일을 조회하는 SQL 쿼리
        // $file_sql = "SELECT * FROM attachments WHERE post_id='$post_id'";
        // $file_result = mysqli_query($connection, $file_sql); //쿼리 결과 가져오기
        $stmt = mysqli_prepare(
            $connection,
            "SELECT * FROM attachments WHERE post_id=?"
        );

        mysqli_stmt_bind_param($stmt, "i", $post_id);
        mysqli_stmt_execute($stmt);

        $file_result = mysqli_stmt_get_result($stmt);

        mysqli_stmt_close($stmt);

        //첨부파일이 있는가
        if(mysqli_num_rows($file_result) > 0) {
            ?>
            <h2>첨부파일</h2>
            <?php
            while($file = mysqli_fetch_assoc($file_result)) {
                $original_name = $file['original_name'];
                $stored_path = $file['stored_path'];
                // download 속성은 링크를 클릭했을 때 파일을 다운로드하도록 브라우저에 지시한다.
                // $original_name은 다운로드될 때의 파일 이름으로 사용된다.
                // echo "<a href='$stored_path' download>$original_name</a><br>";
                echo "<a href='" .
                    htmlspecialchars($stored_path) .
                    "' download>" .
                    htmlspecialchars($original_name) .
                    "</a><br>";
            }
        }
        ?>

        <?php
        //만약 내 글이라면 수정과 삭제가 가능해야 한다.
        if ($post['author_id'] == $_SESSION['user_id']) {
            ?>
            <!-- 게시물 수정 페이지로 이동하는 링크를 제공한다. URL에 게시물 ID를 전달하여 해당 게시물을 수정할 수 있도록 한다. -->
            <a href="edit_post.php?id=<?php echo $post['id']; ?>">수정</a>
            <!-- 게시물 삭제 페이지로 이동하는 링크를 제공한다. URL에 게시물 ID를 전달하여 해당 게시물을 삭제할 수 있도록 한다. -->
            <a href="delete_post.php?id=<?php echo $post['id']; ?>">삭제</a>
            <?php
        }
        ?>

        <h2>댓글 작성</h2>
        <!-- <form>은 입력창 -->
        <form action="comment_process.php" method="post">
            <!-- hidden타입은 사용자에게 보이지 않는 입력 필드로, 서버로 전송할 데이터를 담을 때 사용한다. -->
            <!-- 게시물 ID를 숨겨진 입력 필드로 전달하여 url에서 보이지 않게 한다. -->
            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>"> <!-- 게시물 id -->
            <!-- textarea 태그는 여러 줄의 텍스트 입력을 받을 때 사용된다.
             name 속성은 서버로 전송될 때 데이터의 이름을 지정한다. -->
            <textarea name="content"></textarea>
            <br><br>
            <input type="submit" value="댓글 작성">
        </form>

        <br><br>

        <h2>댓글 목록</h2>
        <?php
        // URL에 'edit_com_id'라는 값이 주어지면 변수에 저장하고, 없으면 null로 놔두기.
        $edit_com_id = isset($_GET['edit_com_id']) ? (int)$_GET['edit_com_id'] : null;

        // $sql = "SELECT * FROM comments WHERE post_id='$post_id' ORDER BY created_at DESC"; // 해당 게시물의 댓글을 조회하는 SQL 쿼리
        // $result = mysqli_query($connection, $sql);
        $stmt = mysqli_prepare(
            $connection,
            "SELECT * FROM comments
            WHERE post_id=?
            ORDER BY created_at DESC"
        );

        mysqli_stmt_bind_param($stmt, "i", $post_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        mysqli_stmt_close($stmt);
        

        if (mysqli_num_rows($result) > 0) {
            // 모든 댓글 출력
            while($comment = mysqli_fetch_assoc($result)) {
                ?>
                <small>작성자: <?php echo $comment['author_id']; ?> | 작성일: <?php echo $comment['created_at']; ?></small>

                <?php
                //지금 그리고 있는 댓글이 유저가 '수정' 버튼을 누른 댓글인가?
                if ($comment['id'] == $edit_com_id) {
                    // 1. 맞다면 평순한 텍스트 대신 입력창(<form>)을 그 자리에서 보여준다.
                    ?>
                    <form action="edit_comment_process.php" method="post">
                        <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>"> <!-- 댓글의 id -->
                        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>"> <!-- 게시글 작성 유저의 id. 돌아와야해서 -->
                        <textarea name="content"><?php echo htmlspecialchars($comment['content']); ?></textarea><br>
                        <input type="submit" value="완료">
                        <a href="view_post.php?id=<?php echo $post_id; ?>">취소</a>
                    </form>
                    <?php
                } else {
                    // 2. 아니면 평순한 텍스트로 보여준다.
                    ?>
                    <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>

                    <?php
                    //만약 내 댓글이라면 수정과 삭제가 가능해야 한다.
                    if ($comment['author_id'] == $_SESSION['user_id']) {
                    ?>
                    <a href="view_post.php?id=<?php echo $post_id; ?>&edit_com_id=<?php echo $comment['id']; ?>">수정</a>

                    <a href="delete_comment.php?id=<?php echo $comment['id']; ?>">삭제</a>
                    <br><br>
                    <?php
                    }
                }
                ?>
                <hr> <!-- 댓글과 댓글 사이에 수평선을 그어 구분. -->
                <?php
            }
        // } else {
        //     echo "댓글이 없습니다.";
        }
        ?>
    </body>
</html>