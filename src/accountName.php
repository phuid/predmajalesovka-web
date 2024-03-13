<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_COOKIE['password'])) {
    $cookie_password = $_COOKIE['password'];
    $cookie_password = preg_replace('/[^0-9A-Za-z]/', '', $cookie_password);

    $servername = "localhost";
    $username = "root";
    $password = "";

    try {
      $conn = new PDO("mysql:host=$servername;dbname=predmajalesova_hra", $username, $password);
      // set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $stmt = $conn->prepare("SELECT * FROM teams WHERE password = :password");
      $stmt->bindParam(':password', $cookie_password, PDO::PARAM_STR, 255);
      $stmt->execute();

      $row = $stmt->fetch();
      if ($row === false) {
        http_response_code(404);
      } else {
        $team_id = $row['id'];
        $team_name = $row['name'];
        echo $team_name;
        http_response_code(200);
      }
    } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
      http_response_code(500);
    }

    $conn = null;
  } else {
    echo "password cookie is missing";
    http_response_code(400);
  }
} else {
  echo "Invalid request method";
  http_response_code(400);
}
