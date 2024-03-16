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
  <title>Kolo <?php echo $round_id;?></title>
  <link rel="stylesheet" href="basicstyles.css">

  <style>
    #timer {
      /* font-size: 1.3rem; */
      text-shadow: 1px 1px var(--accent-pink), -1px -1px var(--accent-blue);
    }

    #hint_container {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;

      /* overflow: scroll; */
      width: 100%;
    }
    
    @media screen and (orientation: portrait) {
      #hint_container {
        flex-direction: column;
      }
      #hint_container img {
        width: 100%;
      }
    }
    
    @media screen and (orientation: landscape) {
      #hint_container {
        flex-direction: row;
        justify-content: left;
        height: calc(100vh / 3);
      }
      #hint_container img {
        height: 100%;
      }
    }
  </style>
</head>

<body>
  <h1>Předmajálesová hra</h1>
  <h4><a href="index.php">zpět na úvod</a></h4>
  <?php
  if ($row === false) {
    echo "</body></html>";
    exit();
  }
  ?>
  <h2>Kolo <?php echo $round_id.": \"".$row["nickname"]."\""; ?></h2>

  <h3>Čas: </h3>
  <p>
    <b>Začátek:</b> <?php echo $row['start_time']; ?><br>
    <b>Konec:</b> <?php echo $row['end_time']; ?>
  </p>
  <p id="timer"></p>

  <h3>Váš důkaz:</h3>

  <h3>Nápovědy (od nejnovější):</h3>

  <div id="hint_container">
    <?php
    if ($row["hint_folder"] != "") {
      try {
        $target_dir = $row["hint_folder"];

        $hint_files = scandir($target_dir);
        if ($hint_files === false) {
          throw new Exception("scandir failed");
        }

        usort($hint_files, function ($a, $b) use ($target_dir) {
          $fileA = $target_dir . '/' . $a;
          $fileB = $target_dir . '/' . $b;
          return filemtime($fileB) - filemtime($fileA);
        });

        foreach ($hint_files as $file) {
          if ($file != "." && $file != "..") {
            echo "<img src='$target_dir/$file' onclick='window.location = \"$target_dir/$file\"'>";
          }
        }
      } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
      }
    }
    ?>
  </div>

  <script>
    let timer = document.getElementById('timer');
    let countDownDate = new Date("<?php echo $row['end_time']; ?>").getTime();
    let startDate = new Date("<?php echo $row['start_time']; ?>").getTime();

    function timeTimer() {
      let now = new Date().getTime();
      let end_distance = countDownDate - now;
      let start_distance = now - startDate;

      let end_days = Math.floor(end_distance / (1000 * 60 * 60 * 24));
      let end_hours = Math.floor((end_distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      let end_minutes = Math.floor((end_distance % (1000 * 60 * 60)) / (1000 * 60));
      let end_seconds = Math.floor((end_distance % (1000 * 60)) / 1000);

      let start_days = Math.floor(start_distance / (1000 * 60 * 60 * 24));
      let start_hours = Math.floor((start_distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      let start_minutes = Math.floor((start_distance % (1000 * 60 * 60)) / (1000 * 60));
      let start_seconds = Math.floor((start_distance % (1000 * 60)) / 1000);

      timer.innerHTML = "<b>Zbývající:</b> " + end_days + "d " + end_hours + "h " + end_minutes + "m " + end_seconds + "s" +
        "<br>" + "<b>Od startu:</b> " + start_days + "d " + start_hours + "h " + start_minutes + "m " + start_seconds + "s";

      if (end_distance < 0) {
        clearInterval(x);
        timer.innerHTML = "Čas pro toto kolo vypršel!";
      }
    }

    let x = setInterval(timeTimer, 1000);
    timeTimer();
  </script>
</body>

</html>