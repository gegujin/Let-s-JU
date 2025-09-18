<?php
// 로그인 페이지에서 사용자 정보를 세션에 저장한 후, 이 페이지에서 세션을 이용하여 정보를 가져옵니다.
session_start();

// 세션에서 사용자 아이디를 가져옵니다.
$userid = $_SESSION['user_id']; // 이때, 로그인 시에 저장한 세션 키를 사용해야 합니다.

// MySQL 연결 설정
$servername = "localhost"; // MySQL 서버 주소
$username = "root"; // MySQL 사용자명
$password = "1277"; // MySQL 비밀번호
$dbname = "ju"; // 사용할 데이터베이스명

// MySQL 연결 생성
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die("연결 실패: " . $conn->connect_error);
}

$updateSuccess = false; // 업데이트 성공 여부를 저장할 변수

// 사용자 이름 업데이트 처리
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newMyname = $_POST['myname'];
    $updateSql = "UPDATE Users SET myname = '$newMyname' WHERE id = '$userid'";
    if ($conn->query($updateSql) === TRUE) {
        $updateSuccess = true;
    } else {
        echo "<script>alert('이름 업데이트 실패: " . $conn->error . "');</script>";
    }
}

// MySQL에서 사용자 정보 가져오기
$sql = "SELECT myname, nickname, gender, birthdate FROM Users WHERE id = '$userid'"; // 세션에서 가져온 아이디를 사용하여 사용자 정보를 가져옴
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // 데이터가 존재하는 경우
    $row = $result->fetch_assoc();
    $myname = $row["myname"];
    $nickname = $row["nickname"];
    $gender = $row["gender"];
    $birthdate = $row["birthdate"];
} else {
    // 데이터가 존재하지 않는 경우
    echo "사용자 정보를 찾을 수 없습니다.";
}

// MySQL 연결 종료
$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>개인정보</title>
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

        .profile-info {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            position: relative;
        }

        .profile-info h2 {
            margin-top: 0;
        }

        .profile-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .profile-info table,
        th,
        td {
            border: 1px solid #ddd;
        }

        .profile-info th,
        .profile-info td {
            padding: 15px;
            text-align: left;
        }

        .profile-info th {
            background-color: #f4f4f4;
            width: 25%; /* 모든 테이블의 회색 부분의 길이를 같게 설정 */
        }

        .profile-info td {
            width: 75%;
        }

        .profile-info form {
            margin-bottom: 60px; /* 폼의 하단 마진을 추가하여 버튼 공간 확보 */
        }

        .profile-info button {
            position: absolute;
            right: 20px;
            bottom: 20px;
            padding: 10px 20px;
            background-color: #1a1a2e;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<header>
    <div class="logo">
        <a href="DBmain2.html">
            <img src="JU유를 JU다.png" alt="로고" width="150" height="100"/>
        </a>
    </div>
    <div class="title">
        <h1>개인정보</h1>
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
    <div class="profile-info">
        <h2>회원정보</h2>
        <form method="post" action="">
            <table>
                <tr>
                    <th>이름</th>
                    <td><input type="text" name="myname" value="<?php echo $myname; ?>" required></td>
                </tr>
                <tr>
                    <th>닉네임</th>
                    <td><?php echo $nickname; ?></td>
                </tr>
                <tr>
                    <th>성별</th>
                    <td><?php echo $gender; ?></td>
                </tr>
                <tr>
                    <th>생년월일</th>
                    <td><?php echo $birthdate; ?></td>
                </tr>
            </table>
            <button type="submit">저장</button>
        </form>
    </div>
</main>

<script>
    function navigateTo(url) {
        window.location.href = url;
    }

    <?php if ($updateSuccess) { ?>
    alert('이름이 성공적으로 업데이트 되었습니다.');
    <?php } ?>
</script>
</body>
</html>
