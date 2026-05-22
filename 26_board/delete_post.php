<?php

session_start();

include 'db.php';

if (!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$post_id = $_GET['id']; // URL에서 게시물 ID를 가져온다.
$check_sql = "SELECT * FROM posts WHERE id='$post_id'"; // 해당 ID의 게시물을 조회하는 SQL 쿼리
$check_result = mysqli_query($connection, $check_sql); //쿼리 실행
$post = mysqli_fetch_assoc($check_result); // 쿼리 결과 가져오기

if(!$post || $post['author_id'] != $_SESSION['user_id']) { // 게시물이 존재하지 않거나, 게시물의 작성자가 현재 로그인한 사용자와 다르면
    echo "게시물을 삭제할 권한이 없습니다."; // 권한이 없다는 메시지를 출력한다.
    exit();
}

$file_sql = "SELECT * FROM attachments WHERE post_id='$post_id'"; // 해당 게시물에 첨부된 파일을 조회하는 SQL 쿼리
$file_result = mysqli_query($connection, $file_sql); // 쿼리 실행

while($file = mysqli_fetch_assoc($file_result)) { // 첨부된 파일이 있는 경우, 각 파일에 대해 반복한다.
    $stored_path = $file['stored_path'];
    $file_path = $stored_path; // 실제 파일 경로

    if(file_exists($file_path)) { // 파일이 실제로 존재하는지 확인한다.
        unlink($file_path); // 파일이 존재하면 삭제한다. unlink 함수는 파일을 삭제하는 데 사용된다.
    }
}

//attachments 테이블에서 해당 게시물과 관련된 파일 정보를 삭제하는 SQL 쿼리
$delete_file_sql = "DELETE FROM attachments WHERE post_id='$post_id'";
mysqli_query($connection, $delete_file_sql); // 쿼리 실행

// 해당 게시물과 관련된 댓글 정보를 삭제하는 SQL 쿼리
$delete_comment_sql = "DELETE FROM comments WHERE post_id='$post_id'";
mysqli_query($connection, $delete_comment_sql); // 쿼리 실행

// 해당 게시물과 관련된 게시글 정보를 삭제하는 SQL 쿼리
$delete_post_sql = "DELETE FROM posts WHERE id='$post_id'";
if(mysqli_query($connection, $delete_post_sql)) { // 게시물 삭제 쿼리 실행
    // echo "<script>alert('게시물이 삭제되었습니다.');</script>"; // 삭제가 성공하면 알림 메시지를 출력.
    // header("Location: index.php"); // 삭제가 성공하면 게시물 목록 페이지로 이동한다.
    // exit();
    //위에 이거 에러난다고 함.
    echo "<script>
        alert('게시물이 삭제되었습니다.');
        location.href='index.php';
    </script>";
    exit();
} else {
    echo "게시물 삭제에 실패했습니다: " . mysqli_error($connection);
}

?>