<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_COOKIE['password'])) {
    $cookie_password = $_COOKIE['password'];
    $cookie_password = preg_replace('/[^0-9A-Za-z]/', '', $cookie_password);

    $servername = "localhost";
    $username = "root";
    $password = "";

    try {
      $conn = new PDO("mysql:host=$servername;dbname=predmajalesova_hra", $username, $password);
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
          if (!isset($_FILES["first-hint-img"])) {
            echo "Missing argument: first-hint-img";
            $missing_arguments = true;
          }
          if (!isset($_POST['nickname'])) {
            echo "Missing argument: nickname";
            $missing_arguments = true;
          }
          if (!isset($_POST['category_higher'])) {
            echo "Missing argument: category_higher";
            $missing_arguments = true;
          }
          if (!isset($_POST['category_lower'])) {
            echo "Missing argument: category_lower";
            $missing_arguments = true;
          }
          if (!isset($_POST['end'])) {
            echo "Missing argument: end";
            $missing_arguments = true;
          }

          if ($missing_arguments) {
            http_response_code(400);
          } else {
            $nickname = $_POST['nickname'];
            $category_higher = $_POST['category_higher'];
            $category_lower = $_POST['category_lower'];
            $end = $_POST['end'];

            echo "end: $end";

            if ($category_higher == "1" && $category_lower == "2") {
              $category = 3;
            } else if ($category_higher == "1") {
              $category = 1;
            } else if ($category_lower == "2") {
              $category = 2;
            } else {
              echo "Invalid category";
              http_response_code(400);
            }

            $stmt = $conn->prepare("INSERT INTO rounds (nickname, category, start_time, end_time) VALUES (:nickname, :category, NOW(), :end)");
            $stmt->bindParam(':nickname', $nickname, PDO::PARAM_STR, 255);
            $stmt->bindParam(':category', $category, PDO::PARAM_INT);
            $stmt->bindParam(':end', $end, PDO::PARAM_STR, 255);
            $stmt->execute();
            // Check if image file is a actual image or fake image
            if ($_FILES["first-hint-img"]["error"] === 0 && $_FILES["first-hint-img"]["tmp_name"] != "") {
              $check = getimagesize($_FILES["first-hint-img"]["tmp_name"]);
              if ($check != false) {
                $new_round_id = null;
                $target_dir = "";
                for ($i = 0; $i < 1000; $i++) {
                  if (!is_dir("../hints/round$i")) {
                    if (!file_exists("../hints/round$i")) {
                      mkdir("../hints/round$i");
                      $target_dir = "../hints/round$i";
                      $new_round_id = $i;
                      break;
                    } else {
                      echo "Error: hint dir exists, but isnt a dir";
                      break;
                    }
                  }
                }
                if ($target_dir == "") {
                  echo "Error: could not find a suitable hints dir";
                  http_response_code(404);
                } else {
                  $target_file = $target_dir . basename($_FILES["first-hint-img"]["name"]);
                  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                  $info = pathinfo($_FILES['first-hint-img']['name']);
                  $basename = $info['basename']; // get the extension of the file
                  $target = $target_dir . "/" . $basename;

                  move_uploaded_file($_FILES['first-hint-img']['tmp_name'], $target);

                  echo "round.php?round=$new_round_id";
                  http_response_code(200);
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
