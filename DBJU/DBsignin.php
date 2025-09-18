<?php
session_start(); // 세션 시작

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root"; 
    $password = "1277"; 
    $dbname = "ju";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("연결 실패: " . $conn->connect_error);
    }

    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, password_hash FROM Users WHERE nickname = ?");
    $stmt->bind_param("s", $username);

    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $passwordHash);
        $stmt->fetch();

        if (password_verify($password, $passwordHash)) {
            $_SESSION['user_id'] = $userId;
            echo "<script>alert('로그인 성공'); window.location.href = 'DBmain2.html';</script>";
        } else {
            echo "<script>alert('비밀번호가 일치하지 않습니다.'); window.location.href = 'DBsignin.php';</script>";
        }
    } else {
        echo "<script>alert('존재하지 않는 사용자입니다.'); window.location.href = 'DBsignin.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>로그인</title>
    <link rel="stylesheet" href="styles.css" />
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

      .login-container {
        width: 360px;
        background-color: white;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
      }

      .login-container h1 {
        color: #1a1a2e;
        margin-bottom: 20px;
      }

      .login-container p {
        color: #666;
        margin-bottom: 20px;
      }

      .login-container form {
        display: flex;
        flex-direction: column;
        align-items: center;
      }

      .login-container input {
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
        width: calc(100% - 22px);
      }

      .login-container button {
        width: 222px;
        padding: 10px;
        background-color: #1a1a2e;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        margin: 0 auto;
      }

      .login-container button:hover {
        background-color: #1a1a2e;
      }

      .links {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 20px;
      }

      .links a {
        color: #1a1a2e;
        text-decoration: none;
        font-size: 14px;
      }

      .links a:hover {
        text-decoration: underline;
      }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>JU유를 JU다</h1>
        <p>로그인 후 이용하실 수 있습니다.</p>
        <form method="POST" action="">
            <input type="text" id="username" name="username" placeholder="이메일" required />
            <input type="password" id="password" name="password" placeholder="비밀번호" required />
            <button type="submit" id="login-btn">로그인</button>
        </form>
        <div class="links">
            <a href="DBFindId.php">이메일 찾기</a>
            <a href="DBFindPass.php">비밀번호 찾기</a>
            <a href="DBsignup.php">회원가입</a>
        </div>
    </div>
</body>
</html>
