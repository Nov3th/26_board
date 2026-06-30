<?php

session_start();

include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$post_id = (int)$_POST['id'];
$title = trim($_POST['title']);
$content = trim($_POST['content']);
$user_id = $_SESSION['user_id'];
$file = $_FILES['upload_file'];

/*echo "<pre>";
print_r($file);
echo "</pre>";
exit();*/

// =========================
// 1. 입력값 검사
// =========================

if (empty($title) || empty($content)) {
    die("제목과 내용을 모두 입력해주세요.");
}

if (mb_strlen($title) > 100) {
    die("제목은 100자 이하만 입력 가능합니다.");
}

// $check_sql = "SELECT * FROM guestbook_posts WHERE id='$post_id'";
// $check_result = mysqli_query($connection, $check_sql);
// $post = mysqli_fetch_assoc($check_result);

// =========================
// 2. 게시글 조회 (Prepared Statement)
// =========================

// $check_sql = "SELECT * FROM posts WHERE id='$post_id'";
// $check_result = mysqli_query($connection, $check_sql);
// $post = mysqli_fetch_assoc($check_result);

$stmt = mysqli_prepare(
    $connection,
    "SELECT * FROM guestbook_posts WHERE id=?"
);

mysqli_stmt_bind_param($stmt, "i", $post_id);
mysqli_stmt_execute($stmt);

$check_result = mysqli_stmt_get_result($stmt);
$post = mysqli_fetch_assoc($check_result);

mysqli_stmt_close($stmt);

// =========================
// 3. 게시글 수정 (Prepared Statement)
// =========================

if (!$post || $post['author_id'] != $_SESSION['user_id'])
{
    echo "<script>alert('수정 권한이 없습니다.'); history.back();</script>";
    exit();
}

// $update_post_sql = "UPDATE guestbook_posts SET title='$title', content='$content' WHERE id='$post_id'";
// $update_result = mysqli_query($connection, $update_post_sql);

$stmt = mysqli_prepare(
    $connection,
    "UPDATE guestbook_posts
     SET title=?, content=?
     WHERE id=?"
);

mysqli_stmt_bind_param(
    $stmt,
    "ssi",
    $title,
    $content,
    $post_id
);

$update_result = mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);

// =========================
// 4. 파일 조회
// =========================

// if(!empty($file['tmp_name']) && $file['error'] == 0) {
//     // $old_file_sql = "SELECT * FROM guestbook_attachments WHERE post_id='$post_id'";
//     // $old_file_result = mysqli_query($connection, $old_file_sql);
//     $stmt = mysqli_prepare(
//         $connection,
//         "SELECT * FROM guestbook_attachments WHERE post_id=?"
//     );

//     mysqli_stmt_bind_param($stmt, "i", $post_id);
//     mysqli_stmt_execute($stmt);

//     $old_file_result = mysqli_stmt_get_result($stmt);
//     mysqli_stmt_close($stmt);

//     if ($old_file = mysqli_fetch_assoc($old_file_result)) {
//         $old_path = $old_file['stored_path'];
//         if (file_exists($old_path)) {
//             unlink($old_path); // 기존 파일 하드 디스크에서 삭제
//         }
//         // mysqli_query($connection, "DELETE FROM guestbook_attachments WHERE post_id='$post_id'"); // 기존 파일 정보 DB에서 삭제
//         $stmt = mysqli_prepare(
//             $connection,
//             "DELETE FROM guestbook_attachments WHERE post_id=?"
//         );

//         mysqli_stmt_bind_param($stmt, "i", $post_id);
//         mysqli_stmt_execute($stmt);

//         mysqli_stmt_close($stmt);
//     }
    
//     $upload_dir = 'guestbook_uploads/';
//     if (!is_dir($upload_dir)) {
//         mkdir($upload_dir, 0755, true);
//     }

//     $original_name = $file['name'];
//     $size = $file['size'];

//     $maxSize = 5 * 1024 * 1024;

//     if ($size > $maxSize) {
//         die("파일은 5MB 이하만 업로드할 수 있습니다.");
//     }

//     $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

//     // 확장자 검사
//     $allowedExt = ['jpg','jpeg','png','gif','pdf'];

//     if (!in_array($ext, $allowedExt)) {
//         die("허용되지 않는 파일 확장자입니다.");
//     }

//     // MIME 검사
//     $finfo = finfo_open(FILEINFO_MIME_TYPE);
//     $mime = finfo_file($finfo, $file['tmp_name']);
//     finfo_close($finfo);

//     $allowedMime = [
//         'image/jpeg',
//         'image/png',
//         'image/gif',
//         'application/pdf'
//     ];

//     if (!in_array($mime, $allowedMime)) {
//         die("허용되지 않는 파일 형식입니다.");
//     }

//     if ($ext !== "pdf") {
//         if (getimagesize($file['tmp_name']) === false) {
//             die("이미지 파일이 아닙니다.");
//         }
//     }

//     // $stored_name = $post_id . '_' . $original_name;
//     $stored_name = bin2hex(random_bytes(16)) . "." . $ext;
//     $stored_path = $upload_dir . $stored_name;

//     // 파일 이동 실행
//     // move_uploaded_file 함수는 업로드된 파일을 임시 위치에서 지정된 위치로 이동한다. 이동이 성공하면 true를 반환한다.
//     if (move_uploaded_file($file['tmp_name'], $stored_path)) {
//         // attachments 테이블에 새 파일 정보 인서트
//         // $file_sql = "INSERT INTO guestbook_attachments (post_id, original_name, stored_path, size_bytes) VALUES ('$post_id', '$original_name', '$stored_path', '$size')";
//         // mysqli_query($connection, $file_sql);
//         $stmt = mysqli_prepare(
//             $connection,
//             "INSERT INTO guestbook_attachments
//             (post_id, original_name, stored_path, size_bytes)
//             VALUES (?, ?, ?, ?)"
//         );

//         mysqli_stmt_bind_param(
//             $stmt,
//             "issi",
//             $post_id,
//             $original_name,
//             $stored_path,
//             $size
//         );

//         mysqli_stmt_execute($stmt);

//         mysqli_stmt_close($stmt);
//     }
// }

// =========================
// 4. 선택한 파일 삭제
// =========================

if (!empty($_POST['delete_files'])) {

    foreach ($_POST['delete_files'] as $file_id) {

        $stmt = mysqli_prepare(
            $connection,
            "SELECT stored_path
             FROM guestbook_attachments
             WHERE id=? AND post_id=?"
        );

        mysqli_stmt_bind_param($stmt, "ii", $file_id, $post_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        if ($row) {

            if (file_exists($row['stored_path'])) {
                unlink($row['stored_path']);
            }

            $stmt = mysqli_prepare(
                $connection,
                "DELETE FROM guestbook_attachments
                 WHERE id=?"
            );

            mysqli_stmt_bind_param($stmt, "i", $file_id);
            mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);
        }
    }
}

// =========================
// 5. 새 파일 업로드
// =========================

$upload_dir = 'uploads/';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

foreach ($file['name'] as $i => $original_name) {

    if ($file['error'][$i] != UPLOAD_ERR_OK) {
        continue;
    }

    $tmp_name = $file['tmp_name'][$i];
    $size = $file['size'][$i];

    $maxSize = 5 * 1024 * 1024;

    if ($size > $maxSize) {
        die("파일은 5MB 이하만 업로드할 수 있습니다.");
    }

    $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

    $allowedExt = ['jpg','jpeg','png','gif','pdf'];

    if (!in_array($ext, $allowedExt)) {
        die("허용되지 않는 파일 확장자입니다.");
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $tmp_name);
    finfo_close($finfo);

    $allowedMime = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'application/pdf'
    ];

    if (!in_array($mime, $allowedMime)) {
        die("허용되지 않는 파일 형식입니다.");
    }

    if ($ext !== "pdf") {

        if (getimagesize($tmp_name) === false) {
            die("이미지 파일이 아닙니다.");
        }
    }

    $stored_name = bin2hex(random_bytes(16)) . "." . $ext;
    $stored_path = $upload_dir . $stored_name;

    if (move_uploaded_file($tmp_name, $stored_path)) {

        $stmt = mysqli_prepare(
            $connection,
            "INSERT INTO guestbook_attachments
            (post_id, original_name, stored_path, size_bytes)
            VALUES (?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "issi",
            $post_id,
            $original_name,
            $stored_path,
            $size
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

header("Location: guestbook_view_post.php?id=" . $post_id);
exit();
?>