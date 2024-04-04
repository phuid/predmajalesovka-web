<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  <form action="run_sql.php">
    <input type="text" name="sql" id="sql">
    <input type="submit" value="Odeslat">
  </form>

  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_COOKIE['password'])) {
      $cookie_password = $_COOKIE['password'];
      $cookie_password = preg_replace('/[^0-9A-Za-z]/', '', $cookie_password);

      $config = parse_ini_file('config.ini');

      $sql_servername = $config['sql_servername'];
      $sql_username = $config['sql_username'];
      $sql_password = $config['sql_password'];

      try {
        $conn = new PDO("mysql:host=$sql_servername;dbname=predmajalesova_hra", $sql_username, $sql_password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT * FROM teams WHERE password = :password");
        $stmt->bindParam(':password', $cookie_password, PDO::PARAM_STR, 255);
        $stmt->execute();

        $row = $stmt->fetch();
        if ($row === false) {
          echo "unauthorised <br>";
          http_response_code(401);
        } else {
          $team_id = $row['id'];
          $team_name = $row['name'];

          if ($team_id == 1 && $team_name == "admin") {

            if (!isset($_GET['sql'])) {
              echo "Missing sql parameter";
              http_response_code(400);
              exit();
            }

            $sql = $_GET['sql'];

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();

            echo "<textarea style=\"width: 100vh; height calc(100vh - 10rem);\">";
            echo json_encode($result);
            echo "</textarea>";
          } else {
            echo "unauthorised <br>";
            http_response_code(403);
          }
        }
      } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit();
        http_response_code(500);
      }
    } else {
      http_response_code(401);
    }
  } else {
    http_response_code(405);
  }
  ?>

</body>

</html>