<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (!isset($_GET['round_id'])) {
    echo "Missing round_id parameter";
    http_response_code(400);
    exit();
  }
  $config = parse_ini_file('config.ini');

  $sql_servername = $config['sql_servername'];
  $sql_username = $config['sql_username'];
  $sql_password = $config['sql_password'];

  $conn = new PDO("mysql:host=$sql_servername;dbname=predmajalesova_hra", $sql_username, $sql_password);

  $stmt = $conn->prepare("SELECT name, teams.id as team_id, category, time, verified FROM proofs INNER JOIN teams ON teams.id = proofs.team_id WHERE round_id = :roundId AND deleted = false AND (verified IS NULL OR verified = true) GROUP BY team_id ORDER BY time ASC");
  $stmt->bindParam(':roundId', $_GET['round_id']);
  $stmt->execute();
  $proofs = $stmt->fetchAll();

  if ($proofs === false) {
    echo "No proofs found";
    http_response_code(404);
    exit();
  }

  echo json_encode($proofs);
}
