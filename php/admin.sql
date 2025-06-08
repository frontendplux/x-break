-- drop table users;
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user VARCHAR(100) NOT NULL,
    pass VARCHAR(300) not null,
    role ENUM('admin', 'teacher', 'student') DEFAULT 'student',
    class VARCHAR(100),
    profile JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Exams table
CREATE TABLE IF NOT EXISTS exams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    access_code VARCHAR(100) UNIQUE NOT NULL,
    title VARCHAR(255),
    class VARCHAR(100),
    duration INT, -- in seconds
    questions JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Exam submissions
CREATE TABLE IF NOT EXISTS submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(100),
    access_code VARCHAR(100),
    answers JSON,
    duration INT,
    attempt_number INT,
    score FLOAT DEFAULT 0,
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(student_id, access_code, attempt_number)
);
