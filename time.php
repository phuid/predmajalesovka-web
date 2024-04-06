<?php
$config = parse_ini_file('config.ini');

$sql_servername = $config['sql_servername'];
$sql_username = $config['sql_username'];
$sql_password = $config['sql_password'];

try {
  $conn = new PDO("mysql:host=$sql_servername;dbname=predmajalesova_hra", $sql_username, $sql_password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $conn->prepare("SELECT NOW()");
  $stmt->execute();

  $row = $stmt->fetch();
  if ($row === false) {
    echo "sql time: error";
  }
  else {
    echo "sql time:" . $row[0];
  }
  echo "<br>";
  echo "php time:" . date("Y-m-d H:i:s");
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
  exit();
  http_response_code(500);
}
