<?php
// DB 연결 정보
$servername = "localhost";
$username = "root"; // 데이터베이스 사용자 이름으로 변경하세요
$password = "1277"; // 데이터베이스 비밀번호로 변경하세요
$dbname = "ju";

// 연결 생성
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die("연결 실패: " . $conn->connect_error);
}

// 폼이 제출되었는지 확인
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 폼 데이터 가져오기
    $name = $_POST["myname"];
    if(isset($_POST["birthdate"])) {
        $birthdate = $_POST["birthdate"];

        // SQL 문 준비 및 바인딩
        $stmt = $conn->prepare("SELECT nickname FROM Users WHERE myname = ? AND birthdate = ?");
        $stmt->bind_param("ss", $name, $birthdate);

        // 문 실행
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($nickname);

        // 결과 확인
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            echo "<script>alert('아이디: $nickname'); window.location.href = 'DBsignin.php';</script>";
        } else {
            echo "<script>alert('일치하는 사용자를 찾을 수 없습니다.');</script>";
        }

        // 문과 연결 닫기
        $stmt->close();
    } else {
        echo "<script>alert('생년월일을 입력해주세요.');</script>";
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>아이디 찾기</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        background-color: #f4f4f4;
      }

      header {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 20px;
        background-color: #1a1a2e;
        color: white;
      }

      header h1 {
        margin: 0;
        display: flex;
        align-items: center;
      }

      header h1 img {
        margin-right: 10px;
        cursor: pointer;
      }

      main {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
      }

      .container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
      }

      .input-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
      }

      .input-container input {
        width: 300px;
        height: 40px;
        font-size: 16px;
        padding: 10px;
        margin: 5px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
      }

      .input-container button {
        width: 320px;
        height: 40px;
        font-size: 16px;
        padding: 10px;
        margin-top: 10px;
        border: none;
        border-radius: 5px;
        background-color: #1a1a2e;
        color: white;
        cursor: pointer;
      }

      footer {
        width: 100%;
        padding: 10px;
        background-color: #1a1a2e;
        color: white;
        text-align: center;
      }

      .links {
        margin-top: 20px;
      }

      .links a {
        color: #1a1a2e;
        text-decoration: none;
        margin-right: 20px; /* 간격을 조절 */
      }

      .links a:last-child {
        margin-right: 0; /* 마지막 링크에는 마진 제거 */
      }
    </style>
</head>
<body>
    <header>
        <h1>
            <img src="JU유를 JU다.png" alt="로고" width="130" height="86" onclick="navigateToMain()" />
        </h1>
    </header>
    <main>
        <div class="container">
            <h2>JU유를 JU다</h2>
            <h4>가입시 입력하신 닉네임과 생년월일로 가입여부를 확인합니다.</h4>
            <form method="POST" action="">
                <div class="input-container">
                    <input type="text" id="name" name="myname" placeholder="닉네임" required />
                    <input type="text" id="birthdate" name="birthdate" placeholder="생년월일 (YYYY-MM-DD)" required />
                    <button type="submit">확인</button>
                </div>
            </form>
          
        </div>
    </main>
    <footer>
        <p>&copy; 2024 저렴한 주유소 추천</p>
    </footer>

    <script>
        function navigateToMain() {
            window.location.href = "DBmain.html";
        }
    </script>
</body>
</html>
