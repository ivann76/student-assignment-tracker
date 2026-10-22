<?php
// GET units (everyone), POST/PUT/DELETE units (admin only)

if ($method === 'GET') {
  out($pdo->query('SELECT * FROM units ORDER BY code')->fetchAll());
}

requireAdmin($pdo);
$code = trim($in['code'] ?? '');
$name = trim($in['name'] ?? '');
if ($method !== 'DELETE' && ($code === '' || $name === '')) {
  out(['error' => 'Code and name are required'], 422);
}

if ($method === 'POST') {
  $pdo->prepare('INSERT INTO units (code,name) VALUES (?,?)')->execute([$code, $name]);
  out(['id' => (int)$pdo->lastInsertId()], 201);
}

if ($method === 'PUT') {
  $pdo->prepare('UPDATE units SET code=?, name=? WHERE id=?')->execute([$code, $name, $id]);
  out(['ok' => true]);
}

if ($method === 'DELETE') {
  $stmt = $pdo->prepare('SELECT COUNT(*) AS n FROM assignments WHERE unit_id=?');
  $stmt->execute([$id]);
  if ($stmt->fetch()['n'] > 0) out(['error' => 'This unit still has assignments'], 409);
  $pdo->prepare('DELETE FROM units WHERE id=?')->execute([$id]);
  out(['ok' => true]);
}
