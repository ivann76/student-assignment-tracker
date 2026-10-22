<?php
// GET list / GET one / POST / PUT / DELETE assignments

$user = currentUser($pdo);

$select = 'SELECT a.*, u.code AS unit_code, u.name AS unit_name, us.name AS owner,
  (SELECT COUNT(*) FROM likes l WHERE l.assignment_id=a.id) AS likes,
  (SELECT COUNT(*) FROM likes l WHERE l.assignment_id=a.id AND l.user_id=:me) AS liked
  FROM assignments a JOIN units u ON u.id=a.unit_id JOIN users us ON us.id=a.user_id';

if ($method === 'GET' && !$id) {
  if (!$user) { // guests only see a small public preview
    $stmt = $pdo->prepare($select . ' WHERE a.is_shared=1 ORDER BY a.created_at DESC LIMIT 3');
    $stmt->execute([':me' => 0]);
    out($stmt->fetchAll());
  }
  $stmt = $pdo->prepare($select . ' WHERE a.user_id=:me2 OR a.is_shared=1');
  $stmt->execute([':me' => $user['id'], ':me2' => $user['id']]);
  out($stmt->fetchAll());
}

$user = requireUser($pdo);

if ($method === 'GET') {
  $stmt = $pdo->prepare($select . ' WHERE a.id=:id AND (a.user_id=:me2 OR a.is_shared=1)');
  $stmt->execute([':me' => $user['id'], ':me2' => $user['id'], ':id' => $id]);
  $row = $stmt->fetch();
  $row ? out($row) : out(['error' => 'Not found'], 404);
}

if ($method === 'POST' || $method === 'PUT') {
  if (trim($in['title'] ?? '') === '' || empty($in['due_date']) || empty($in['unit_id'])) {
    out(['error' => 'Title, unit and due date are required'], 422);
  }
  $values = [
    $in['unit_id'], trim($in['title']), $in['description'] ?? '', $in['due_date'],
    $in['status'] ?? 'todo', $in['priority'] ?? 'medium', empty($in['is_shared']) ? 0 : 1
  ];
  if ($method === 'POST') {
    $pdo->prepare('INSERT INTO assignments (unit_id,title,description,due_date,status,priority,is_shared,user_id) VALUES (?,?,?,?,?,?,?,?)')
        ->execute([...$values, $user['id']]);
    out(['id' => (int)$pdo->lastInsertId()], 201);
  }
  // Only the owner or an admin may update
  $pdo->prepare('UPDATE assignments SET unit_id=?,title=?,description=?,due_date=?,status=?,priority=?,is_shared=? WHERE id=? AND (user_id=? OR ?="admin")')
      ->execute([...$values, $id, $user['id'], $user['role']]);
  out(['ok' => true]);
}

if ($method === 'DELETE') {
  $pdo->prepare('DELETE FROM assignments WHERE id=? AND (user_id=? OR ?="admin")')
      ->execute([$id, $user['id'], $user['role']]);
  out(['ok' => true]);
}
