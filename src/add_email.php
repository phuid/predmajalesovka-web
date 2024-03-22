<html>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (isset($_GET['email'])) {
    $email = $_GET['email'];
    // Do something with the email, such as saving it to a database or sending an email
    // ...
    echo "Email received: " . $email . "<br>";

    $sql_servername = "localhost";
    $sql_username = "root";
    $sql_password = "";

    try {
      $conn = new PDO("mysql:host=$sql_servername;dbname=predmajalesova_hra", $sql_username, $sql_password);
      // set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $stmt = $conn->prepare("SELECT COUNT(*) FROM emails WHERE email = :email");
      $stmt->bindParam(':email', $email, PDO::PARAM_STR, 255);
      $stmt->execute();
      $result = $stmt->fetchColumn();
      if ($result > 0) {
        echo "Email already registered <br>";
        echo "To unsubscribe: visit <a href=\"remove_email.php?email=" . $email . "\">this link</a>";
        return;
      }

      $stmt = $conn->prepare("INSERT INTO emails(email) VALUES (:email)");
      $stmt->bindParam(':email', $email, PDO::PARAM_STR, 255);
      $stmt->execute();

      $stmt = $conn->prepare("SELECT COUNT(*) FROM emails WHERE email = :email");
      $stmt->bindParam(':email', $email, PDO::PARAM_STR, 255);
      $stmt->execute();
      $result = $stmt->fetchColumn();
      if ($result > 0) {
        echo "Email registered succesfully <br>";
      }
    } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
    }

    $conn = null;

    echo "To unsubscribe: visit <a href=\"remove_email.php?email=" . $email . "\">this link</a>";
  } else {
    echo "Email field is missing";
  }
} else {
  echo "Invalid request method";
}
?>

</html>