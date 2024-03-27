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
        $team_category = $row['category'];

        $round_id = null;

        //check if all arguments (nickname, category_higher, category_lower, end) are set
        $missing_arguments = false;
        if (!isset($_FILES["img"])) {
          echo "Missing argument: img";
          $missing_arguments = true;
        }
        if (!isset($_POST['round_id']) || $_POST['round_id'] == "") {
          echo "Missing argument: round_id";
          $missing_arguments = true;
        } else {
          $round_id = (int)$_POST['round_id'];
        }

        if ($missing_arguments) {
          http_response_code(400);
        } else {
          $stmt = $conn->prepare("SELECT * FROM rounds WHERE id = :round_id");
          $stmt->bindParam(':round_id', $round_id, PDO::PARAM_INT);
          $stmt->execute();
          $round = $stmt->fetch();
          if ($round == false) {
            echo "Could not find round with id $round_id";
            http_response_code(404);
          } else {
            if ($round['category'] != $team_category && $team_category != 3 && $round['category'] != 3) {
              echo "This round does not belong to your category";
              http_response_code(400);
            } else if (strtotime($round['end_time']) < strtotime(date("Y-m-d H:i:s"))) {
              echo "Čas pro odevzdání obrázků pro toto kolo vypršel, pokud máte dobrý důvot proč jste obrázek nedoručili včas, kontaktujte nás na instagramu <a href=\"https://www.instagram.com/majales.budoucnosti/\">@majales.budoucnosti</a>";
              http_response_code(400);
            } else {
              // Check if image file is a actual image or fake image
              if ($_FILES["img"]["error"] === 0 && $_FILES["img"]["tmp_name"] != "") {
                $check = getimagesize($_FILES["img"]["tmp_name"]);
                if ($check != false) {
                  $target_dir = "../proofs/round$round_id";

                  if (!is_dir($target_dir)) {
                    if (!file_exists($target_dir)) {
                      mkdir($target_dir);
                    } else {
                      echo "Error: proof dir exists, but isnt a dir";
                      $target_dir = "";
                    }
                  }

                  if ($target_dir != "") {
                    $target_dir = $target_dir . "/team" . $team_id;
                    if (!is_dir($target_dir)) {
                      if (!file_exists($target_dir)) {
                        mkdir($target_dir);
                      } else {
                        echo "Error: proof dir exists, but isnt a dir";
                        $target_dir = "";
                      }
                    }
                  }

                  if ($target_dir == "") {
                    echo "Error: could not find a suitable proofs dir";
                    http_response_code(404);
                  } else {
                    $target_file = $target_dir . basename($_FILES["img"]["name"]);
                    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                    $info = pathinfo($_FILES['img']['name']);
                    $basename = $info['basename']; // get the extension of the file
                    $target = $target_dir . "/" . $basename;

                    if (move_uploaded_file($_FILES['img']['tmp_name'], $target)) {

                      $stmt = $conn->prepare("INSERT INTO proofs (round_id, team_id, time, img_url, deleted) VALUES (:round_id, :team_id, :time, :img_url, false)");
                      $stmt->bindParam(':round_id', $round_id, PDO::PARAM_STR, 255);
                      $stmt->bindParam(':team_id', $team_id, PDO::PARAM_INT);
                      $stmt->bindParam(':img_url', $target, PDO::PARAM_STR, 255);
                      $stmt->bindParam(':time', date("Y-m-d H:i:s"), PDO::PARAM_STR, 255);
                      $stmt->execute();
                      
                      $stmt = $conn->prepare("SELECT * FROM proofs WHERE (round_id = :round_id AND team_id = :team_id AND img_url = :img_url) ORDER BY id DESC LIMIT 1");
                      $stmt->bindParam(':round_id', $round_id, PDO::PARAM_STR, 255);
                      $stmt->bindParam(':team_id', $team_id, PDO::PARAM_INT);
                      $stmt->bindParam(':img_url', $target, PDO::PARAM_INT);
                      $stmt->execute();

                      $result = $stmt->fetch();
                      if ($result !== false) {
                        echo "Proof created";
                        foreach ($result as $key => $value) {
                          echo " data-$key='$value'";
                        }
                        http_response_code(200);
                      } else {
                        echo "Proof database entry creation failed";
                        http_response_code(500);
                      }
                    } else {
                      echo "File move failed";
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
          }
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
