<?php

session_start(); // 회원가입 후 자동 로그인 및 index.php로 이동을 위해 세션 시작

// db.php 파일 안에 있던 코드가 이 자리에 그대로 이식됨. 그래서 $connection 변수가 이 파일에서도 사용 가능하게 됨.
include 'db.php';

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";

if (mysqli_query($connection, $sql)) {
    //mysqli_insert_id는 방금 INSERT된 레코드의 AUTO_INCREMENT id를 반환한다
    $user_id = mysqli_insert_id($connection);

    $_SESSION['username'] = $username; // 회원가입 후 자동 로그인용 세션 설정
    $_SESSION['user_id'] = $user_id;

    // <script> 태그는 클라이언트 측에서 JavaScript 코드를 실행하는 데 사용된다.
    // javaScript는 웹 페이지에 동적인 기능을 추가하는 데 사용되는 프로그래밍 언어이다. 움직임과 기능 담당. 두뇌, 근육, 신경망
    // alert() 함수는 사용자에게 메시지를 표시하는 데 사용된다. 여기서는 '회원가입이 완료되었습니다.'라는 메시지를 표시한다.
    // ;는 JavaScript에서 명령어의 끝을 나타내는 구분자이다. 여러 명령어를 한 줄에 작성할 때 사용된다.
    // window.location.href는 현재 페이지를 다른 URL로 이동시키는 데 사용된다.
    // 여기서는 회원가입이 완료된 후 lobby.php로 이동하도록 설정되어 있다.
    echo "<script>alert('회원가입이 완료되었습니다.'); window.location.href='lobby.php';</script>"; // 회원가입 성공 시 lobby.php로 이동
} else {
    echo "오류가 발생했습니다.";
}

?>