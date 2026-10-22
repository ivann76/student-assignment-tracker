CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(80) NOT NULL, email VARCHAR(120) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL, role ENUM('student','admin') DEFAULT 'student', token VARCHAR(64) NULL);
CREATE TABLE units (id INT AUTO_INCREMENT PRIMARY KEY, code VARCHAR(20) NOT NULL, name VARCHAR(120) NOT NULL);
CREATE TABLE assignments (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, unit_id INT NOT NULL,
  title VARCHAR(150) NOT NULL, description TEXT, due_date DATE NOT NULL,
  status ENUM('todo','doing','done') DEFAULT 'todo', priority ENUM('low','medium','high') DEFAULT 'medium',
  is_shared TINYINT(1) DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, FOREIGN KEY (unit_id) REFERENCES units(id));
CREATE TABLE likes (user_id INT, assignment_id INT, PRIMARY KEY (user_id, assignment_id),
  FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE);
CREATE TABLE comments (id INT AUTO_INCREMENT PRIMARY KEY, assignment_id INT NOT NULL, user_id INT NOT NULL,
  body VARCHAR(500) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE);
INSERT INTO units (code,name) VALUES ('COS30043','Interface Design and Development'),('COS20031','Database Design'),('COS10009','Introduction to Programming');
