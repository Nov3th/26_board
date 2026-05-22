<?php

session_start();

include 'db.php';

if (!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

$title = $_POST['title']; // form에서 입력된 제목을 가져온다.
$content = $_POST['content']; // form에서 입력된 내용을 가져온다.
$author_id = $_SESSION['user_id']; // 세션에서 로그인한 사용자의 ID를 가져온다. 게시물 작성자의 ID로 사용된다.
$file = $_FILES['upload_file']; // form에서 업로드된 파일 정보를 가져온다. $_FILES는 파일 업로드를 처리하기 위한 슈퍼 글로벌 변수이다.

$sql = "INSERT INTO posts (title, content, author_id) VALUES ('$title', '$content', '$author_id')";
$result = mysqli_query($connection, $sql);

if ($result) {
    if(!empty($file['tmp_name']) || $file['error'] == 0) { // 파일 업로드에 성공했는지 확인한다.
        // mysqli_insert_id 함수는 마지막으로 삽입된 행의 ID를 반환한다. 새로 생성된 게시물의 ID를 가져온다.
        $post_id = mysqli_insert_id($connection);

        $upload_dir = __DIR__ . '/uploads/'; // 파일이 저장될 디렉토리 경로를 지정한다.
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $original_name = $file['name']; // 업로드된 파일의 원래 이름
        $tmp_name = $file['tmp_name']; // 업로드된 파일이 임시로 저장된 경로
        $size = $file['size']; // 업로드된 파일의 크기

        // 저장될 파일 이름을 게시물 ID와 원래 파일 이름을 조합하여 만든다.
        // 이렇게 하면 파일 이름이 중복되는 것을 방지할 수 있다.
        $stored_name = $post_id . '_' . $original_name;
        $stored_path = $upload_dir . $stored_name; // 실제 저장 경로?

        // move_uploaded_file 함수는 업로드된 파일을 임시 위치에서 지정된 위치로 이동한다.
        if (move_uploaded_file($file['tmp_name'], $stored_path)){
            $file_sql = "INSERT INTO attachments (post_id, original_name, stored_path, size_bytes) VALUES ('$post_id', '$original_name', '$stored_path', '$size')";
            mysqli_query($connection, $file_sql);
        }
    }

    // 게시물이 성공적으로 저장되면 index.php로 이동한다.
    header("Location: index.php");
    exit();
} else {
    // 게시물 저장에 실패하면 오류 메시지를 출력한다.
    echo "Error: " . $sql . "<br>" . mysqli_error($connection);
}

?>