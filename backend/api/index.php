<?php
// Entry point: sets headers, reads the request, then hands it to the matching route file.
require 'config.php';
require 'helpers.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, X-Token');
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'OPTIONS') exit;

$route = $_GET['route'] ?? '';
$id = (int)($_GET['id'] ?? 0);
$in = json_decode(file_get_contents('php://input'), true) ?? [];

// Which file handles which route
$routes = [
  'register' => 'auth',
  'login' => 'auth',
  'units' => 'units',
  'assignments' => 'assignments',
  'like' => 'likes',
  'comments' => 'comments',
];

try {
  if (!isset($routes[$route])) out(['error' => 'Unknown route'], 404);
  require __DIR__ . '/routes/' . $routes[$route] . '.php';
  out(['error' => 'Method not allowed'], 405);
} catch (Throwable $e) {
  out(['error' => 'Server error'], 500);
}
