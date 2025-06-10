CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100),
  password VARCHAR(255),
  role ENUM('client', 'freelancer')
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
