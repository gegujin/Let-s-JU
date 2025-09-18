<?php
session_start(); // 세션 시작

// MySQL 데이터베이스 연결 설정
$host = 'localhost'; // 호스트 이름 또는 IP 주소    
$dbname = 'ju'; // 데이터베이스 이름
$username = 'root'; // 데이터베이스 사용자 이름
$password = '1277'; // 데이터베이스 암호

// MySQL 데이터베이스 연결
$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 북마크 삭제 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_bookmark'])) {
    $bookmark_id = $_POST['bookmark_id'];

    $deleteQuery = "DELETE FROM bookmarks WHERE id = :bookmark_id AND user_id = :user_id";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bindValue(':bookmark_id', $bookmark_id);
    $deleteStmt->bindValue(':user_id', $_SESSION['user_id']);
    $deleteStmt->execute();
}

// 검색어가 있는 경우 필터링
$search = isset($_POST['search']) ? $_POST['search'] : '';

// 사용자 ID 가져오기
$user_id = $_SESSION['user_id'];

// 쿼리 준비
if ($search) {
    $query = "SELECT * FROM bookmarks WHERE user_id = :user_id AND (station_name LIKE :search OR address LIKE :search)";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':user_id', $user_id);
    $stmt->bindValue(':search', '%' . $search . '%');
} else {
    $query = "SELECT * FROM bookmarks WHERE user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':user_id', $user_id);
}

// 쿼리 실행
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>스크랩 내역 조회</title>
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
        /* 주소 셀에만 적용되는 스타일 */
        .order-history td.address {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="http://localhost/DBmain2.html">
                <img src="JU유를 JU다.png" alt="로고" width="150" height="100" />
            </a>
        </div>
        <div class="title">
            <h1>스크랩 내역 조회</h1>
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
            <div class="filter">
                <form method="POST" action="">
                    <input type="text" name="search" placeholder="주유소명 또는 주소로 검색" />
                    <button type="submit">조회하기</button>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>스크랩</th>
                        <th>주유소명</th>
                        <th>휘발유 가격</th>
                        <th>경유 가격</th>
                        <th>LPG 가격</th>
                        <th>주유소 주소</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // 결과를 HTML로 출력
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                                <td>
                                    <form method='POST' action='' class='bookmark-form'>
                                        <input type='hidden' name='bookmark_id' value='{$row['id']}'>
                                        <input type='hidden' name='delete_bookmark' value='1'>
                                        <button type='submit' class='bookmark-btn'>삭제</button>
                                    </form>
                                </td>
                                <td>{$row['station_name']}</td>
                                <td>{$row['gasoline_price']}</td>
                                <td>{$row['diesel_price']}</td>
                                <td>{$row['lpg_price']}</td>
                                <td class='address' onclick='copyAddress(this)'>{$row['address']}</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
    
    <script>
        function navigateTo(page) {
            window.location.href = page;
        }

        function copyAddress(element) {
            const address = element.textContent; // 클릭된 주소 텍스트 가져오기
            navigator.clipboard.writeText(address); // 클립보드에 주소 복사
            alert("주소가 복사되었습니다: " + address); // 복사 완료 메시지 표시
        }
    </script>
</body>
</html>
