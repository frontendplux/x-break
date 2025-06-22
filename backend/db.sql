CREATE TABLE IF NOT EXISTS videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post JSON,
    status ENUM('inactive', 'active') DEFAULT 'active'
);
