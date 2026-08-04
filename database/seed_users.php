<?php
/**
 * AssetTracker — User Seeder
 * Run: php database/seed_users.php
 *
 * Inserts 15 realistic users spread across all departments & designations.
 * All passwords are "password123".
 * Skips any row whose email or mobile already exists.
 */

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$host = $_ENV['DB_HOST']     ?? 'localhost';
$db   = $_ENV['DB_NAME']     ?? 'asset_tracker';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage() . "\n");
}

// password_hash of "password123"
$hash = password_hash('password123', PASSWORD_DEFAULT);

/*
 * department_id  name
 * 1  Human Resources
 * 2  Engineering
 * 3  Marketing
 * 4  Finance
 * 5  Operations
 * 6  Sales
 * 7  IT Support
 * 8  Legal
 *
 * designation_id  name
 * 1  Software Engineer
 * 2  Senior Software Engineer
 * 3  Team Lead
 * 4  Project Manager
 * 5  HR Manager
 * 6  Marketing Executive
 * 7  Financial Analyst
 * 8  Operations Manager
 * 9  Sales Executive
 * 10 IT Administrator
 */
$users = [
    ['Arjun Verma',        'arjun.verma@example.com',        '9811223344', 2,  1],
    ['Pooja Iyer',         'pooja.iyer@example.com',         '9811223345', 1,  5],
    ['Suresh Bhat',        'suresh.bhat@example.com',        '9811223346', 7, 10],
    ['Meera Krishnan',     'meera.krishnan@example.com',     '9811223347', 3,  6],
    ['Aakash Pandey',      'aakash.pandey@example.com',      '9811223348', 4,  7],
    ['Ritika Saxena',      'ritika.saxena@example.com',      '9811223349', 5,  8],
    ['Deepak Chauhan',     'deepak.chauhan@example.com',     '9811223350', 6,  9],
    ['Kavya Menon',        'kavya.menon@example.com',        '9811223351', 8,  4],
    ['Harish Rao',         'harish.rao@example.com',         '9811223352', 2,  2],
    ['Sunita Tiwari',      'sunita.tiwari@example.com',      '9811223353', 1,  5],
    ['Manish Agarwal',     'manish.agarwal@example.com',     '9811223354', 2,  3],
    ['Pallavi Shukla',     'pallavi.shukla@example.com',     '9811223355', 3,  6],
    ['Girish Naik',        'girish.naik@example.com',        '9811223356', 4,  7],
    ['Lakshmi Pillai',     'lakshmi.pillai@example.com',     '9811223357', 7, 10],
    ['Rahul Kapoor',       'rahul.kapoor@example.com',       '9811223358', 6,  9],
];

$insertStmt = $conn->prepare(
    'INSERT INTO users (name, email, mobile, department_id, designation_id, password)
     VALUES (:name, :email, :mobile, :department_id, :designation_id, :password)'
);

$checkEmail  = $conn->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
$checkMobile = $conn->prepare('SELECT COUNT(*) FROM users WHERE mobile = ?');

$created = 0;
$skipped = 0;

foreach ($users as [$name, $email, $mobile, $deptId, $desigId]) {
    // Check email
    $checkEmail->execute([$email]);
    if ($checkEmail->fetchColumn() > 0) {
        echo "  SKIP  (email exists)   : $name <$email>\n";
        $skipped++;
        continue;
    }

    // Check mobile
    $checkMobile->execute([$mobile]);
    if ($checkMobile->fetchColumn() > 0) {
        echo "  SKIP  (mobile exists)  : $name ($mobile)\n";
        $skipped++;
        continue;
    }

    $insertStmt->execute([
        'name'           => $name,
        'email'          => strtolower($email),
        'mobile'         => $mobile,
        'department_id'  => $deptId,
        'designation_id' => $desigId,
        'password'       => $hash,
    ]);

    echo "  CREATED : $name <$email>\n";
    $created++;
}

echo "\n✔  Done — $created user(s) created, $skipped skipped.\n";
echo "   Password for all new users: password123\n";
