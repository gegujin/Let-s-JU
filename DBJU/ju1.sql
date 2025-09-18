DROP DATABASE IF EXISTS ju;
CREATE DATABASE IF NOT EXISTS ju;
USE ju;


-- Users 테이블 생성
CREATE TABLE Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    myname VARCHAR(50) NOT NULL,
    nickname VARCHAR(50) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    gender ENUM('male', 'female') NOT NULL, -- 성별을 male, female 값으로 제한
    birthdate DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ScrapedContent 테이블 생성
CREATE TABLE IF NOT EXISTS gas_stations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    station_name VARCHAR(255) NOT NULL,
    gasoline_price DECIMAL(10, 2),
    diesel_price DECIMAL(10, 2),
    lpg_price DECIMAL(10, 2),
    address VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS bookmarks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    station_name VARCHAR(255) NOT NULL,
    gasoline_price DECIMAL(10, 2),
    diesel_price DECIMAL(10, 2),
    lpg_price DECIMAL(10, 2),
    address VARCHAR(255) NOT NULL,
    user_id INT NOT NULL, -- 사용자를 식별하기 위한 필드 (예: 로그인 시스템이 있는 경우)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- 주유소 정보 테이블
LOAD DATA INFILE 'C:\\ProgramData\\MySQL\\MySQL Server 8.0\\Uploads\\gas_station_info.txt'
INTO TABLE gas_stations
FIELDS TERMINATED BY ','  -- 쉼표로 필드 구분
LINES TERMINATED BY '\n'  -- 줄 바꿈 문자로 행 구분
IGNORE 1 LINES
(@station_name, @gasoline_price, @diesel_price, @lpg_price, @address)
SET station_name = SUBSTRING_INDEX(@station_name, ',', 1),
    gasoline_price = NULLIF(@gasoline_price, ''),  
    diesel_price = NULLIF(@diesel_price, ''),  -- 빈 문자열을 NULL로 처리
    lpg_price = NULLIF(@lpg_price, ''), 
    address = @address;


-- 기존의 user_id 열 삭제
ALTER TABLE bookmarks
DROP COLUMN user_id;

-- 새로운 user_id 열 추가
ALTER TABLE bookmarks
ADD COLUMN user_id INT NOT NULL;

-- user_id 외래 키 제약 추가
ALTER TABLE bookmarks
ADD CONSTRAINT fk_user_id
FOREIGN KEY (user_id) REFERENCES Users(id);



