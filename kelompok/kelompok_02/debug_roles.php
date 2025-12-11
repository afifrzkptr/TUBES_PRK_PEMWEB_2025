<?php
require_once 'config/db.php';
$conn = connect_db();

echo "<h1>Debug Info</h1>";

// Check Roles
echo "<h2>Roles Table</h2>";
$roles = $conn->query("SELECT * FROM roles");
echo "<table border='1'><tr><th>ID</th><th>Name</th></tr>";
while ($r = $roles->fetch_assoc()) {
    echo "<tr><td>{$r['id_role']}</td><td>{$r['role_name']}</td></tr>";
}
echo "</table>";

// Check Users
echo "<h2>Users Table (Joined)</h2>";
$users = $conn->query("
    SELECT u.id_user, u.username, u.email, u.id_role, r.role_name 
    FROM users u 
    LEFT JOIN roles r ON u.id_role = r.id_role
");
echo "<table border='1'><tr><th>ID</th><th>Username</th><th>Email</th><th>Role ID</th><th>Role Name</th></tr>";
while ($u = $users->fetch_assoc()) {
    echo "<tr>
        <td>{$u['id_user']}</td>
        <td>{$u['username']}</td>
        <td>{$u['email']}</td>
        <td>{$u['id_role']}</td>
        <td>{$u['role_name']}</td>
    </tr>";
}
echo "</table>";

// Check Session (if logged in)
session_start();
echo "<h2>Current Session</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>