<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (isset($_GET['round_num'])) {
    $round_num = $_GET['round_num'];

    $servername = "localhost";
    $username = "root";
    $password = "";

    try {
      $conn = new PDO("mysql:host=$servername;dbname=predmajalesova_hra", $username, $password);
      // set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $stmt = $conn->prepare("SELECT * FROM rounds WHERE round_num = :round_num");
      $stmt->bindParam(':round_num', $round_num, PDO::PARAM_INT);
      $stmt->execute();

      while (true) {
        $row = $stmt->fetch();
        if ($row === false) {
          echo "<h1>No round found!</h1>";
          break;
        }

        echo "Round number: " . $row['round_num'] . "<br>";
        echo "Round name: " . $row['name'] . "<br>";
        echo "Round start: " . $row['start-time'] . "<br>";
        echo "Round hint-folder: " . $row['hint-folder'] . "<br>";
      }
    } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
    }

    $conn = null;

    echo "To unsubscribe: visit <a href=\"remove_email.php?email=" . $email . "\">this link</a>";
  } else {
    echo "round field is missing";
  }
} else {
  echo "Invalid request method";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kolo</title>
</head>
<body>
  
</body>
</html>