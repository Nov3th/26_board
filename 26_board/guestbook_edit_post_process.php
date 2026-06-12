<?php

session_start();

include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$post_id = $_POST['id'];
$title = $_POST['title'];
$content = $_POST['content'];
$user_id = $_SESSION['user_id'];
$file = $_FILES['upload_file'];

/*echo "<pre>";
print_r($file);
echo "</pre>";
exit();*/

$check_sql = "SELECT * FROM guestbook_posts WHERE id='$post_id'";
$check_result = mysqli_query($connection, $check_sql);
$post = mysqli_fetch_assoc($check_result);

if (!$post || $post['author_id'] != $_SESSION['user_id'])
{
    echo "<script>alert('수정 권한이 없습니다.'); history.back();</script>";
    exit();
}

$update_post_sql = "UPDATE guestbook_posts SET title='$title', content='$content' WHERE id='$post_id'";
$update_result = mysqli_query($connection, $update_post_sql);

if(!empty($file['tmp_name']) && $file['error'] == 0) {
    $old_file_sql = "SELECT * FROM guestbook_attachments WHERE post_id='$post_id'";
    $old_file_result = mysqli_query($connection, $old_file_sql);

    if ($old_file = mysqli_fetch_assoc($old_file_result)) {
        $old_path = $old_file['stored_path'];
        if (file_exists($old_path)) {
            unlink($old_path); // 기존 파일 하드 디스크에서 삭제
        }
        mysqli_query($connection, "DELETE FROM guestbook_attachments WHERE post_id='$post_id'"); // 기존 파일 정보 DB에서 삭제
    }
    
    $upload_dir = 'guestbook_uploads/';

    $original_name = $file['name'];
    $size = $file['size'];
    $stored_name = $post_id . '_' . $original_name;
    $stored_path = $upload_dir . $stored_name;

    // 파일 이동 실행
    // move_uploaded_file 함수는 업로드된 파일을 임시 위치에서 지정된 위치로 이동한다. 이동이 성공하면 true를 반환한다.
    if (move_uploaded_file($file['tmp_name'], $stored_path)) {
        // attachments 테이블에 새 파일 정보 인서트
        $file_sql = "INSERT INTO guestbook_attachments (post_id, original_name, stored_path, size_bytes) VALUES ('$post_id', '$original_name', '$stored_path', '$size')";
        mysqli_query($connection, $file_sql);
    }
}

header("Location: guestbook_view_post.php?id=" . $post_id);
exit();
?>