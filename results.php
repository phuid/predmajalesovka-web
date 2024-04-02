<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Výsledky předmajálesové hry</title>
  <link rel="stylesheet" href="basicstyles.css">
</head>

<body>
  <h1>Předmajálesová hra</h1>
  <h4><a href="index.php">zpět na úvod</a></h4>
  <h2>Výsledky</h2>

  <?php
  $config = parse_ini_file('config.ini');

  $sql_servername = $config['sql_servername'];
  $sql_username = $config['sql_username'];
  $sql_password = $config['sql_password'];

  try {
    $conn = new PDO("mysql:host=$sql_servername;dbname=predmajalesova_hra", $sql_username, $sql_password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
    http_response_code(500);
  }

  $stmt = $conn->prepare("SELECT * FROM rounds ORDER BY id ASC");
  $stmt->execute();

  $rounds = $stmt->fetchAll();

  foreach ($rounds as $round) {
    echo "<h3>Kolo " . $round['id'] . "</h3>";
  }
  ?>
</body>

</html>