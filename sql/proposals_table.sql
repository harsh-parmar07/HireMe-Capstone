CREATE TABLE proposals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  job_id INT,
  freelancer_id INT,
  bid_amount DECIMAL(10,2),
  proposal_text TEXT,
  status VARCHAR(20),
  FOREIGN KEY (job_id) REFERENCES jobs(id),
  FOREIGN KEY (freelancer_id) REFERENCES users(id)
);
