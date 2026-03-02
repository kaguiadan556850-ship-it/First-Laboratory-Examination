-- Database Schema for Student Record Management System
-- Import this into XAMPP phpMyAdmin

-- Create Database
CREATE DATABASE IF NOT EXISTS student_management;
USE student_management;

-- Create Users Table (for login)
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Students Table
CREATE TABLE IF NOT EXISTS students (
  id_number VARCHAR(20) PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  course VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample user account for login
-- Username: user1, Password: password123
INSERT INTO users (username, password, email) VALUES 
('user1', '$2y$10$N0YHK3wSWDQdEhOCWq.vauTWRrQsJKfWuwLr5HqzG2xVTwcGJ29Ye', 'user1@example.com');

-- Note: Password hash is for 'password123' using bcrypt
