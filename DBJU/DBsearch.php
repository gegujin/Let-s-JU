<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gas Station Finder</title>
    <style>
        /* CSS */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        header {
            background-color: #1a1a2e; /* 상단 배경색 설정 */
            padding: 20px;
            display: flex;
            justify-content: space-between; /* 좌우로 배치 */
            align-items: center;
        }

        .logo img {
            max-width: 200px;
            height: auto;
            cursor: pointer; /* 이미지에 커서 포인터 추가 */
        }

        .header-buttons {
            display: flex;
            align-items: center;
        }

        .header-buttons button {
            margin-left: 10px; /* 버튼 간격 조절 */
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #ffffff;
            cursor: pointer;
        }

        main {
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .filter-section {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .filter-section label {
            font-weight: bold;
            margin-right: 10px;
        }

        .filter-section select, .filter-section input {
            padding: 5px;
            margin-left: 10px;
        }

        .results-section {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
        }

        .result-item {
            display: flex;
            flex-direction: column;
            border-bottom: 1px solid #ccc;
            padding-bottom: 20px;
            margin-bottom: 20px;
            position: relative;
        }

        .result-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .info {
            flex: 1;
        }

        .info p {
            margin: 5px 0;
        }

        .bookmark-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 20px;
            color: #000;
        }

        .bookmark-btn.clicked {
            color: #FFA500;
        }

        .address {
            cursor: pointer;
            color: blue;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="http://localhost/DBmain2.html">
                <img src="JU유를 JU다.png" alt="Gas Station Finder Logo"> <!-- 이미지 파일 경로 설정 -->
            </a>
        </div>
        <div class="header-buttons">
            <button onclick="navigateToPage('DBmypage.php')">마이페이지</button>
            <button onclick="navigateToPage('DBmain.html')">로그아웃</button>
        </div>
    </header>

    <main>
        <section class="filter-section">
            <!-- 필터 섹션 -->
            <label for="fuel-type">연료 유형별 정렬:</label>
            <select id="fuel-type">
                <option value="gasoline">휘발유</option>
                <option value="diesel">경유</option>
                <option value="lpg">LPG</option>
            </select>
            <label for="address-filter">주소 필터:</label>
            <input type="text" id="address-filter" placeholder="주소 입력">
        </section>

        <section class="results-section" id="results-section">
            <!-- 결과 섹션 -->
            <?php
session_start(); // 세션 시작

// MySQL 데이터베이스 연결 설정
$host = 'localhost'; // 호스트 이름 또는 IP 주소    
$dbname = 'ju'; // 데이터베이스 이름
$username = 'root'; // 데이터베이스 사용자 이름
$password = '1277'; // 비밀번호

// MySQL 데이터베이스 연결
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}

// 북마크 추가/제거 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id']; // 현재 사용자의 ID
    } else {
        // 로그인이 되어 있지 않으면 처리 중단
        echo '<script>window.addEventListener("DOMContentLoaded", function(){ alert("로그인 후 이용해주세요!"); });</script>';
        exit;
    }

    $station_name = $_POST['station_name'];
    $gasoline_price = $_POST['gasoline_price'] !== '' ? $_POST['gasoline_price'] : NULL;
    $diesel_price = $_POST['diesel_price'] !== '' ? $_POST['diesel_price'] : NULL;
    $lpg_price = $_POST['lpg_price'] !== '' ? $_POST['lpg_price'] : NULL;
    $address = $_POST['address'];
    $action = $_POST['action'];

    if ($action === 'add') {
        // 북마크 추가 쿼리
        $query = "INSERT INTO bookmarks (station_name, gasoline_price, diesel_price, lpg_price, address, user_id)
                  VALUES (:station_name, :gasoline_price, :diesel_price, :lpg_price, :address, :user_id)";
    } elseif ($action === 'remove') {
        // 북마크 제거 쿼리
        $query = "DELETE FROM bookmarks WHERE station_name = :station_name AND user_id = :user_id";
    }

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':station_name', $station_name);
    $stmt->bindParam(':gasoline_price', $gasoline_price);
    $stmt->bindParam(':diesel_price', $diesel_price);
    $stmt->bindParam(':lpg_price', $lpg_price);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':user_id', $user_id); // 세션에 저장된 사용자 ID를 사용합니다.
    $stmt->execute();

    // 알림 메시지 표시
    if ($action === 'add') {
        echo '<script>window.addEventListener("DOMContentLoaded", function(){ alert("스크랩에 저장되었습니다!"); });</script>';
    } elseif ($action === 'remove') {
        echo '<script>window.addEventListener("DOMContentLoaded", function(){ alert("스크랩에서 제거되었습니다!"); });</script>';
    }
}

// 중복된 주유소 정보를 제거하는 쿼리
$query = "SELECT DISTINCT gs.station_name, gs.gasoline_price, gs.diesel_price, gs.lpg_price, gs.address, 
          CASE WHEN b.station_name IS NOT NULL THEN 'true' ELSE 'false' END AS is_bookmarked
          FROM gas_stations gs
          LEFT JOIN bookmarks b ON gs.station_name = b.station_name AND b.user_id = :user_id";

// 쿼리 실행
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']); // 세션에 저장된 사용자 ID를 사용합니다.
$stmt->execute();

// 결과를 HTML로 출력
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $is_bookmarked = $row['is_bookmarked'] === 'true' ? 'clicked' : '';
    echo '<div class="result-item" data-name="'.$row['station_name'].'" data-gasoline="'.$row['gasoline_price'].'" data-diesel="'.$row['diesel_price'].'" data-lpg="'.$row['lpg_price'].'" data-address="'.$row['address'].'">';
    echo '<form method="POST" class="bookmark-form">';
    echo '<input type="hidden" name="station_name" value="'.$row['station_name'].'">';
    echo '<input type="hidden" name="gasoline_price" value="'.$row['gasoline_price'].'">';
    echo '<input type="hidden" name="diesel_price" value="'.$row['diesel_price'].'">';
    echo '<input type="hidden" name="lpg_price" value="'.$row['lpg_price'].'">';
    echo '<input type="hidden" name="address" value="'.$row['address'].'">';
    echo '<input type="hidden" name="action" value="'.($is_bookmarked ? 'remove' : 'add').'">';
    echo '<button type="submit" class="bookmark-btn '.$is_bookmarked.'">★</button>';
    echo '</form>';
    echo '<div class="info">';
    echo '<p><span>주유소명:</span> '.$row['station_name'].'</p>';
    echo '<p class="gasoline price"><span>휘발유:</span> '.($row['gasoline_price'] ? $row['gasoline_price'].'원' : '').'</p>';
    echo '<p class="diesel price"><span>경유:</span> '.($row['diesel_price'] ? $row['diesel_price'].'원' : '').'</p>';
    echo '<p class="lpg price"><span>LPG:</span> '.($row['lpg_price'] ? $row['lpg_price'].'원' : '').'</p>';
    echo '<p class="address" data-address="'.$row['address'].'"><span>주소:</span> '.$row['address'].'</p>';
    echo '</div></div>';
}
?>


        </section>
    </main>

    <script>
        // 클라이언트 측 JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.bookmark-btn').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const form = button.closest('form');
                    const formData = new FormData(form);
                    const action = formData.get('action');
                    const stationName = formData.get('station_name');

                    fetch('', {
                        method: 'POST',
                        body: formData
                    }).then(response => response.text()).then(text => {
                        if (action === 'add') {
                            button.classList.add('clicked');
                            button.closest('form').querySelector('input[name="action"]').value = 'remove';
                            alert(stationName + '이(가) 스크랩에 저장되었습니다!');
                        } else {
                            button.classList.remove('clicked');
                            button.closest('form').querySelector('input[name="action"]').value = 'add';
                            alert(stationName + '이(가) 스크랩에서 제거되었습니다!');
                        }
                    }).catch(error => console.error('Error:', error));
                });
            });

            document.querySelectorAll('.address').forEach(addressElem => {
                addressElem.addEventListener('click', function() {
                    const address = addressElem.getAttribute('data-address');
                    copyAddress(address);
                });
            });

            document.getElementById('fuel-type').addEventListener('change', sortByFuelType);
            document.getElementById('address-filter').addEventListener('input', filterByAddress);

            // URL에서 주소 파라미터를 가져와서 address-filter에 설정
            const urlParams = new URLSearchParams(window.location.search);
            const location = urlParams.get('location');
            if (location) {
                document.getElementById('address-filter').value = location;
                filterByAddress();
            }
        });

        // 연료 유형에 따라 정렬하는 함수
        function sortByFuelType() {
            const selectedFuelType = document.getElementById('fuel-type').value;
            const resultsSection = document.getElementById('results-section');
            const stations = Array.from(resultsSection.querySelectorAll('.result-item'));

            stations.sort((a, b) => {
                const priceA = parseFloat(a.getAttribute(`data-${selectedFuelType}`)) || Infinity;
                const priceB = parseFloat(b.getAttribute(`data-${selectedFuelType}`)) || Infinity;
                return priceA - priceB;
            });

            resultsSection.innerHTML = '';
            stations.forEach(station => resultsSection.appendChild(station));
        }

        // 주소로 필터링하는 함수
        function filterByAddress() {
            const address = document.getElementById('address-filter').value.trim();
            const resultsSection = document.getElementById('results-section');
            const stations = Array.from(resultsSection.querySelectorAll('.result-item'));

            stations.forEach(station => {
                const stationAddress = station.getAttribute('data-address');
                if (stationAddress.includes(address) || address === '') {
                    station.style.display = 'block';
                } else {
                    station.style.display = 'none';
                }
            });
        }

        // 주소 복사 함수
        function copyAddress(address) {
            navigator.clipboard.writeText(address).then(function() {
                alert('주소가 복사되었습니다: ' + address);
            }, function(err) {
                console.error('주소 복사에 실패했습니다: ', err);
            });
        }

        // 페이지 이동 함수
        function navigateToPage(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
