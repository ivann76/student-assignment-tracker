<?php
require 'config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, X-Token');
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'OPTIONS') exit;
$route = $_GET['route'] ?? '';
$id = (int)($_GET['id'] ?? 0);
$in = json_decode(file_get_contents('php://input'), true) ?? [];

function out($data, $code = 200) { http_response_code($code); echo json_encode($data); exit; }
function currentUser($pdo) {
  $t = $_SERVER['HTTP_X_TOKEN'] ?? '';
  if (!$t) return null;
  $s = $pdo->prepare('SELECT id,name,email,role FROM users WHERE token=?');
  $s->execute([$t]);
  return $s->fetch() ?: null;
}
function requireUser($pdo) { $u = currentUser($pdo); if (!$u) out(['error' => 'Please log in'], 401); return $u; }
function newToken($pdo, $uid) {
  $t = bin2hex(random_bytes(24));
  $pdo->prepare('UPDATE users SET token=? WHERE id=?')->execute([$t, $uid]);
  return $t;
}

try {
  if ($route === 'register' && $method === 'POST') {
    $name = trim($in['name'] ?? ''); $email = trim($in['email'] ?? ''); $pw = $in['password'] ?? '';
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pw) < 8) out(['error' => 'Invalid input'], 422);
    $s = $pdo->prepare('SELECT id FROM users WHERE email=?'); $s->execute([$email]);
    if ($s->fetch()) out(['error' => 'Email already registered'], 409);
    $pdo->prepare('INSERT INTO users (name,email,password) VALUES (?,?,?)')->execute([$name, $email, password_hash($pw, PASSWORD_DEFAULT)]);
    $uid = (int)$pdo->lastInsertId();
    out(['token' => newToken($pdo, $uid), 'user' => ['id' => $uid, 'name' => $name, 'email' => $email, 'role' => 'student']], 201);
  }
  if ($route === 'login' && $method === 'POST') {
    $s = $pdo->prepare('SELECT * FROM users WHERE email=?'); $s->execute([$in['email'] ?? '']);
    $u = $s->fetch();
    if (!$u || !password_verify($in['password'] ?? '', $u['password'])) out(['error' => 'Wrong email or password'], 401);
    out(['token' => newToken($pdo, $u['id']), 'user' => ['id' => $u['id'], 'name' => $u['name'], 'email' => $u['email'], 'role' => $u['role']]]);
  }
  if ($route === 'units') out($pdo->query('SELECT * FROM units ORDER BY code')->fetchAll());

  $user = currentUser($pdo);
  $select = 'SELECT a.*, u.code AS unit_code, u.name AS unit_name, us.name AS owner,
    (SELECT COUNT(*) FROM likes l WHERE l.assignment_id=a.id) AS likes,
    (SELECT COUNT(*) FROM likes l WHERE l.assignment_id=a.id AND l.user_id=:me) AS liked
    FROM assignments a JOIN units u ON u.id=a.unit_id JOIN users us ON us.id=a.user_id';

  if ($route === 'assignments') {
    if ($method === 'GET' && !$id) {
      if (!$user) { // guests only see a small public preview
        $s = $pdo->prepare($select . ' WHERE a.is_shared=1 ORDER BY a.created_at DESC LIMIT 3'); $s->execute([':me' => 0]);
        out($s->fetchAll());
      }
      $s = $pdo->prepare($select . ' WHERE a.user_id=:me2 OR a.is_shared=1');
      $s->execute([':me' => $user['id'], ':me2' => $user['id']]);
      out($s->fetchAll());
    }
    if ($method === 'GET') {
      $user = requireUser($pdo);
      $s = $pdo->prepare($select . ' WHERE a.id=:id AND (a.user_id=:me2 OR a.is_shared=1)');
      $s->execute([':me' => $user['id'], ':me2' => $user['id'], ':id' => $id]);
      $row = $s->fetch(); $row ? out($row) : out(['error' => 'Not found'], 404);
    }
    $user = requireUser($pdo);
    if ($method === 'POST' || $method === 'PUT') {
      if (trim($in['title'] ?? '') === '' || empty($in['due_date']) || empty($in['unit_id'])) out(['error' => 'Title, unit and due date are required'], 422);
      $v = [$in['unit_id'], trim($in['title']), $in['description'] ?? '', $in['due_date'], $in['status'] ?? 'todo', $in['priority'] ?? 'medium', empty($in['is_shared']) ? 0 : 1];
      if ($method === 'POST') {
        $pdo->prepare('INSERT INTO assignments (unit_id,title,description,due_date,status,priority,is_shared,user_id) VALUES (?,?,?,?,?,?,?,?)')->execute([...$v, $user['id']]);
        out(['id' => (int)$pdo->lastInsertId()], 201);
      }
      $s = $pdo->prepare('UPDATE assignments SET unit_id=?,title=?,description=?,due_date=?,status=?,priority=?,is_shared=? WHERE id=? AND (user_id=? OR ?="admin")');
      $s->execute([...$v, $id, $user['id'], $user['role']]);
      out(['ok' => true]);
    }
    if ($method === 'DELETE') {
      $pdo->prepare('DELETE FROM assignments WHERE id=? AND (user_id=? OR ?="admin")')->execute([$id, $user['id'], $user['role']]);
      out(['ok' => true]);
    }
  }
  if ($route === 'like' && $method === 'POST') {
    $user = requireUser($pdo);
    $s = $pdo->prepare('DELETE FROM likes WHERE user_id=? AND assignment_id=?'); $s->execute([$user['id'], $id]);
    if (!$s->rowCount()) $pdo->prepare('INSERT INTO likes (user_id,assignment_id) VALUES (?,?)')->execute([$user['id'], $id]);
    out(['ok' => true]);
  }
  if ($route === 'comments') {
    $user = requireUser($pdo);
    if ($method === 'POST') {
      $body = trim($in['body'] ?? '');
      if ($body === '' || strlen($body) > 500) out(['error' => 'Comment must be 1-500 characters'], 422);
      $pdo->prepare('INSERT INTO comments (assignment_id,user_id,body) VALUES (?,?,?)')->execute([$id, $user['id'], $body]);
      out(['ok' => true], 201);
    }
    $s = $pdo->prepare('SELECT c.*, u.name FROM comments c JOIN users u ON u.id=c.user_id WHERE assignment_id=? ORDER BY c.created_at DESC');
    $s->execute([$id]); out($s->fetchAll());
  }
  out(['error' => 'Unknown route'], 404);
} catch (Throwable $e) { out(['error' => 'Server error'], 500); }
