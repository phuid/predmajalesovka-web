<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

        if ($team_id == 1 && $team_name == "admin") {
          //check if all arguments (nickname, category_higher, category_lower, end) are set
          $missing_arguments = false;
          if (!isset($_FILES["new-hint-img"])) {
            echo "Missing argument: new-hint-img";
            $missing_arguments = true;
          }
          if (!isset($_POST['round_id'])) {
            echo "Missing argument: round_id";
            $missing_arguments = true;
          }

          if ($missing_arguments) {
            http_response_code(400);
          } else {
            $round_id = $_POST['round_id'];

            // Check if image file is a actual image or fake image
            if ($_FILES["new-hint-img"]["error"] === 0 && $_FILES["new-hint-img"]["tmp_name"] != "") {
              $check = getimagesize($_FILES["new-hint-img"]["tmp_name"]);
              if ($check != false) {

                $stmt = $conn->prepare("SELECT * FROM rounds WHERE id = :id");
                $stmt->bindParam(':id', $round_id, PDO::PARAM_INT);
                $stmt->execute();
                $target_dir = $stmt->fetch()['hint_folder'];

                if ($target_dir == "") {
                  echo "Error: could not find a suitable hints dir";
                  http_response_code(404);
                } else {
                  $target_file = $target_dir . basename($_FILES["new-hint-img"]["name"]);
                  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                  $info = pathinfo($_FILES['new-hint-img']['name']);
                  $basename = $info['basename']; // get the extension of the file
                  $target = $target_dir . "/" . $basename;

                  move_uploaded_file($_FILES['new-hint-img']['tmp_name'], $target);

                  $stmt = $conn->prepare("SELECT * FROM rounds WHERE id = :id");
                  $stmt->bindParam(':id', $round_id, PDO::PARAM_INT);
                  $stmt->execute();

                  $result = $stmt->fetch();
                  if ($result !== false) {
                    $hint_files = scandir($target_dir);

                    usort($hint_files, function ($a, $b) use ($target_dir) {
                      $fileA = $target_dir . '/' . $a;
                      $fileB = $target_dir . '/' . $b;
                      return filemtime($fileA) - filemtime($fileB);
                    });

                    $stmt = $conn->prepare("SELECT email FROM emails");
                    $stmt->execute();

                    $result = $stmt->fetch();
                    while ($result !== false) {
                      $to = $result['email'];
                      $subject = "Nové kolo Předmajálesové hry - $nickname";

                      $mail_body = "<!DOCTYPE html><html lang='en' style='font-family: Verdana, sans-serif; background: hsl(192, 15%, 10%); color: white;'><body style='font-family: Verdana, sans-serif; background: hsl(192, 15%, 10%); color: white;'>  <h1 style='text-shadow: 2px 1px hsl(302, 100%, 66%), -2px -1px hsl(182, 98%, 23%); font-size: 3rem;'>Nové kolo předmajálesové hry se jmenuje $nickname</h1>  <h3>Stačí najít místo na obrázku, navštívit ho a vyfotit samolepku s logem majálesu do $end. To přece zvládne každý!!!  </h3>  <h3>Podrobnosti: <a href='https://majales.gyrec.cz/predmajalesovka/round.php?round=$new_round_id' style='transition: text-shadow 0.1s ease; color: hsl(179, 100%, 50%);'>https://majales.gyrec.cz/predmajalesovka/round.php?round=$new_round_id</a>  </h3>  <h3>PoZnávÁte tOTo MÍSto???????:</h3>";
                      foreach ($hint_files as $file) {
                        $mail_body .= "<img src='https://majales.gyrec.cz/predmajalesovka/round.php?round=$new_round_id' style='transition: text-shadow 0.1s ease; color: hsl(179, 100%, 50%);'>https://majales.gyrec.cz/predmajalesovka/hints/$target_dir/$file'>";
                      }
                      $mail_body .= "<h5>Pokud chcete odhlásit příjem těchto emailů, klikněte <a href='https://majales.gyrec.cz/predmajalesovka/remove_email.php?email=$to' style='transition: text-shadow 0.1s ease; color: hsl(179, 100%, 50%);'>sem</a></h5></body></html>";

                      mail($to, "Nové kolo předmajálesovky se jmenuje $nickname", $mail_body);

                      $result = $stmt->fetch();
                    }

                    echo "round.php?round=$new_round_id";
                    http_response_code(200);
                  } else {
                    echo "Round creation failed";
                    http_response_code(500);
                  }
                }
              } else {
                echo "File upload failed";
                http_response_code(500);
              }
            } else {
              echo "File not uploaded";
              http_response_code(500);
            }
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
