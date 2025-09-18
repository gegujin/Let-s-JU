<!DOCTYPE html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>회원가입</title>
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

      .signup-container {
        width: 360px;
        background-color: white;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        text-align: center;
      }

      .signup-container h1 {
        color: #1a1a2e;
        margin-bottom: 20px;
      }

      .signup-container form {
        display: flex;
        flex-direction: column;
      }

      .signup-container input,
      .signup-container select {
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
      }

      .signup-container .hint {
        font-size: 12px;
        color: #666;
        margin-bottom: 15px;
        text-align: left;
      }

      .signup-container button {
        padding: 10px;
        background-color: #1a1a2e;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
      }

      .signup-container button:hover {
        background-color: #1a1a2e;
      }

      .radio-group {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 15px;
      }

      .radio-group label {
        margin-right: 10px;
        font-size: 14px;
      }

      .radio-group input {
        margin-right: 5px;
      }

      .birthdate-group {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
      }

      .birthdate-group input,
      .birthdate-group select {
        margin-right: 10px;
        box-sizing: border-box;
        padding: 10px;
      }

      .birthdate-group input[type="text"] {
        flex: 1;
        min-width: 0;
      }

      .birthdate-group input:last-child,
      .birthdate-group select:last-child {
        margin-right: 0;
      }

      .gender-group {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 15px;
      }

      .gender-group label {
        display: flex;
        align-items: center;
        margin-right: 20px;
      }

      .gender-group input {
        margin-right: 5px;
      }
    </style>
  </head>
  <body>
    <div class="signup-container">
      <h1>JU유를 JU다</h1>
      <form method="POST" action="">
        <input type="text" id="myname" name="myname" placeholder="닉네임" required />
        <input type="text" id="username" name="username" placeholder="이메일" required />
        <div class="hint">
          이메일 형식으로 입력해주세요. (ex _______@naver.com)
        </div>
        <input type="password" id="password" name="password" placeholder="비밀번호" required />
        <div class="hint">
          문자, 숫자, 기호를 조합하여 10~15자로 입력해주세요.
        </div>
        <input type="password" id="password-check" name="password-check" placeholder="비밀번호 재확인" required />
        <div class="birthdate-group">
          <input type="text" id="birth-year" name="birth-year" placeholder="년(4자)" required />
          <select id="birth-month" name="birth-month" required>
            <option value="">월</option>
          </select>
          <input type="text" id="birth-day" name="birth-day" placeholder="일" required />
        </div>
        <div class="gender-group">
          <label for="male"><input type="radio" id="male" name="gender" value="male" required />남성</label>
          <label for="female"><input type="radio" id="female" name="gender" value="female" required />여성</label>
        </div>
        <button type="submit">회원가입</button>
      </form>
    </div>
    <script>
      document.addEventListener("DOMContentLoaded", () => {
        const birthMonthSelect = document.getElementById("birth-month");
        for (let i = 1; i <= 12; i++) {
          const option = document.createElement("option");
          option.value = i;
          option.textContent = i;
          birthMonthSelect.appendChild(option);
        }
      });
    </script>
  </body>
</html>

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
    $nickname = $_POST["username"];
    $password = $_POST["password"];
    $passwordCheck = $_POST["password-check"];
    $birthYear = $_POST["birth-year"];
    $birthMonth = $_POST["birth-month"];
    $birthDay = $_POST["birth-day"];
    $gender = $_POST["gender"];

    // 비밀번호 일치 여부 확인
    if ($password !== $passwordCheck) {
        die("비밀번호가 일치하지 않습니다.");
    }

    // 비밀번호 해시화
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // 생년월일 형식 맞추기
    $birthdate = "$birthYear-$birthMonth-$birthDay";

    // SQL 문 준비 및 바인딩
    $stmt = $conn->prepare("INSERT INTO Users (myname, nickname, password_hash, gender, birthdate) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $nickname, $passwordHash, $gender, $birthdate);

    // 문 실행
    if ($stmt->execute()) {
        // 회원가입 성공 시 로그인 페이지로 리디렉션
        echo "<script>alert('회원가입이 완료되었습니다.'); window.location.href = 'DBsignin.php';</script>";
    } else {
        echo "오류: " . $stmt->error;
    }

    // 문과 연결 닫기
    $stmt->close();
    $conn->close();
}
?>
