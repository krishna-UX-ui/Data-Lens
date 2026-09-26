<?php
// auth.php - DataLens Backend Authentication Endpoint
// Handles Registration, Login, Logout, and Session Verification with PDO & password_hash/verify

header('Content-Type: application/json; charset=utf-8');

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

// Helper to send JSON response and exit
function sendResponse($success, $message, $extra = []) {
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message
    ], $extra));
    exit;
}

// Read input data (handles both JSON payloads and standard POST Form data)
$inputData = [];
$rawBody = file_get_contents('php://input');
if (!empty($rawBody)) {
    $decoded = json_decode($rawBody, true);
    if (is_array($decoded)) {
        $inputData = $decoded;
    }
}
// Merge with $_POST if available
$inputData = array_merge($inputData, $_POST);

$action = isset($_GET['action']) ? trim($_GET['action']) : (isset($inputData['action']) ? trim($inputData['action']) : '');

try {
    $pdo = getDB();
} catch (Exception $e) {
    sendResponse(false, 'Database connection error: ' . $e->getMessage());
}

switch ($action) {
    case 'login':
        $email = isset($inputData['email']) ? trim($inputData['email']) : '';
        $password = isset($inputData['password']) ? (string)$inputData['password'] : '';

        if (empty($email) || empty($password)) {
            sendResponse(false, 'Please provide both email and password.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendResponse(false, 'Please enter a valid email address.');
        }

        // PDO Prepared Statement to look up user
        $stmt = $pdo->prepare('SELECT id, full_name, email, password_hash FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            sendResponse(false, 'Invalid email or password. Please verify your credentials.');
        }

        // Successful authentication: create PHP session
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['logged_in_time'] = time();

        sendResponse(true, 'Login successful! Welcome back, ' . htmlspecialchars($user['full_name']) . '.', [
            'user' => [
                'id' => (int)$user['id'],
                'name' => $user['full_name'],
                'email' => $user['email']
            ]
        ]);
        break;

    case 'register':
        $fullName = isset($inputData['full_name']) ? trim($inputData['full_name']) : (isset($inputData['name']) ? trim($inputData['name']) : '');
        $email = isset($inputData['email']) ? trim($inputData['email']) : '';
        $password = isset($inputData['password']) ? (string)$inputData['password'] : '';

        if (empty($fullName)) {
            sendResponse(false, 'Full name is required.');
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendResponse(false, 'A valid email address is required.');
        }

        if (strlen($password) < 8) {
            sendResponse(false, 'Password must be at least 8 characters long.');
        }

        // Check for duplicate email using PDO prepared statement
        $checkStmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $checkStmt->execute([$email]);
        if ($checkStmt->fetch()) {
            sendResponse(false, 'An account with this email already exists. Please sign in instead.');
        }

        // Hash password securely using password_hash() - NEVER store plain text
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user using PDO prepared statement
        $insertStmt = $pdo->prepare('INSERT INTO users (full_name, email, password_hash, created_at) VALUES (?, ?, ?, NOW())');
        $insertStmt->execute([$fullName, $email, $passwordHash]);
        $newUserId = (int)$pdo->lastInsertId();

        // Create PHP session upon registration
        session_regenerate_id(true);
        $_SESSION['user_id'] = $newUserId;
        $_SESSION['user_name'] = $fullName;
        $_SESSION['user_email'] = $email;
        $_SESSION['logged_in_time'] = time();

        sendResponse(true, 'Account created successfully! Welcome to DataLens, ' . htmlspecialchars($fullName) . '.', [
            'user' => [
                'id' => $newUserId,
                'name' => $fullName,
                'email' => $email
            ]
        ]);
        break;

    case 'check_session':
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            sendResponse(true, 'Session active', [
                'authenticated' => true,
                'user' => [
                    'id' => (int)$_SESSION['user_id'],
                    'name' => $_SESSION['user_name'] ?? 'User',
                    'email' => $_SESSION['user_email'] ?? ''
                ]
            ]);
        } else {
            sendResponse(true, 'No active session', [
                'authenticated' => false
            ]);
        }
        break;

    case 'logout':
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
        sendResponse(true, 'Signed out successfully.');
        break;

    default:
        sendResponse(false, 'Invalid authentication action requested.');
        break;
}
