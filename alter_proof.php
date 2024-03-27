<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (isset($_COOKIE['password'])) {
    $cookie_password = $_COOKIE['password'];
    $cookie_password = preg_replace('/[^0-9A-Za-z]/', '', $cookie_password);

    $sql_servername = "localhost";
    $sql_username = "root";
    $sql_password = "";

    try {
      $conn = new PDO("mysql:host=$sql_servername;dbname=predmajalesova_hra", $sql_username, $sql_password);
      // set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $stmt = $conn->prepare("SELECT * FROM teams WHERE password = :password");
      $stmt->bindParam(':password', $cookie_password, PDO::PARAM_STR, 255);
      $stmt->execute();

      $row = $stmt->fetch();
      if ($row === false) {
        http_response_code(401);
      } else {
        $team_id = $row['id'];
        $team_name = $row['name'];

        $stmt = $conn->prepare("SELECT * FROM proofs WHERE id = :id");
        $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $stmt->execute();

        $proof = $stmt->fetch();
        if ($proof !== false && ($proof['team_id'] == $team_id || $team_name == "admin")) {
          $action = $_GET['action'] . "d";
          if (($action == "verified" && $team_name == "admin") || $action == "deleted") {
            $value = 1;
            if ($proof[$action] == 1) {
              $value = 0;
            }

            $stmt = $conn->prepare("UPDATE proofs SET " . $action . " = :value WHERE id = :id");
            $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
            $stmt->bindParam(':value', $value, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $conn->prepare("SELECT * FROM proofs WHERE id = :id");
            $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
            $stmt->execute();

            $check = $stmt->fetch();
            if ($check !== false) {
              if ($check[$action] == $value) {
                echo $action . "=" . $value;
                http_response_code(200);
              } else {
                echo "Failed to update proof, check failed";
                foreach ($check as $key => $value) {
                  echo $key . "=" . $value . "\n";
                }
                http_response_code(500);
              }
            } else {
              echo "Failed to update proof, check failed";
              http_response_code(500);
            }
          } else if ($action == "verified") {
            echo "Unauthorized action - only admin can verify proof";
            http_response_code(400);
          } else {
            echo "Invalid action";
            http_response_code(400);
          }
        } else {
          echo "Unauthorized";
          http_response_code(403);
        }
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
