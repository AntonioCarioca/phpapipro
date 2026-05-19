INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@email.com', '$2y$12$CV/DKzbKBZ3.KP9CPKR9puBJ/NlLTYwqTjZsF8f9gpdTWPEOtVFkK', 'admin')
ON DUPLICATE KEY UPDATE 
name = VALUES(name), 
email = VALUES(email),
password = VALUES(password);