<?php

session_start();

// db.php 파일 안에 있던 코드가 이 자리에 그대로 이식됨. 그래서 $connection 변수가 이 파일에서도 사용 가능하게 됨.
include 'db.php';

if (!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

// posts 테이블에서 모든 게시물을 created_at 컬럼을 기준으로 내림차순으로 가져오는 SQL 쿼리이다.
// 최신 게시물이 먼저 나오도록 정렬한다.
$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$result = mysqli_query($connection, $sql);

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>게시판</title>
    </head>
    <body>
        <h1>게시판</h1>
        
        <?php echo "안녕하세요, " . $_SESSION['username'] . "님!"; ?>

        <br><br>

        <a href="login.php">로그아웃</a>

        <br><br>
        <!-- <a> 태그는 하이퍼링크를 생성하는 데 사용된다.
            여기서는 '글쓰기'라는 텍스트를 클릭하면 create_post.php로 이동한다. -->
        <a href="create_post.php">글쓰기</a>
        <hr>  <!-- <hr> 태그는 수평선을 그리는 데 사용된다. 여기서는 게시판 제목과 게시물 목록을 구분하는 역할을 한다. -->
        
        <?php
            // mysqli_num_rows 함수는 쿼리 결과의 행 수를 반환한다. 여기서는 게시물이 있는지 확인하는 데 사용된다.
            if (mysqli_num_rows($result) > 0) {
                // mysqli_fetch_assoc 함수는 쿼리 결과에서 한 행을 연관 배열로 가져온다. 반복문을 통해 모든 게시물을 출력한다.
                while($row = mysqli_fetch_assoc($result)) {
                    // // 게시물의 제목, 내용, 작성자, 작성일을 출력한다. HTML 태그를 사용하여 게시물의 형식을 지정한다.
                    // //<h2> 태그는 제목을 나타내는 데 사용된다.
                    // // <p> 태그는 단락을 나타내는 데 사용된다.
                    // // <small> 태그는 작은 글씨로 텍스트를 표시하는 데 사용된다.
                    // // . 연산자는 문자열을 연결하는 데 사용된다.
                    // echo "<h2>" . $row['title'] . "</h2>";
                    // echo "<p>" . $row['content'] . "</p>";
                    // echo "<small>작성자: " . $row['author_id'] . " | 작성일: " . $row['created_at'] . "</small>";
                    // echo "<hr>";

                    // 게시물 제목을 클릭하면 해당 게시물의 상세 페이지로 이동하도록 링크를 설정한다.
                    // view_post.php?id=1과 같이 게시물의 id를 URL에 전달하여 상세 페이지에서 해당 게시물을 조회할 수 있도록
                    // <a> 태그의 href 속성에 PHP 코드를 사용하여 게시물의 id를 동적으로 삽입한다.
                    ?> <!-- 아래에서 HTML 구조로 게시물 제목을 출력하기 위해 PHP 태그를 닫는다. 참 기괴한 코드 구조야 -->

                    <a href="view_post.php?id=<?php echo $row['id']; ?>" >
                        <h2><?php echo $row['title']; ?></h2>
                    </a>
                    <br><br>

                <?php
                }
            } else {
                echo "게시물이 없습니다.";
            }
        ?>

    </body>