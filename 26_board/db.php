<?php

// db 연결
$connection = mysqli_connect("localhost", "root", "qwer1234", "board26");

// 연결 확인
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// <?php

// $host = "localhost";
// $user = "board26_user";
// $password = "c96gef8bdd42113";
// $dbname = "board26";

// // db 연결
// $connection = mysqli_connect($host, $user, $password, $dbname);

// // 연결 확인
// if (!$connection) {
//     die("Connection failed: " . mysqli_connect_error());
// }