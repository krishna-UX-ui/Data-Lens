<?php
// admin/users.php - Protected DataLens Admin Directory
// Displays registered users: ID, Name, Email, Created At
// NEVER queries or displays passwords or password hashes

session_start();

// Protection: Require active PHP session
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

require_once __DIR__ . '/../db.php';

$currentUserName = $_SESSION['user_name'] ?? 'Admin User';
$currentUserEmail = $_SESSION['user_email'] ?? '';
$error = '';
$users = [];

try {
    $pdo = getDB();
    // PDO prepared statement - NEVER query password_hash
    $stmt = $pdo->prepare('SELECT id, full_name, email, created_at FROM users ORDER BY id ASC');
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (Exception $e) {
    $error = 'Database error: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DataLens Admin — User Directory</title>
  <style>
    :root {
      --bg-deep: #050811;
      --bg-surface: #0a1124;
      --bg-card: rgba(14, 23, 47, 0.75);
      --border-glow: rgba(0, 240, 255, 0.2);
      --border-subtle: rgba(255, 255, 255, 0.08);
      --cyan: #00F0FF;
      --cyan-glow: rgba(0, 240, 255, 0.35);
      --blue: #3B82F6;
      --gold: #D4AF37;
      --text-main: #F8FAFC;
      --text-muted: #94A3B8;
      --text-dim: #64748B;
      --success: #34D399;
      --error: #F87171;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      background-color: var(--bg-deep);
      background-image: 
        radial-gradient(circle at 15% 15%, rgba(0, 240, 255, 0.06) 0%, transparent 45%),
        radial-gradient(circle at 85% 85%, rgba(59, 130, 246, 0.07) 0%, transparent 45%),
        linear-gradient(to bottom, #050811, #080e1f);
      color: var(--text-main);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Top Navigation Header */
    .admin-nav {
      background: rgba(10, 16, 33, 0.85);
      backdrop-filter: blur(16px);
      border-bottom: 1px solid var(--border-glow);
      padding: 16px 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .brand-section {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .brand-title {
      font-size: 1.25rem;
      font-weight: 800;
      letter-spacing: 0.12em;
      color: #fff;
    }

    .brand-title span {
      color: var(--cyan);
    }

    .badge-admin {
      background: rgba(0, 240, 255, 0.12);
      border: 1px solid rgba(0, 240, 255, 0.35);
      color: var(--cyan);
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      padding: 3px 9px;
      border-radius: 999px;
    }

    .nav-actions {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .user-pill {
      display: flex;
      align-items: center;
      gap: 9px;
      padding: 6px 14px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid var(--border-subtle);
      border-radius: 999px;
      font-size: 0.86rem;
      color: var(--text-muted);
    }

    .user-avatar {
      width: 26px;
      height: 26px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--cyan), var(--blue));
      color: #050811;
      font-weight: 800;
      font-size: 0.75rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 18px;
      font-size: 0.86rem;
      font-weight: 600;
      border-radius: 8px;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .btn-outline {
      background: rgba(0, 240, 255, 0.05);
      color: var(--cyan);
      border: 1px solid rgba(0, 240, 255, 0.35);
    }

    .btn-outline:hover {
      background: rgba(0, 240, 255, 0.15);
      border-color: var(--cyan);
      box-shadow: 0 0 14px var(--cyan-glow);
    }

    .btn-danger {
      background: rgba(248, 113, 113, 0.1);
      color: var(--error);
      border: 1px solid rgba(248, 113, 113, 0.3);
    }

    .btn-danger:hover {
      background: rgba(248, 113, 113, 0.2);
      border-color: var(--error);
    }

    /* Main Container */
    .admin-content {
      flex: 1;
      max-width: 1180px;
      width: 100%;
      margin: 36px auto;
      padding: 0 24px;
    }

    .header-row {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      margin-bottom: 28px;
      flex-wrap: wrap;
      gap: 16px;
    }

    .page-heading h1 {
      font-size: 1.85rem;
      font-weight: 800;
      letter-spacing: -0.01em;
      margin-bottom: 6px;
    }

    .page-heading p {
      color: var(--text-muted);
      font-size: 0.95rem;
    }

    .stats-card {
      background: var(--bg-card);
      border: 1px solid var(--border-glow);
      backdrop-filter: blur(16px);
      padding: 12px 24px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      gap: 16px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    .stats-num {
      font-size: 1.9rem;
      font-weight: 800;
      color: var(--cyan);
      line-height: 1;
    }

    .stats-label {
      font-size: 0.8rem;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.06em;
    }

    /* Table Container */
    .table-container {
      background: var(--bg-card);
      border: 1px solid var(--border-glow);
      backdrop-filter: blur(18px);
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.45);
    }

    .table-header-info {
      padding: 18px 24px;
      border-bottom: 1px solid var(--border-subtle);
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: rgba(255, 255, 255, 0.02);
    }

    .table-title {
      font-size: 1rem;
      font-weight: 700;
      color: var(--text-main);
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .security-notice {
      font-size: 0.78rem;
      color: var(--gold);
      background: rgba(212, 175, 55, 0.08);
      border: 1px solid rgba(212, 175, 55, 0.25);
      padding: 5px 12px;
      border-radius: 6px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
    }

    thead th {
      padding: 14px 24px;
      background: rgba(9, 16, 34, 0.95);
      color: var(--text-dim);
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      border-bottom: 1px solid var(--border-subtle);
    }

    tbody td {
      padding: 16px 24px;
      font-size: 0.92rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.04);
      color: var(--text-main);
    }

    tbody tr:last-child td {
      border-bottom: none;
    }

    tbody tr:hover {
      background: rgba(0, 240, 255, 0.035);
    }

    .user-cell {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .row-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: rgba(0, 240, 255, 0.15);
      color: var(--cyan);
      border: 1px solid rgba(0, 240, 255, 0.3);
      font-weight: 700;
      font-size: 0.8rem;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .user-name {
      font-weight: 600;
    }

    .id-tag {
      font-family: monospace;
      font-size: 0.82rem;
      color: var(--cyan);
      background: rgba(0, 240, 255, 0.08);
      padding: 2px 8px;
      border-radius: 4px;
      border: 1px solid rgba(0, 240, 255, 0.2);
    }

    .email-text {
      color: var(--text-muted);
    }

    .date-text {
      color: var(--text-dim);
      font-size: 0.85rem;
    }

    .empty-state {
      padding: 48px;
      text-align: center;
      color: var(--text-muted);
    }

    /* Footer */
    .admin-footer {
      text-align: center;
      padding: 24px;
      color: var(--text-dim);
      font-size: 0.82rem;
      border-top: 1px solid var(--border-subtle);
      margin-top: auto;
    }
  </style>
</head>
<body>

  <!-- Top Navigation Bar -->
  <header class="admin-nav">
    <div class="brand-section">
      <div class="brand-title">DATA<span>•</span>LENS</div>
      <span class="badge-admin">Admin Directory</span>
    </div>
    <div class="nav-actions">
      <div class="user-pill">
        <div class="user-avatar">
          <?php echo strtoupper(substr($currentUserName, 0, 1)); ?>
        </div>
        <span><?php echo htmlspecialchars($currentUserName); ?></span>
      </div>
      <a href="../index.php" class="btn btn-outline">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Return to DataLens
      </a>
      <a href="../auth.php?action=logout" class="btn btn-danger">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Sign Out
      </a>
    </div>
  </header>

  <!-- Main Content -->
  <main class="admin-content">
    <div class="header-row">
      <div class="page-heading">
        <h1>Registered Users Directory</h1>
        <p>Real-time authenticated users recorded in XAMPP MySQL database <code>datalens</code>.</p>
      </div>
      <div class="stats-card">
        <div class="stats-num"><?php echo count($users); ?></div>
        <div class="stats-label">Total<br>Accounts</div>
      </div>
    </div>

    <?php if (!empty($error)): ?>
      <div style="background: rgba(248, 113, 113, 0.12); border: 1px solid var(--error); color: var(--error); padding: 14px 20px; border-radius: 8px; margin-bottom: 24px;">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <div class="table-container">
      <div class="table-header-info">
        <div class="table-title">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--cyan)" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          User Accounts
        </div>
        <div class="security-notice">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          Protected View: Passwords and hashes are strictly hidden
        </div>
      </div>

      <table>
        <thead>
          <tr>
            <th style="width: 80px;">ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Created At</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
            <tr>
              <td colspan="4" class="empty-state">No users registered yet.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($users as $u): ?>
              <?php
                $initials = '';
                $parts = explode(' ', trim($u['full_name']));
                foreach ($parts as $p) {
                    if (!empty($p)) $initials .= strtoupper($p[0]);
                }
                $initials = substr($initials, 0, 2) ?: 'U';
              ?>
              <tr>
                <td><span class="id-tag">#<?php echo (int)$u['id']; ?></span></td>
                <td>
                  <div class="user-cell">
                    <div class="row-avatar"><?php echo htmlspecialchars($initials); ?></div>
                    <span class="user-name"><?php echo htmlspecialchars($u['full_name']); ?></span>
                  </div>
                </td>
                <td><span class="email-text"><?php echo htmlspecialchars($u['email']); ?></span></td>
                <td><span class="date-text"><?php echo htmlspecialchars($u['created_at']); ?></span></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>

  <footer class="admin-footer">
    DataLens &copy; <?php echo date('Y'); ?> &bull; SEE DATA. UNDERSTAND BETTER. DECIDE SMARTER.
  </footer>

</body>
</html>
