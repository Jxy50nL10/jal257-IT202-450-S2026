<?php
require_once(__DIR__ . "/../../../lib/db.php");

$db = getDB();
// query is OK here because no user input is inside the SQL.
$stmt = $db->query(
    "SELECT id, email, modified, created
     FROM Users
     ORDER BY created DESC
     LIMIT 10"
);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php if (count($users) === 0): ?>
  <p>No users yet.</p>
<?php else: ?>
  <ul>
    <?php foreach ($users as $user): ?>
      <li>
        <!-- Escape database values before showing them in HTML. -->
        <?php echo htmlspecialchars($user["email"]); ?>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
<?php
try {
    $db = getDB();
    $stmt = $db->query("SELECT id, email FROM Users LIMIT 10");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $users = [];
    $pageError = "Database is unavailable right now.";
}
