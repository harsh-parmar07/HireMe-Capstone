CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100),
  password VARCHAR(255),
  role ENUM('client', 'freelancer', 'admin')
);

CREATE TABLE jobs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_id INT,
  title VARCHAR(255),
  description TEXT,
  budget DECIMAL(10,2),
  deadline DATE,
  status VARCHAR(20),
  FOREIGN KEY (client_id) REFERENCES users(id)
);

CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  job_id INT,
  sender_id INT,
  receiver_id INT,
  content TEXT,
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (job_id) REFERENCES jobs(id),
  FOREIGN KEY (sender_id) REFERENCES users(id),
  FOREIGN KEY (receiver_id) REFERENCES users(id)
);
