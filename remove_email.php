<html>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (isset($_GET['email'])) {
    $email = $_GET['email'];
    // Do something with the email, such as saving it to a database or sending an email
    // ...
    echo "Email received: " . $email . "<br>";

    $config = parse_ini_file('config.ini');

    $sql_servername = $config['sql_servername'];
    $sql_username = $config['sql_username'];
    $sql_password = $config['sql_password'];

    try {
      $conn = new PDO("mysql:host=$sql_servername;dbname=predmajalesova_hra", $sql_username, $sql_password);
      // set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $stmt = $conn->prepare("DELETE FROM emails WHERE email = :email");
      $stmt->bindParam(':email', $email, PDO::PARAM_STR, 255);
      $stmt->execute();

      $stmt = $conn->prepare("SELECT COUNT(*) FROM emails WHERE email = :email");
      $stmt->bindParam(':email', $email, PDO::PARAM_STR, 255);
      $stmt->execute();
      $result = $stmt->fetchColumn();
      if ($result > 0) {
        echo "Email not unsubscibed (there was a problem on our side, contact xhrud@gyrec.cz to resolve the issue) <br>";
      } else {
        echo "Email unsubscibed succesfully <br>";
      }
    } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
    }

    $conn = null;
  } else {
    echo "Email field is missing";
  }
} else {
  echo "Invalid request method";
}
?>

</html>