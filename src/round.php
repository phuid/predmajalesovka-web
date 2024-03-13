<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (isset($_GET['round_id'])) {
    $round_id = $_GET['round_id'];

    $servername = "localhost";
    $username = "root";
    $password = "";

    try {
      $conn = new PDO("mysql:host=$servername;dbname=predmajalesova_hra", $username, $password);
      // set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $stmt = $conn->prepare("SELECT * FROM rounds WHERE id = :round_id");
      $stmt->bindParam(':round_id', $round_id, PDO::PARAM_INT);
      $stmt->execute();

      $row = $stmt->fetch();
      if ($row === false) {
        echo "<h1>No round with id $round_id found!</h1>";
        echo "if you are an admin, you can create a new round with id $round_id <a onclick=\"fetch('add_round.php', {method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: 'round_id=$round_id'})\">here</a>";
      } else {
        echo "Round number: " . $row['id'] . "<br>";
        echo "Round name: " . $row['nickname'] . "<br>";
        echo "Round start: " . $row['start_time'] . "<br>";
        echo "Round end: " . $row['end_time'] . "<br>";
        echo "Round hint_folder: " . $row['hint_folder'] . "<br>";
        echo "Round category: " . $row['category'] . "<br>";
      }
    } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
    }

    $conn = null;
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