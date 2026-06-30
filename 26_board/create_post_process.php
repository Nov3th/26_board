<?php

session_start();

include 'db.php';

// 에러 확인
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

// 로그인 여부 확인
if (!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

// 입력값
$title = trim($_POST['title']); // form에서 입력된 제목을 가져온다.
$content = trim($_POST['content']); // form에서 입력된 내용을 가져온다.
$author_id = $_SESSION['user_id']; // 세션에서 로그인한 사용자의 ID를 가져온다. 게시물 작성자의 ID로 사용된다.
$file = $_FILES['upload_file']; // form에서 업로드된 파일 정보를 가져온다. $_FILES는 파일 업로드를 처리하기 위한 슈퍼 글로벌 변수이다.

// =========================
// 1. 입력값 검사
// =========================

if (empty($title) || empty($content)) {
    die("제목과 내용을 모두 입력해주세요.");
}

if (mb_strlen($title) > 100) {
    die("제목은 100자 이하만 입력 가능합니다.");
}

// =========================
// 2. 게시글 저장 (Prepared Statement)
// =========================

$stmt = mysqli_prepare(
    $connection,
    "INSERT INTO posts (title, content, author_id)
     VALUES (?, ?, ?)"
);

mysqli_stmt_bind_param(
    $stmt,
    "ssi",
    $title,
    $content,
    $author_id
);

$result = mysqli_stmt_execute($stmt);

if (!$result) {
    die("게시글 저장에 실패했습니다.");
}

// 게시글 번호
$post_id = mysqli_insert_id($connection);

mysqli_stmt_close($stmt);

// =========================
// 3. 파일 업로드
// =========================

// if (!empty($file['tmp_name']) && $file['error'] == 0) {
    
//     $upload_dir = "uploads/";
//     if (!is_dir($upload_dir)) {
//         mkdir($upload_dir, 0755, true);
//     }

//     $original_name = basename($file['name']);
//     $tmp_name = $file['tmp_name'];
//     $size = $file['size'];

//     // 최대 5MB
//     $maxSize = 5 * 1024 * 1024;

//     if ($size > $maxSize) {
//         die("파일은 5MB 이하만 업로드할 수 있습니다.");
//     }

//     // 확장자 검사
//     $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

//     $allowedExt = [
//         'jpg',
//         'jpeg',
//         'png',
//         'gif',
//         'pdf'
//     ];

//     if (!in_array($ext, $allowedExt)) {
//         die("허용되지 않는 파일 확장자입니다.");
//     }

//     // MIME 타입 검사
//     $finfo = finfo_open(FILEINFO_MIME_TYPE);
//     $mime = finfo_file($finfo, $tmp_name);
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
//         if (getimagesize($tmp_name) === false) {
//             die("이미지 파일이 아닙니다.");
//         }
//     }

//     // 랜덤 파일명 생성
//     $stored_name = bin2hex(random_bytes(16)) . "." . $ext;
//     $stored_path = $upload_dir . $stored_name;

//     if (move_uploaded_file($tmp_name, $stored_path)) {

//         $stmt = mysqli_prepare(
//             $connection,
//             "INSERT INTO attachments
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

//         if (!mysqli_stmt_execute($stmt)) {
//             die("첨부파일 정보를 저장하지 못했습니다.");
//         }
//         mysqli_stmt_close($stmt);
//     }else{
//         die("파일 업로드에 실패했습니다.");
//     }
// }

$upload_dir = "uploads/";

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

foreach ($file['name'] as $i => $original_name) {

    // 파일을 선택하지 않은 칸은 건너뛴다.
    if ($file['error'][$i] == UPLOAD_ERR_NO_FILE) {
        continue;
    }

    // 업로드 중 오류 발생
    if ($file['error'][$i] != UPLOAD_ERR_OK) {
        die("파일 업로드 중 오류가 발생했습니다.");
    }

    $original_name = basename($original_name);
    $tmp_name = $file['tmp_name'][$i];
    $size = $file['size'][$i];

    // 최대 5MB
    $maxSize = 5 * 1024 * 1024;

    if ($size > $maxSize) {
        die("파일은 5MB 이하만 업로드할 수 있습니다.");
    }

    // 확장자 검사
    $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

    $allowedExt = [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'pdf'
    ];

    if (!in_array($ext, $allowedExt)) {
        die("허용되지 않는 파일 확장자입니다.");
    }

    // MIME 검사
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

    // 이미지 검사
    if ($ext !== "pdf") {

        if (getimagesize($tmp_name) === false) {
            die("이미지 파일이 아닙니다.");
        }
    }

    // 랜덤 파일명 생성
    $stored_name = bin2hex(random_bytes(16)) . "." . $ext;
    $stored_path = $upload_dir . $stored_name;

    // 실제 업로드
    if (!move_uploaded_file($tmp_name, $stored_path)) {
        die("파일 업로드에 실패했습니다.");
    }

    // DB 저장
    $stmt = mysqli_prepare(
        $connection,
        "INSERT INTO attachments
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

    if (!mysqli_stmt_execute($stmt)) {
        die("첨부파일 정보를 저장하지 못했습니다.");
    }

    mysqli_stmt_close($stmt);
}

// =========================
// 4. 종료
// =========================

mysqli_close($connection);

header("Location: index.php");
exit();


// if ($result) {
//     if(!empty($file['tmp_name']) && $file['error'] == 0) { // 파일 업로드에 성공했는지 확인한다.
//         // mysqli_insert_id 함수는 마지막으로 삽입된 행의 ID를 반환한다. 새로 생성된 게시물의 ID를 가져온다.
//         $post_id = mysqli_insert_id($connection);

//         $upload_dir = 'uploads/'; // 파일이 저장될 디렉토리 경로를 지정한다.
//         $original_name = $file['name']; // 업로드된 파일의 원래 이름
//         $tmp_name = $file['tmp_name']; // 업로드된 파일이 임시로 저장된 경로
//         $size = $file['size']; // 업로드된 파일의 크기

//         // 저장될 파일 이름을 게시물 ID와 원래 파일 이름을 조합하여 만든다.
//         // 이렇게 하면 파일 이름이 중복되는 것을 방지할 수 있다.
//         $stored_name = $post_id . '_' . $original_name;
//         $stored_path = $upload_dir . $stored_name; // 실제 저장 경로?

//         // move_uploaded_file 함수는 업로드된 파일을 임시 위치에서 지정된 위치로 이동한다.
//         if (move_uploaded_file($file['tmp_name'], $stored_path)){
//             $file_sql = "INSERT INTO attachments (post_id, original_name, stored_path, size_bytes) VALUES ('$post_id', '$original_name', '$stored_path', '$size')";
//             mysqli_query($connection, $file_sql);
//         }
//     }

//     // 게시물이 성공적으로 저장되면 index.php로 이동한다.
//     header("Location: index.php");
//     exit();
// } else {
//     // 게시물 저장에 실패하면 오류 메시지를 출력한다.
//     echo "Error: " . $sql . "<br>" . mysqli_error($connection);
// }

?>