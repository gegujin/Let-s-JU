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
    $myname = isset($_POST["username"]) ? $_POST["username"] : '';
    $nickname = isset($_POST["email"]) ? $_POST["email"] : '';
    $birthdate = isset($_POST["birthdate"]) ? $_POST["birthdate"] : '';

    // 모든 필드가 채워져 있는지 확인
    if (!empty($myname) && !empty($nickname) && !empty($birthdate)) {
        // SQL 문 준비 및 바인딩
        $stmt = $conn->prepare("SELECT myname FROM Users WHERE myname = ? AND nickname = ? AND birthdate = ?");
        $stmt->bind_param("sss", $myname, $nickname, $birthdate);

        // 문 실행
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($stored_myname);

        // 결과 확인
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            if ($myname === $stored_myname) {
                // 새로운 임시 비밀번호 생성
                $tempPassword = bin2hex(random_bytes(4)); // 8자리 임시 비밀번호 생성
                $tempPasswordHash = password_hash($tempPassword, PASSWORD_BCRYPT);

                // 임시 비밀번호로 업데이트
                $update_stmt = $conn->prepare("UPDATE Users SET password_hash = ? WHERE myname = ?");
                $update_stmt->bind_param("ss", $tempPasswordHash, $myname);

                if ($update_stmt->execute()) {
                    echo "<script>alert('임시 비밀번호: $tempPassword'); window.location.href = 'DBsignin.php';</script>";
                } else {
                    echo "오류: " . $update_stmt->error;
                }

                $update_stmt->close();
            } else {
                echo "<script>alert('이메일 주소가 일치하지 않습니다.');</script>";
            }
        } else {
            echo "<script>alert('일치하는 사용자를 찾을 수 없습니다.');</script>";
        }

        // 문과 연결 닫기
        $stmt->close();
    } else {
        echo "<script>alert('모든 필드를 입력해주세요.');</script>";
    }
    
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>비밀번호 찾기</title>
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

      .input-container form {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
      }

      .input-container input {
        width: 300px;
        height: 40px;
        font-size: 16px;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
      }

      .input-container button {
        width: 320px;
        height: 40px;
        font-size: 16px;
        padding: 10px;
        margin-top: 20px;
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
        color: #0e71eb;
        text-decoration: none;
        margin: 0 10px;
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
            <h4>가입시 입력하신 닉네임, 이메일 및 생년월일 가입여부를 확인합니다.</h4>
            <div class="input-container">
                <form method="POST" action="">
                    <input type="text" id="name" name="username" placeholder="닉네임" />
                    <input type="text" id="email" name="email" placeholder="이메일" />
                    <input type="text" id="birthdate" name="birthdate" placeholder="생년월일 (YYYY-MM-DD)" />
                    <button type="submit">확인</button>
                </form>
            </div>
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
