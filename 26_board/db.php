<?php

// db 연결
$connection = mysqli_connect("localhost", "root", "qwer1234", "board26");

// 연결 확인
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}