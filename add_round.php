  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
          http_response_code(401);
        } else {
          $team_id = $row['id'];
          $team_name = $row['name'];

          if ($team_id == 1 && $team_name == "admin") {
            //check if all arguments (nickname, category_higher, category_lower, end) are set
            $missing_arguments = false;
            if (!isset($_FILES["first-hint-img"]) && !(isset($_POST['edit']) && $_POST['edit'] == "true" && isset($_POST['round_id']))) {
              echo "Missing argument: first-hint-img";
              $missing_arguments = true;
            }
            if (!isset($_POST['nickname']) || $_POST['nickname'] == "") {
              echo "Missing argument: nickname";
              $missing_arguments = true;
            }
            if (!isset($_POST['category']) || $_POST['category'] == "") {
              echo "Missing argument: category";
              $missing_arguments = true;
            }
            if (!isset($_POST['end']) || $_POST['end'] == "") {
              echo "Missing argument: end";
              $missing_arguments = true;
            }
            if ((isset($_POST['edit']) && $_POST['edit'] == "true" && isset($_POST['round_id'])) && !isset($_POST['start'])) {
              echo "Missing argument: start";
              $missing_arguments = true;
            } else if (isset($_POST['start'])) {
              $start = $_POST['start'];
            }

            if ($missing_arguments) {
              http_response_code(400);
            } else {
              $nickname = $_POST['nickname'];
              $category = $_POST['category'];
              $end = str_replace("T", " ", $_POST['end']);

              // echo "nickname: $nickname\n";
              // echo "category: $category\n";
              // echo "end: $end\n";

              if ($category == "lower") {
                $category = 1;
              } else if ($category == "higher") {
                $category = 2;
              } else if ($category == "both") {
                $category = 3;
              }

              if ($category != 1 && $category != 2 && $category != 3) {
                echo "Invalid category";
                http_response_code(400);
              } else {
                // Check if image file is a actual image or fake image
                if ((isset($_POST['edit']) && $_POST['edit'] == "true" && isset($_POST['round_id'])) || ($_FILES["first-hint-img"]["error"] === 0 && $_FILES["first-hint-img"]["tmp_name"] != "")) {
                  if (!(isset($_POST['edit']) && $_POST['edit'] == "true" && isset($_POST['round_id']))) {
                    $check = getimagesize($_FILES["first-hint-img"]["tmp_name"]);
                  } else {
                    $check = true;
                  }
                  if ($check != false) {
                    $new_round_id = null;
                    $target_dir = "";
                    if (isset($_POST['edit']) && $_POST['edit'] == "true") {
                      $stmt = $conn->prepare("SELECT hint_folder FROM rounds WHERE id = :round_id");
                      $stmt->bindParam(':round_id', $_POST['round_id'], PDO::PARAM_INT);
                      $stmt->execute();

                      $result = $stmt->fetch();
                      if ($result !== false) {
                        $target_dir = $result['hint_folder'];
                        $new_round_id = $_POST['round_id'];
                      } else {
                        echo "Round to edit not found";
                        http_response_code(404);
                      }
                    } else {
                      $stmt = $conn->prepare("SELECT id FROM rounds ORDER BY id DESC LIMIT 1");
                      // $stmt->bindParam(':nickname', $nickname, PDO::PARAM_STR, 255);
                      $stmt->execute();

                      $new_round_id = 1;
                      if ($stmt->rowCount() > 0) {
                        $current_max_round_num = $stmt->fetchColumn();
                        $new_round_id = $current_max_round_num + 1;
                      }

                      if (!is_dir("./hints/round$new_round_id")) {
                        if (!file_exists("./hints/round$new_round_id")) {
                          if (!mkdir("./hints/round$new_round_id")) {
                            print_r(error_get_last());
                            echo "Error: could not create dir";
                            $target_dir = "";
                          } else {
                            $target_dir = "./hints/round$new_round_id";
                          }
                        } else {
                          echo "Error: hint dir exists, but isnt a dir";
                        }
                      }

                      if ($target_dir == "") {
                        for ($i = 1; $i < 1000; $i++) {
                          if (!is_dir("./hints/round$i")) {
                            if (!file_exists("./hints/round$i")) {
                              if (!mkdir("./hints/round$i")) {
                                print_r(error_get_last());
                                echo "Error: could not create dir";
                                $target_dir = "";
                              } else {
                                $target_dir = "./hints/round$i";
                                $new_round_id = $i;
                                break;
                              }
                            } else {
                              echo "Error: hint dir exists, but isnt a dir";
                              break;
                            }
                          }
                        }
                      }
                    }

                    if ($target_dir == "") {
                      echo "Error: could not find a suitable hints dir";
                      http_response_code(404);
                    } else {
                      if (isset($_POST['edit']) && $_POST['edit'] == "true") {
                        if (isset($_POST['round_id'])) {
                          $stmt = $conn->prepare("SELECT * FROM rounds WHERE id = :round_id");
                          $stmt->bindParam(':round_id', $new_round_id, PDO::PARAM_INT);
                          $stmt->execute();

                          $result = $stmt->fetch();
                          echo "before" . $result['start_time'];
                          echo "<br>update<br>";
                          $stmt = $conn->prepare("UPDATE rounds SET nickname = :nickname, category = :category, end_time = :end, start_time=:start WHERE id = :round_id");
                          $stmt->bindParam(':nickname', $nickname, PDO::PARAM_STR, 255);
                          $stmt->bindParam(':category', $category, PDO::PARAM_INT);
                          $stmt->bindParam(':end', $end, PDO::PARAM_STR, 255);
                          $stmt->bindParam(':start', $start, PDO::PARAM_STR, 255);
                          $stmt->bindParam(':round_id', $new_round_id, PDO::PARAM_INT);
                          $stmt->execute();

                          $stmt = $conn->prepare("SELECT * FROM rounds WHERE id = :round_id");
                          $stmt->bindParam(':round_id', $new_round_id, PDO::PARAM_INT);
                          $stmt->execute();

                          $result = $stmt->fetch();
                          echo "after" . $result['start_time'];
                        } else {
                          echo "round_id to edit not set";
                          http_response_code(400);
                        }
                      } else {
                        $target_file = $target_dir . basename($_FILES["first-hint-img"]["name"]);
                        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                        $info = pathinfo($_FILES['first-hint-img']['name']);
                        $basename = $info['basename']; // get the extension of the file
                        $target = $target_dir . "/" . $basename;

                        move_uploaded_file($_FILES['first-hint-img']['tmp_name'], $target);

                        $stmt = $conn->prepare("INSERT INTO rounds (nickname, category, start_time, end_time, hint_folder) VALUES (:nickname, :category, :time, :end, :hint_folder)");
                        $stmt->bindParam(':nickname', $nickname, PDO::PARAM_STR, 255);
                        $stmt->bindParam(':category', $category, PDO::PARAM_INT);
                        $stmt->bindParam(':end', $end, PDO::PARAM_STR, 255);
                        $stmt->bindParam(':time', date("Y-m-d H:i:s"), PDO::PARAM_STR, 255);
                        $stmt->bindParam(':hint_folder', $target_dir, PDO::PARAM_STR, 255);
                        $stmt->execute();
                      }

                      $stmt = $conn->prepare("SELECT * FROM rounds WHERE (nickname = :nickname AND category = :category AND end_time = :end AND hint_folder = :hint_folder)");
                      $stmt->bindParam(':nickname', $nickname, PDO::PARAM_STR, 255);
                      $stmt->bindParam(':category', $category, PDO::PARAM_INT);
                      $stmt->bindParam(':end', $end, PDO::PARAM_STR, 255);
                      $stmt->bindParam(':hint_folder', $target_dir, PDO::PARAM_STR, 255);
                      $stmt->execute();

                      $result = $stmt->fetch();
                      if ($result !== false) {
                        if (!(isset($_POST['edit']) && $_POST['edit'] == "true" && isset($_POST['round_id']))) {
                          $new_round_sql_id = $result['id'];

                          $hint_files = scandir($target_dir);

                          usort($hint_files, function ($a, $b) use ($target_dir) {
                            $fileA = $target_dir . '/' . $a;
                            $fileB = $target_dir . '/' . $b;
                            return filemtime($fileA) - filemtime($fileB);
                          });

                          $stmt = $conn->prepare("SELECT * FROM emails");
                          $stmt->execute();

                          $result = $stmt->fetch();
                          while ($result !== false) {
                            if ($result['category'] == 3 || $category == 3 || $result['category'] == $category) {
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
                          }

                          echo "round.php?round_id=$new_round_sql_id";
                          http_response_code(200);
                        } else {
                          echo "Round creation failed";
                          http_response_code(500);
                        }
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
