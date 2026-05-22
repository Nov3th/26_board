<?php

// session_start() 함수는 세션을 시작하거나 기존 세션을 계속하는 데 사용된다.
// 세션은 사용자의 상태를 유지하는 데 사용되는 서버 측 저장소이다. 예를 들어, 로그인 상태를 유지하는 데 사용된다.
session_start();

// db.php 파일 안에 있던 코드가 이 자리에 그대로 이식됨. 그래서 $connection 변수가 이 파일에서도 사용 가능하게 됨.
include 'db.php';

// $_POST는 HTML 폼에서 전송된 데이터를 가져오는 데 사용되는 슈퍼 글로벌 변수이다.
// 여기서는 'username'과 'password'라는 이름으로 전송된 데이터를 각각 $username과 $password 변수에 저장한다.
$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
$result = mysqli_query($connection, $sql); // mysqli_query 함수는 데이터베이스에 SQL 쿼리를 실행하는 데 사용된다.

if (mysqli_num_rows($result) > 0) {
    // mysqli_num_rows 함수는 쿼리 결과의 행 수를 반환한다.
    $user = mysqli_fetch_assoc($result);

    // $_SESSION은 세션 변수를 저장하는 데 사용되는 슈퍼 글로벌 변수이다.
    // 여기서는 'username'이라는 이름으로 로그인한 사용자의 아이디를 저장한다.
    $_SESSION['username'] = $user['username'];

    // 로그인한 사용자의 ID를 세션에 저장한다.
    $_SESSION['user_id'] = $user['id'];

    //header 함수는 HTTP 헤더를 전송하는 데 사용함.
    //이 경우, "Location: index.php"는 클라이언트에게 index.php로 이동하도록 지시한다.
    // 따라서 로그인 성공 시 사용자는 자동으로 index.php로 리디렉션된다.
    header("Location: index.php"); // 로그인 성공 시 index.php로 강제 이동
    exit(); // 현재 스크립트 실행을 종료. header 함수로 리디렉션 후 스크립트가 계속 실행되는 것을 방지하기 위해 사용된다.
} else {
    echo "아이디 또는 비밀번호가 잘못되었습니다.";
}

?>