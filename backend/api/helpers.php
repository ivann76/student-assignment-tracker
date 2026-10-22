<?php
// Small helpers shared by every route file.

function out($data, $code = 200) {
  http_response_code($code);
  echo json_encode($data);
  exit;
}

// Returns the logged-in user (from the X-Token header) or null
function currentUser($pdo) {
  $token = $_SERVER['HTTP_X_TOKEN'] ?? '';
  if (!$token) return null;
  $stmt = $pdo->prepare('SELECT id,name,email,role FROM users WHERE token=?');
  $stmt->execute([$token]);
  return $stmt->fetch() ?: null;
}

function requireUser($pdo) {
  $user = currentUser($pdo);
  if (!$user) out(['error' => 'Please log in'], 401);
  return $user;
}

function requireAdmin($pdo) {
  $user = requireUser($pdo);
  if ($user['role'] !== 'admin') out(['error' => 'Admins only'], 403);
  return $user;
}

function newToken($pdo, $userId) {
  $token = bin2hex(random_bytes(24));
  $pdo->prepare('UPDATE users SET token=? WHERE id=?')->execute([$token, $userId]);
  return $token;
}
