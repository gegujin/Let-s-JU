<?php
$servername = "localhost";
$username = "root";
$password = "1277";
$dbname = "ju";

try {
    // PDO 객체 생성
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 데이터베이스 및 테이블 생성 쿼리 실행
    $sql = "
    DROP DATABASE IF EXISTS ju;
    CREATE DATABASE IF NOT EXISTS ju;
    USE ju;
    
    
    -- Users 테이블 생성
    CREATE TABLE Users (
        user_id INT AUTO_INCREMENT PRIMARY KEY, -- 사용자 ID
        nickname VARCHAR(255) UNIQUE, -- 닉네임 (중복 불가)
        password_hash VARCHAR(255), -- 비밀번호 해시
        social_login_id VARCHAR(255), -- 소셜 로그인 ID
        gender ENUM('남자', '여자') -- 성별
    );
    
    -- ScrapedContent 테이블 생성
    CREATE TABLE ScrapedContent (
        scrap_id INT AUTO_INCREMENT PRIMARY KEY, -- 스크랩 ID
        user_id INT, -- 사용자 ID (외래 키)
        content_id INT, -- 콘텐츠 ID
        is_public BOOLEAN, -- 공개 여부
        FOREIGN KEY (user_id) REFERENCES Users(user_id) -- 외래 키 제약 조건
    );
    
    CREATE TABLE gas_stations (
        station_name VARCHAR(255),
        gasoline_price DECIMAL(8,2),
        diesel_price DECIMAL(8,2),
        lpg_price DECIMAL(8,2),  -- LPG 가격이 X일 수 있으므로 DECIMAL로 설정
        address VARCHAR(255)
    );";

    $pdo->exec($sql);

    // CSV 파일에서 데이터를 읽어 데이터베이스에 삽입
    $file_path = 'C:\\ProgramData\\MySQL\\MySQL Server 8.0\\Uploads\\gas_station_info.txt';
    if (($handle = fopen($file_path, "r")) !== FALSE) {
        fgetcsv($handle); // Skip the header row
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $station_name = $pdo->quote($data[0]);
            $gasoline_price = !empty($data[1]) ? $data[1] : 'NULL';
            $diesel_price = !empty($data[2]) ? $data[2] : 'NULL';
            $lpg_price = !empty($data[3]) ? $data[3] : 'NULL';
            $address = $pdo->quote($data[4]);

            $sql_insert = "INSERT INTO gas_stations (station_name, gasoline_price, diesel_price, lpg_price, address)
                           VALUES ($station_name, $gasoline_price, $diesel_price, $lpg_price, $address)";
            $pdo->exec($sql_insert);
        }
        fclose($handle);
    }

    // gas_station 테이블에서 데이터 가져오기 및 표시
    $sql_select = "SELECT * FROM gas_stations";
    $stmt = $pdo->query($sql_select);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($result)) {
        /*
        echo "<table border='1'>
                <tr>
                    <th>주유소 이름</th>
                    <th>휘발유 가격</th>
                    <th>디젤 가격</th>
                    <th>LPG 가격</th>
                    <th>주소</th>
                </tr>";
        foreach ($result as $row) {
            echo "<tr>
                    <td>{$row['station_name']}</td>
                    <td>{$row['gasoline_price']}</td>
                    <td>{$row['diesel_price']}</td>
                    <td>{$row['lpg_price']}</td>
                    <td>{$row['address']}</td>
                  </tr>";
        }
        echo "</table>";
        */ 
    } else {
        // 결과가 없을 때 처리
    }
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
