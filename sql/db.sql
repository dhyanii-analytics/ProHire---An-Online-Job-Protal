CREATE DATABASE IF NOT EXISTS pro_db;
USE pro_db;

-- Users Table (Job Seekers)
CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    location VARCHAR(100),
    skills TEXT,
    experience TEXT,
    education TEXT,
    resume_path VARCHAR(255),
    profile_pic VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Companies Table
CREATE TABLE companies (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    industry VARCHAR(100),
    description TEXT,
    logo_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Jobs Table
CREATE TABLE jobs (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    company_id INT(11) NOT NULL,
    job_title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    requirements TEXT,
    salary VARCHAR(100),
    location VARCHAR(100),
    job_type VARCHAR(50),
    experience_required VARCHAR(50),
    posted_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deadline DATE,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

-- Applications Table
CREATE TABLE applications (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    job_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    applied_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'Applied',
    FOREIGN KEY (job_id) REFERENCES jobs(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Admin Table
CREATE TABLE admin (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);


-- Saved Job
CREATE TABLE saved_jobs (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    job_id INT(11) NOT NULL,
    saved_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_job (user_id, job_id)
);

-- Insert sample data for admin
-- Username: admin, Password: admin123
INSERT INTO admin (username, password) VALUES ('admin', 'admin123');

-- Insert sample data for company
-- Email: company@techsolutions.com, Password: company123
INSERT INTO companies (company_name, email, password, phone, address, industry, description) 
VALUES ('Tech Solutions Inc.', 'company@techsolutions.com', 'company123', '1234567890', '123 Tech Street, cg road', 'Technology', 'Leading technology solutions provider');

-- Insert sample data for user
-- Email: john@example.com, Password: user123
INSERT INTO users (full_name, email, password, phone, location, skills, experience, education) 
VALUES ('John wick', 'john@example.com', 'user123', '9876543210', 'Gujrat', 'PHP, JavaScript, MySQL', '5 years in web development', 'BSc Computer Science');

-- Insert sample data for job
INSERT INTO jobs (company_id, job_title, description, requirements, salary, location, job_type, experience_required, deadline) 
VALUES (1, 'Senior PHP Developer', 'We are looking for an experienced PHP developer...', '5+ years of PHP experience, MySQL knowledge', '80,000 - 100,000', 'Tamil Nadu', 'Full-time', '5+ years', '2025-12-31'),
(1, 'Frontend Developer', 'We are looking for a skilled Frontend Developer...', '3+ years of React experience, HTML, CSS, JavaScript', '70,000 - 90,000', 'Gujrat', 'Full-time', '3+ years', '2025-12-15');
