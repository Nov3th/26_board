<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>로그인</title>
    </head>
    <body>
        <h1>로그인</h1>
        <!-- form 태그는 사용자 입력을 서버로 전송하는 데 사용된다.
         action 속성은 데이터를 처리할 URL을 지정한다. method 속성은 데이터를 전송하는 방법을 지정한다. -->
        <form action="login_process.php" method="post">
            <!-- label 태그는 사용자 인터페이스 요소에 대한 설명을 제공한다.
             for 속성은 연결된 입력 요소의 id를 참조한다. required 속성은 입력이 필수임을 나타낸다. -->
            <label for="username">아이디:</label>
            <!-- input 태그는 사용자로부터 데이터를 입력받는 데 사용된다. type 속성은 입력 유형을 지정한다. -->
            <!-- id 속성은 요소의 고유 식별자이며, name 속성은 서버로 전송될 때 데이터의 이름을 지정한다. -->
            <!-- id는 브라우저가 화면을 꾸미고 제어할 때 쓰는 이름표, name은 서버로 데이터를 전송할 때 사용하는 이름표 -->
            <input type="text" id="username" name="username" required><br><br>

            <!-- type="password"는 입력된 텍스트를 숨기는 역할을 한다. -->
            <label for="password">비밀번호:</label>
            <input type="password" id="password" name="password" required><br><br>

            <!-- type="submit"은 폼 데이터를 서버로 전송하는 버튼을 생성한다. -->
            <input type="submit" value="로그인">
        </form>
        <br> <!-- <br> 태그는 줄바꿈을 의미한다. -->
        <a href="join.php">회원가입</a> <!-- href 속성은 링크의 대상 URL을 지정한다. -->
    </body>
</html>