-- ====================================================================
-- SQL Script: Seed 20 Users with Specific ENUM Roles ('ADMIN', 'HR', 'MANAGER', 'EMPLOYEE')
-- ====================================================================

-- 1. Ensure all core Departments exist
INSERT IGNORE INTO `departments` (`name`) VALUES
('Human Resources'),
('Engineering'),
('Marketing'),
('Finance'),
('Operations'),
('Sales'),
('IT Support'),
('Legal');

-- 2. Ensure all core Designations exist
INSERT IGNORE INTO `designations` (`name`) VALUES
('Software Engineer'),
('Senior Software Engineer'),
('Team Lead'),
('Project Manager'),
('HR Manager'),
('Marketing Executive'),
('Financial Analyst'),
('Operations Manager'),
('Sales Executive'),
('IT Administrator');

-- 3. Add 'role' ENUM column to 'users' table if it does not exist with the correct uppercase values
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `role` ENUM('ADMIN', 'HR', 'MANAGER', 'EMPLOYEE') DEFAULT 'EMPLOYEE';

-- 4. Seed 20 users resolving department and designation IDs dynamically
-- Note: 'password123' hashed with bcrypt: '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
INSERT INTO `users` (`name`, `email`, `mobile`, `department_id`, `designation_id`, `role`, `password`) VALUES
-- Admins (Full Access)
('Rajesh Varma', 'rajesh.varma@example.com', '9811224401', 
  (SELECT `id` FROM `departments` WHERE `name` = 'IT Support' LIMIT 1), 
  (SELECT `id` FROM `designations` WHERE `name` = 'IT Administrator' LIMIT 1), 
  'ADMIN', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
  
('Sandeep Kumar', 'sandeep.kumar@example.com', '9811224402', 
  (SELECT `id` FROM `departments` WHERE `name` = 'Engineering' LIMIT 1), 
  (SELECT `id` FROM `designations` WHERE `name` = 'Team Lead' LIMIT 1), 
  'ADMIN', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),

-- HR Users (Employee Management)
('Sunita Iyer', 'sunita.iyer@example.com', '9811224403', 
  (SELECT `id` FROM `departments` WHERE `name` = 'Human Resources' LIMIT 1), 
  (SELECT `id` FROM `designations` WHERE `name` = 'HR Manager' LIMIT 1), 
  'HR', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
  
('Pooja Nair', 'pooja.nair@example.com', '9811224404', 
  (SELECT `id` FROM `departments` WHERE `name` = 'Human Resources' LIMIT 1), 
  (SELECT `id` FROM `designations` WHERE `name` = 'HR Manager' LIMIT 1), 
  'HR', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
  
('Renu Saxena', 'renu.saxena@example.com', '9811224405', 
  (SELECT `id` FROM `departments` WHERE `name` = 'Human Resources' LIMIT 1), 
  (SELECT `id` FROM `designations` WHERE `name` = 'HR Manager' LIMIT 1), 
  'HR', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),

-- Managers (IT & Asset Management)
('Vikas Gupta', 'vikas.gupta@example.com', '9811224406', 
  (SELECT `id` FROM `departments` WHERE `name` = 'IT Support' LIMIT 1), 
  (SELECT `id` FROM `designations` WHERE `name` = 'IT Administrator' LIMIT 1), 
  'MANAGER', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
  
('Suresh Pillai', 'suresh.pillai@example.com', '9811224407', 
  (SELECT `id` FROM `departments` WHERE `name` = 'Engineering' LIMIT 1), 
  (SELECT `id` FROM `designations` WHERE `name` = 'Project Manager' LIMIT 1), 
  'MANAGER', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),

-- Employees (Read Only)
('Divya Nair', 'divya.nair2@example.com', '9811224408', 
  (SELECT `id` FROM `departments` WHERE `name` = 'Engineering' LIMIT 1), 
  (SELECT `id` FROM `designations` WHERE `name` = 'Software Engineer' LIMIT 1), 
  'EMPLOYEE', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
  
('Amit Kumar', 'amit.kumar2@example.com', '9811224409', 
  (SELECT `id` FROM `departments` WHERE `name` = 'Engineering' LIMIT 1), 
  (SELECT `id` FROM `designations` WHERE `name` = 'Senior Software Engineer' LIMIT 1), 
  'EMPLOYEE', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
  
('Rohan Desai', 'rohan.desai2@example.com', '9811224410', 
  (SELECT `id` FROM `departments` WHERE `name` = 'Engineering' LIMIT 1), 
  (SELECT `id` FROM `designations` WHERE `name` = 'Senior Software Engineer' LIMIT 1), 
  'EMPLOYEE', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
  
('Sneha Reddy', 'sneha.reddy2@example.com', '9811224411', 
  (SELECT `id` FROM `departments` WHERE `name` = 'Marketing' LIMIT 1), 
  (SELECT `id` FROM `designations` WHERE `name` = 'Marketing Executive' LIMIT 1), 
  'EMPLOYEE', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
  
('Vikram Singh', 'vikram.singh2@example.com', '9811224412', 
  (SELECT `id` FROM `departments` WHERE `name` = 'Finance' LIMIT 1), 
  (SELECT `id` FROM `designations` WHERE `name` = 'Financial Analyst' LIMIT 1), 
  'EMPLOYEE', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
  
('Anjali Gupta', 'anjali.gupta2@example.com', '9811224413', 
  (SELECT `id` FROM `departments` WHERE `name` = 'Operations' LIMIT 1), 
  (SELECT `id` FROM `designations` WHERE `name` = 'Operations Manager' LIMIT 1), 
  'EMPLOYEE', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
  
('Karan Mehta', 'karan.mehta2@example.com', '9811224414', 
  (SELECT `id` FROM `departments` WHERE `name` = 'Sales' LIMIT 1), 
  (SELECT `id` FROM `designations` WHERE `name` = 'Sales Executive' LIMIT 1), 
  'EMPLOYEE', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
  
('Neha Joshi', 'neha.joshi2@example.com', '9811224415', 
  (SELECT `id` FROM `departments` WHERE `name` = 'IT Support' LIMIT 1), 
  (SELECT `id` FROM `designations` WHERE `name` = 'IT Administrator' LIMIT 1), 
  'EMPLOYEE', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
  
('Rohit Sharma', 'rohit.sharma@example.com', '9811224416', 
  (SELECT `id` FROM `departments` WHERE `name` = 'Sales' LIMIT 1), 
  (SELECT `id` FROM `designations` WHERE `name` = 'Sales Executive' LIMIT 1), 
  'EMPLOYEE', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
  
('Kavita Rao', 'kavita.rao@example.com', '9811224417', 
  (SELECT `id` FROM `departments` WHERE `name` = 'Marketing' LIMIT 1), 
  (SELECT `id` FROM `designations` WHERE `name` = 'Marketing Executive' LIMIT 1), 
  'EMPLOYEE', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
  
('Manoj Pandey', 'manoj.pandey@example.com', '9811224418', 
  (SELECT `id` FROM `departments` WHERE `name` = 'Finance' LIMIT 1), 
  (SELECT `id` FROM `designations` WHERE `name` = 'Financial Analyst' LIMIT 1), 
  'EMPLOYEE', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
  
('Ajay Chauhan', 'ajay.chauhan@example.com', '9811224419', 
  (SELECT `id` FROM `departments` WHERE `name` = 'Operations' LIMIT 1), 
  (SELECT `id` FROM `designations` WHERE `name` = 'Operations Manager' LIMIT 1), 
  'EMPLOYEE', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
  
('Shalini Menon', 'shalini.menon@example.com', '9811224420', 
  (SELECT `id` FROM `departments` WHERE `name` = 'Legal' LIMIT 1), 
  (SELECT `id` FROM `designations` WHERE `name` = 'Project Manager' LIMIT 1), 
  'EMPLOYEE', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
