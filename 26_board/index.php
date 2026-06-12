<?php

session_start();

// db.php 파일 안에 있던 코드가 이 자리에 그대로 이식됨. 그래서 $connection 변수가 이 파일에서도 사용 가능하게 됨.
include 'db.php';

if (!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

// 검색어를 GET 방식으로 받아온다. 검색어가 없으면 빈 문자열로 초기화한다.
$search = $_GET['search'] ?? ''; //게시글 검색
$user_search = $_GET['user_search'] ?? ''; //유저 검색

$user_result = null;

if ($user_search != '') {

    $user_search = mysqli_real_escape_string($connection, $user_search);

    $sql_user_search ="SELECT username
                        FROM users
                        WHERE username LIKE '%$user_search%'
                        ORDER BY username";
    $user_result = mysqli_query($connection, $sql_user_search);
}

// writer 가져오기
$writer = $_GET['writer'] ?? ''; //유저의 글 확인

$sort = $_GET['sort'] ?? 'new'; //최신순, 오래된 순
$order = "DESC"; //이렇게 쓴 이유 : 아래 쿼리에서 $order을 사용하는데 혹시 비어 있으면 안되니까
//그래서 if - else 안쓰고 이렇게 씀
if ($sort == "old") {
    $order = "ASC";
}


if ($writer != ''){

    $writer = mysqli_real_escape_string($connection, $writer);
    // posts.* : posts 테이블의 모든 컬럼
    $sql = " SELECT posts.*
    FROM posts
    JOIN users ON posts.author_id = users.id
    WHERE users.username = '$writer'
    ORDER BY posts.created_at $order
    ";

    $result = mysqli_query($connection, $sql);

}else if ($search != '') { // 검색어가 비어있지 않은 경우에만 검색을 수행한다.

    // mysqli_real_escape_string 함수는 SQL 인젝션 공격을 방지하기 위해 사용된다.
    // 이 함수는 검색어에서 특수 문자를 이스케이프 처리하여 안전하게 SQL 쿼리에 사용할 수 있도록 한다.
    $search = mysqli_real_escape_string($connection, $search);
    // LIKE 연산자는 SQL에서 문자열 패턴 매칭을 수행하는 데 사용된다.
    // '%$search%'는 검색어가 포함된 모든 게시물을 찾기 위한 패턴이다.
    // '%'는 와일드카드로, 검색어 앞뒤에 어떤 문자든 올 수 있음을 의미한다.
    $sql = "SELECT * FROM posts WHERE title LIKE '%$search%' ORDER BY created_at $order";
    // mysqli_num_rows 함수는 쿼리 결과의 행 수를 반환한다. 여기서는 검색 결과가 있는지 확인하는 데 사용된다.
    if (mysqli_num_rows(mysqli_query($connection, $sql)) == 0) { // 검색 결과가 없는 경우
        $message = '검색 결과가 없습니다.';
    } else {
        $result = mysqli_query($connection, $sql);
    }
}else { // 검색어가 비어있는 경우, 모든 게시물을 가져온다.

    // posts 테이블에서 모든 게시물을 created_at 컬럼을 기준으로 내림차순으로 가져오는 SQL 쿼리이다.
    // 최신 게시물이 먼저 나오도록 정렬한다.
    $sql = "SELECT * FROM posts ORDER BY created_at $order";
    $result = mysqli_query($connection, $sql);
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>자유 게시판</title>
    </head>
    <body>
        <h1>자유 게시판</h1>
        
        <?php echo "안녕하세요, " . $_SESSION['username'] . "님!"; ?>

        <br><br>

        <a href="logout.php">로그아웃</a>

        <br><br>
        <!-- <a> 태그는 하이퍼링크를 생성하는 데 사용된다.
            여기서는 '글쓰기'라는 텍스트를 클릭하면 create_post.php로 이동한다. -->
        <a href="create_post.php">글쓰기</a>
        <hr>  <!-- <hr> 태그는 수평선을 그리는 데 사용된다. 여기서는 게시판 제목과 게시물 목록을 구분하는 역할을 한다. -->
        <!-- 게시물 검색을 위한 입력 필드이다. 사용자가 검색어를 입력할 수 있도록 한다. -->
         <!-- <input> 태그는 사용자로부터 입력을 받을 수 있는 필드를 생성하는 데 사용된다.
              type="text"은 텍스트 입력 필드를 나타내며, id="searchInput"은 자바스크립트나 CSS에서 이 요소를 참조할 때 사용할 수 있는 고유한 식별자이다.
              placeholder="검색"은 입력 필드에 회색으로 표시되는 안내 텍스트로, 사용자가 무엇을 입력해야 하는지 알려준다. -->
        <form method="GET" action="index.php">
            <input type="text" name="search" id="searchInput" placeholder="게시글 검색">
            <button id="searchButton">검색</button>
        </form>
        <form method="GET" action="index.php">
            <input type="text" name="user_search" placeholder="유저 검색">
            <button type="submit">유저 검색</button>
        </form>
        <br>
        <a href="index.php?writer=<?php echo $writer; ?>&sort=new">
            최신순
        </a>
        <a href="index.php?writer=<?php echo $writer; ?>&sort=old">
            오래된순
        </a>
        <a href="index.php">전체 글 보기</a>
        <br><br>

        <?php
            //유저 검색 기능
            if ($user_result != null) {

                echo "<h3>유저 검색 결과</h3>";

                while ($user_row = mysqli_fetch_assoc($user_result)) {
            ?>
                    <a href="index.php?writer=<?php echo $user_row['username']; ?>">
                        <?php echo $user_row['username']; ?>
                    </a>
                    <br>
            <?php
                }
            }

            // 게시글 기능
            if ($message != '') { //검색 결과가 없으면 while문 자체가 실행되지 않아서 while문 위에다가 작성함.
                ?>
                <br><br>
                <?php
                echo $message;
            }

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
                    $id = $row['author_id']; // 게시물 작성자 이름을 표기하기 위함.
                    $sql_user = "SELECT username FROM users WHERE id='$id'"; // 작성자 이름을 가져오기 위한 SQL 쿼리
                    $result_user = mysqli_query($connection, $sql_user);
                    $user = mysqli_fetch_assoc($result_user); // 작성자 이름을 가져온다
                    ?> <!-- 아래에서 HTML 구조로 게시물 제목을 출력하기 위해 PHP 태그를 닫는다. 참 기괴한 코드 구조야 -->

                    <a href="view_post.php?id=<?php echo $row['id']; ?>" >
                        <h2><?php echo $row['title']; ?></h2>
                    </a>
                    <small>작성자: <?php echo $user['username']; ?> | 작성일: <?php echo $row['created_at']; ?></small>
                    <br><br>
                    
                <?php
                }
            } else {
                echo "게시물이 없습니다.";
            }
        ?>

    </body>