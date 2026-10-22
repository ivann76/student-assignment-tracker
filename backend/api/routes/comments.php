<?php
// GET comments of assignment {id}, POST a comment, DELETE comment {id}

$user = requireUser($pdo);

if ($method === 'POST') {
  $body = trim($in['body'] ?? '');
  if ($body === '' || strlen($body) > 500) out(['error' => 'Comment must be 1-500 characters'], 422);
  $pdo->prepare('INSERT INTO comments (assignment_id,user_id,body) VALUES (?,?,?)')->execute([$id, $user['id'], $body]);
  out(['ok' => true], 201);
}

if ($method === 'DELETE') { // here id is the comment id; only the author or an admin may delete
  $pdo->prepare('DELETE FROM comments WHERE id=? AND (user_id=? OR ?="admin")')->execute([$id, $user['id'], $user['role']]);
  out(['ok' => true]);
}

if ($method === 'GET') {
  $stmt = $pdo->prepare('SELECT c.*, u.name FROM comments c JOIN users u ON u.id=c.user_id WHERE assignment_id=? ORDER BY c.created_at DESC');
  $stmt->execute([$id]);
  out($stmt->fetchAll());
}
