<?php
// index.php - DataLens PHP Web Application Entry Point
// Starts PHP session and serves the DataLens Studio application

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pass PHP session state to JavaScript window variable if logged in
$sessionJson = 'null';
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $sessionJson = json_encode([
        'loggedIn' => true,
        'id' => (int)$_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? 'User',
        'email' => $_SESSION['user_email'] ?? '',
        'timestamp' => time() * 1000
    ]);
}

// Read and serve DataLens_.html with dynamic session awareness
$html = file_get_contents(__DIR__ . '/DataLens_.html');

// Inject the server session state right before </head>
$injectScript = "<script>window.DATALENS_PHP_SESSION = " . $sessionJson . ";</script>\n</head>";
$html = str_replace('</head>', $injectScript, $html);

echo $html;
