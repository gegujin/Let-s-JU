<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>회원탈퇴</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #1a1a2e;
            color: white;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logo {
            display: flex;
            align-items: center;
        }
        .logo img {
            cursor: pointer;
        }
        .title {
            flex-grow: 1;
            text-align: center;
            margin-right: 150px; /* 이미지의 너비 만큼 오른쪽 마진을 추가합니다. */
        }
        nav {
            background-color: white;
            padding: 10px;
            width: 200px;
            float: left;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
        }
        nav ul li {
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
        }
        nav ul li:hover {
            text-decoration: underline;
        }
        main {
            margin-left: 220px;
            padding: 20px;
        }
        .order-history {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
        }
        .order-history table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-history table,
        th,
        td {
            border: 1px solid #ddd;
        }
        .order-history th,
        .order-history td {
            padding: 15px;
            text-align: left;
        }
        .order-history th {
            background-color: #f4f4f4;
        }
        .button-container {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .button-container button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #1a1a2e;
            color: white;
            cursor: pointer;
        }
    </style>
    <script>
        function navigateTo(page) {
            window.location.href = page;
        }
    </script>
</head>
<body>
    <header>
        <div class="logo">
            <a href="DBmain2.html">
                <img src="JU유를 JU다.png" alt="로고" width="150" height="100" />
            </a>
        </div>
        <div class="title">
            <h1>회원탈퇴</h1>
        </div>
    </header>

    <nav>
        <ul>
            <li onclick="navigateTo('DBmypage.php')">스크랩 내역 조회</li>
            <li onclick="navigateTo('DBmypage2.php')">개인정보</li>
            <li onclick="navigateTo('DBmypage3.php')">회원탈퇴</li>
        </ul>
    </nav>

    <main>
        <div class="order-history">
            <h2>회원탈퇴</h2>
            <p>정말로 회원을 탈퇴하시겠습니까? 이 작업은 되돌릴 수 없습니다.</p>
            <form action="" method="post" id="withdrawForm">
                <input type="checkbox" id="confirm" name="confirm" />
                <label for="confirm">회원탈퇴에 동의합니다.</label><br />
                <button type="submit" id="withdrawButton">회원탈퇴</button>
            </form>
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["confirm"])) {
                // 데이터베이스 연결 설정
                $servername = "localhost";
                $username = "root";
                $password = "1277";
                $dbname = "ju";

                // 데이터베이스 연결 생성
                $conn = new mysqli($servername, $username, $password, $dbname);

                // 연결 확인
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // 탈퇴할 사용자 ID (예시로 설정, 실제로는 로그인 세션 등을 통해 얻어야 함)
                $user_id = 1; 

                $sql = "DELETE FROM Users WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);

                if ($stmt->execute() === TRUE) {
                    echo "<script>
                            alert('회원탈퇴 되었습니다.');
                            window.location.href = 'DBmain.html';
                          </script>";
                } else {
                    echo "<script>alert('Error: " . $stmt->error . "');</script>";
                }

                $stmt->close();
                $conn->close();
            }
            ?>
        </div>
    </main>

    <script>
        document
            .getElementById("withdrawButton")
            .addEventListener("click", function (event) {
                if (!document.getElementById("confirm").checked) {
                    event.preventDefault();
                    alert("회원탈퇴에 동의해야 합니다.");
                }
            });
    </script>
</body>
</html>
