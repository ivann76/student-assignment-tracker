<?php
// POST like: toggles the current user's like on assignment {id}

if ($method === 'POST') {
  $user = requireUser($pdo);
  $stmt = $pdo->prepare('DELETE FROM likes WHERE user_id=? AND assignment_id=?');
  $stmt->execute([$user['id'], $id]);
  if (!$stmt->rowCount()) {
    $pdo->prepare('INSERT INTO likes (user_id,assignment_id) VALUES (?,?)')->execute([$user['id'], $id]);
  }
  out(['ok' => true]);
}
