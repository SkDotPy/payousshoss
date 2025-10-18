<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

echo json_encode([
  'user_id'        => $_SESSION['user_id']        ?? null,
  'email'          => $_SESSION['email']          ?? null,
  'role'           => $_SESSION['role']           ?? null,
  'status'         => $_SESSION['status']         ?? null,
  'nom'            => $_SESSION['nom']            ?? null,
  'age'            => $_SESSION['age']            ?? null,
  'is_super_admin' => $_SESSION['is_super_admin'] ?? null,
  'session_id'     => session_id(),
]);
