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

  <script>
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

    $result = $stmt->fetch();

    echo "let rounds = [";
    while ($result != false) {
      echo $result['id'] . ", ";
      $result = $stmt->fetch();
    }
    echo "];";
    ?>

    rounds.forEach(element => {
      fetch('get_results.php?round_id=' + element)
        .then(response => response.json())
        .then(data => {
          console.log(data);
        });
    });
  </script>
</body>

</html>