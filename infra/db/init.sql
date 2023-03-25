DROP DATABASE IF EXISTS hakoniwa;
DROP USER IF EXISTS hakoniwa_user;

CREATE DATABASE hakoniwa DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;
CREATE USER 'hakoniwa_user'@'%' identified BY 'Vp8mD!uS';
GRANT ALL PRIVILEGES ON hakoniwa.* TO 'hakoniwa_user'@'%';

FLUSH PRIVILEGES;
