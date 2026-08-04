-- =============================================
-- AssetTracker Seed Data
-- =============================================

-- Departments
INSERT INTO departments (name) VALUES
('Human Resources'),
('Engineering'),
('Marketing'),
('Finance'),
('Operations'),
('Sales'),
('IT Support'),
('Legal');

-- Designations
INSERT INTO designations (name) VALUES
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

-- Users (all passwords are hashed version of "password123")
INSERT INTO users (name, email, mobile, department_id, designation_id, password) VALUES
('Rahul Sharma', 'rahul.sharma@example.com', '9876543210', 2, 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Priya Patel', 'priya.patel@example.com', '9876543211', 1, 5, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Amit Kumar', 'amit.kumar@example.com', '9876543212', 2, 2, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Sneha Reddy', 'sneha.reddy@example.com', '9876543213', 3, 6, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Vikram Singh', 'vikram.singh@example.com', '9876543214', 4, 7, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Anjali Gupta', 'anjali.gupta@example.com', '9876543215', 5, 8, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Karan Mehta', 'karan.mehta@example.com', '9876543216', 6, 9, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Neha Joshi', 'neha.joshi@example.com', '9876543217', 7, 10, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Rohan Desai', 'rohan.desai@example.com', '9876543218', 2, 3, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Divya Nair', 'divya.nair@example.com', '9876543219', 8, 4, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
