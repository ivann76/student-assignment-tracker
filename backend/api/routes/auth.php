<?php
// POST register, POST login

if ($route === 'register' && $method === 'POST') {
  $name = trim($in['name'] ?? '');
  $email = trim($in['email'] ?? '');
  $password = $in['password'] ?? '';
  if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
    out(['error' => 'Invalid input'], 422);
  }
  $stmt = $pdo->prepare('SELECT id FROM users WHERE email=?');
  $stmt->execute([$email]);
  if ($stmt->fetch()) out(['error' => 'Email already registered'], 409);

  $pdo->prepare('INSERT INTO users (name,email,password) VALUES (?,?,?)')
      ->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
  $userId = (int)$pdo->lastInsertId();
  out([
    'token' => newToken($pdo, $userId),
    'user' => ['id' => $userId, 'name' => $name, 'email' => $email, 'role' => 'student']
  ], 201);
}

if ($route === 'login' && $method === 'POST') {
  $stmt = $pdo->prepare('SELECT * FROM users WHERE email=?');
  $stmt->execute([$in['email'] ?? '']);
  $user = $stmt->fetch();
  if (!$user || !password_verify($in['password'] ?? '', $user['password'])) {
    out(['error' => 'Wrong email or password'], 401);
  }
  out([
    'token' => newToken($pdo, $user['id']),
    'user' => ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']]
  ]);
}
