<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>회원가입</title>
    </head>
    <body>
        <h1>회원가입</h1>
        <form action="join_process.php" method="post">
            <label for="username">아이디:</label>
            <input type="text" id="username" name="username" required><br><br>

            <label for="password">비밀번호:</label>
            <input type="password" id="password" name="password" required><br><br>
            
            <input type="submit" value="회원가입">
        </form>
        <br>
        <a href="login.php">로그인으로 돌아가기</a>
    </body>