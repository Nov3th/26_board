<?php

session_start();

// 모든 세션 변수 제거
$_SESSION = array();

// 세션 삭제
session_destroy();

// 로그인 페이지로 이동
header("Location: login.php");
exit();

?>