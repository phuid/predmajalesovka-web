<?php
date_default_timezone_set('Europe/Prague');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (isset($_GET['round_id'])) {
    $round_id = $_GET['round_id'];

    $config = parse_ini_file('config.ini');

    $sql_servername = $config['sql_servername'];
    $sql_username = $config['sql_username'];
    $sql_password = $config['sql_password'];

    try {
      $conn = new PDO("mysql:host=$sql_servername;dbname=predmajalesova_hra", $sql_username, $sql_password);
      // set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $stmt = $conn->prepare("SELECT * FROM rounds WHERE id = :round_id");
      $stmt->bindParam(':round_id', $round_id, PDO::PARAM_INT);
      $stmt->execute();

      $row = $stmt->fetch();
      if ($row === false) {
        echo "<h1>No round with id $round_id found!</h1>";
      }
    } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
      $row = false;
    }
  } else {
    echo "<h1>round field is missing</h1>";
    $row = false;
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
  <title>Kolo <?php echo $round_id; ?></title>
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

    body {
      display: flex;
    }

    #body,
    #header {
      /* border: 1px solid var(--text); */
      -ms-overflow-style: none;
      /* IE and Edge */
      scrollbar-width: none;
      /* Firefox */
    }

    #body::-webkit-scrollbar,
    #header::-webkit-scrollbar {
      display: none;
    }

    #header:before {
      background: url("map_logo.png") var(--background) no-repeat bottom center;
      opacity: 0.2;
      background-size: contain;
      content: " ";
      display: inline-block;
      position: absolute;
      z-index: -1;
    }

    #top-bar {
      height: 5rem;
    }

    .proof_img {
      max-width: 95%;
      max-height: 95%;
      cursor: zoom-in;
    }

    #logo {
      height: 5vh;
      padding: 1vh;
    }

    table * {
      padding: 5px;
    }

    table tr:nth-child(even) {
      background-color: rgba(0, 0, 0, 10%);
    }

    table tr:nth-child(odd) {
      background-color: rgba(255, 255, 255, 10%);
    }

    table th {
      background-color: var(--accent-blue);
      color: var(--background);
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

      body {
        flex-direction: row;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
      }

      #body,
      #header {
        overflow-y: scroll;
        height: 100%;
      }

      #header {
        width: 30vw;
      }

      #body {
        width: 65vw;
      }
    }

    @media screen and (orientation: portrait) {
      #hint_container {
        flex-direction: column;
      }

      #hint_container img {
        width: 100%;
      }

      body {
        flex-direction: column;
        overflow-y: scroll;
      }
    }
  </style>
  <style id="header_styletag"></style>
  <style id="adminperm_styletag"></style>
</head>

<body>
  <div id="header">
    <div class="flex flex-row flex-space-between" id="top-bar">
      <h3 id="account"><a href="login.php">Přihlásit se</a></h3>
      <img src="map_logo.png" alt="logo" id="logo">
    </div>

    <h1>Předmajálesová hra</h1>
    <h4><a href="index.php">zpět na úvod</a></h4>
    <?php
    if ($row === false) {
      echo "</body></html>";
      exit();
    }
    ?>
    <h2>Kolo <?php echo $round_id . ": \"" . $row["nickname"] . "\""; ?></h2>

    <h3 style="display: inline;">Kategorie: </h3> <?php echo (($row['category'] == 3) ? "obě" : (($row['category'] == 1) ? "nižší" : (($row['category'] == 2) ? "vyšší" : "Nepodařilo se načíst kategorii (value:" . $row['category'] . ")"))); ?>

    <h3>Čas: </h3>
    <p>
      <b>Začátek:</b> <?php echo $row['start_time']; ?><br>
      <b>Konec:</b> <?php echo $row['end_time']; ?>
    </p>
    <p id="timer"></p>

    <h3 class="adminperm"><u onclick="toggleVisibility(document.getElementById('add-hint-form'))">add hint</u></h3>

    <form class="adminperm" style="display: none;" id="add-hint-form">
      <input type="file" name="new-hint-img" id="new-hint-img">
      <input type="button" value="Nahrát" onclick="addHint()">
    </form>

    <script>
      function toggleVisibility(x) {
        if (x.style.display === "block") {
          x.style.display = "none";
        } else {
          x.style.display = "block";
        }
      }

      function addHint() {
        const myform = document.getElementById('add-hint-form');
        const formData = new FormData(myform);

        formData.append("round_id", <?php echo $round_id; ?>);

        fetch("add_hint.php", {
          method: "POST",
          body: formData,
        }).then(
          (response) => {
            if (response.status === 200) {
              console.log("success");
              myform.reset();
              response.text().then((txt) => {
                console.log(txt);
                alert("Nápověda úspěšně nahrána, děkujeme!");
                location.reload();
              });
            } else {
              console.log("fail");
              response.text().then(txt => alert("Nahrání nápovědy selhalo, status: " + response.status + "\nmessage: " + txt));
            }
          }
        ).catch(e => console.log(e));
      }
    </script>

    <h3 class="adminperm"><u onclick="toggleVisibility(document.getElementById('edit-round-form'))">edit round</u></h3>
    <form id="edit-round-form" style="display: none; border: 1px solid red;">
      <label for="nickname">Nickname:</label>
      <input type="text" name="nickname" id="nickname" placeholder="nickname" value="<?php echo $row['nickname'] ?>">
      <br>
      <label for="new-round-end-time">start_time:</label>
      <input type="datetime-local" name="start" id="start_time" value="<?php echo $row['start_time'] ?>">
      <br>
      <label for="new-round-end-time">Deadline:</label>
      <input type="datetime-local" name="end" id="end_time" value="<?php echo $row['end_time'] ?>">

      <fieldset>
        <legend>Kategorie:</legend>
        <input type="radio" name="category" value="lower" <?php if ($row['category'] == 1) {
                                                            echo "checked";
                                                          } ?>>nižší
        <input type="radio" name="category" value="higher" <?php if ($row['category'] == 2) {
                                                              echo "checked";
                                                            } ?>>vyšší
        <input type="radio" name="category" value="both" <?php if ($row['category'] == 3) {
                                                            echo "checked";
                                                          } ?>>obě
      </fieldset>
      <input type="button" value="Upravit" onclick="editRound()">
    </form>
    <script>
      function editRound() {
        const myform = document.getElementById('edit-round-form');
        const formData = new FormData(myform);

        formData.append("edit", true);
        formData.append("round_id", <?php echo $round_id; ?>);

        fetch("add_round.php", {
          method: "POST",
          body: formData,
        }).then(
          (response) => {
            if (response.status === 200) {
              console.log("success");
              myform.reset();
              response.text().then((txt) => {
                console.log(txt);
                alert("Kolo úspěšně upraveno, děkujeme!");
                location.reload();
              });
            } else {
              console.log("fail");
              response.text().then(txt => alert("Úprava kola selhala, status: " + response.status + "\nmessage: " + txt));
            }
          }
        ).catch(e => console.log(e));
      }
    </script>

    <h3>Váš důkaz:</h3>
    <?php
    $cookie_password = $_COOKIE['password'];
    // Assuming you have established a database connection
    $stmt = $conn->prepare("SELECT * FROM teams WHERE password = :password");
    $stmt->bindParam(':password', $cookie_password, PDO::PARAM_STR, 255);
    $stmt->execute();

    $result = $stmt->fetch();
    if ($result !== false) {
      $team_id = $result['id'];
      $team_name = $result['name'];
      $team_category = $result['category'];

      if ($team_name == "admin") {
        $stmt = $conn->prepare("SELECT * FROM proofs WHERE round_id = :roundId");
      } else {
        // Prepare the SQL query
        $stmt = $conn->prepare("SELECT * FROM proofs WHERE team_id = :teamId AND round_id = :roundId");
        $stmt->bindParam(':teamId', $team_id);
      }
      $stmt->bindParam(':roundId', $round_id);

      // Execute the query
      $stmt->execute();

      $result = $stmt->fetch();
      while ($result != false) {

        $team_stmt = $conn->prepare("SELECT name, category FROM teams WHERE id = :teamId");
        $team_stmt->bindParam(':teamId', $result['team_id']);
        $team_stmt->execute();
        $proof_team = $team_stmt->fetch();

        echo "<div";
        if ($result['deleted'] == 1) {
          echo " style='display: none;'";
        }
        echo " id='proof-" . $result['id'] . "'>";
        echo "<img class=\"proof_img\" onclick=\"window.location='" . $result['img_url'] . "'\" src='" . $result['img_url'] . "'><br>";
        echo "Čas nahrání: " . $result['time'] . "<br>";
        echo "Tým: " . $proof_team['name'] . "<br>";
        echo "Ověřeno adminem: <a class='adminverify-txt'>" . (($result['verified'] === NULL) ? "zatím ne" : (($result['verified'] == false) ? "zamítnuto" : "ano")) . "</a>";
        echo "<button class='adminperm' onclick=\"verifyProof(" . $result['id'] . ")\">Ověřit / Zrušit ověření</button>";
        echo "<br><button onclick=\"deleteProof(" . $result['id'] . ")\">Smazat</button>";
        echo "<hr></div>";
        $result = $stmt->fetch();
      }
    }

    ?>
    <form action="add_proof.php" method="post" enctype="multipart/form-data" id="new-proof-form">

      <input type="file" name="img" id="img">

      <input type="button" value="Nahrát" onclick="sendData()">
    </form>

    <script>
      function sendData(data) {
        // Construct a FormData instance
        const myform = document.getElementById('new-proof-form');
        const formData = new FormData(myform);

        formData.append("round_id", <?php echo $round_id; ?>);

        // let password = document.cookie.replace(/(?:(?:^|.*;\s*)password\s*\=\s*([^;]*).*$)|^.*$/, "$1");
        // console.log(password);
        // formData.append("password", password);

        fetch("add_proof.php", {
          method: "POST",
          // Set the FormData instance as the request body
          body: formData,
        }).then(
          (response) => {
            if (response.status === 200) {
              console.log("success");
              myform.reset();
              // setTimeout(() => location.reload(), 1000);
              response.text().then((txt) => {
                console.log(txt);
                alert("Důkaz úspěšně nahrán, děkujeme!");
                location.reload();
              });
            } else {
              console.log("fail");
              response.text().then(txt => alert("Nahrání důkazu selhalo, status: " + response.status + "\nmessage: " + txt));
            }
          }
        ).catch(e => console.log(e));
      }

      function verifyProof(id) {
        // Send GET request to verify_proof.php with id as argument
        fetch(`alter_proof.php?id=${id}&action=verifie`, {
          method: "GET"
        }).then(
          (response) => {
            if (response.status === 200) {
              response.text().then((txt) => {
                document.getElementById(`proof-${id}`).getElementsByClassName("adminverify-txt")[0].innerText = (Number(txt.substring(txt.indexOf("=") + 1)) == 1) ? "ano" : "zamítnuto";
              });
              // Handle successful verification
            } else {
              response.text().then(txt => alert("Verifikace důkazu selhalo, status: " + response.status + "\nmessage: " + txt));
              // Handle verification failure
            }
          }
        ).catch(e => console.log(e));
      }

      function deleteProof(id) {
        if (confirm("Opravdu chcete smazat tento důkaz?")) {
          fetch(`alter_proof.php?id=${id}&action=delete`, {
            method: "GET"
          }).then(
            (response) => {
              if (response.status === 200) {
                alert("deletion successful");
                response.text().then((txt) => {
                  document.getElementById(`proof-${id}`).style.display = "none";
                });
                // Handle successful verification
              } else {
                response.text().then(txt => alert("Smazání důkazu selhalo, status: " + response.status + "\nmessage: " + txt));
                // Handle verification failure
              }
            }
          ).catch(e => console.log(e));
        }
      }
    </script>
  </div>

  <div id="body">

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
              echo "<img style='cursor: zoom-in;' src='$target_dir/$file' onclick='window.location = \"$target_dir/$file\"'>";
            }
          }
        } catch (Exception $e) {
          echo "Error: " . $e->getMessage();
        }
      }
      ?>
    </div>
    <h3>Výsledky:</h3>
    <div id="results-container">
      <?php
      $stmt = $conn->prepare("SELECT round_id, team_id, time, img_url, verified, deleted, name, category FROM proofs INNER JOIN teams ON teams.id = proofs.team_id WHERE round_id = :roundId AND deleted = false AND (verified IS NULL OR verified = true) GROUP BY team_id ORDER BY time ASC");
      $stmt->bindParam(':roundId', $round_id);
      $stmt->execute();
      $allResults = $stmt->fetchAll();

      $points = [100, 70, 50, 40, 30, 20];
      for ($cat = 1; $cat < 4; $cat++) {

        echo "<h4> Kategorie " . $cat . " (" . (($cat == 1) ? "nižší" : (($cat == 2) ? "vyšší" : "přístup ke všem kolům")) . "): </h4>";

        echo "<table> <tr> <th> Pořadí </th> <th> Body </th> <th> Tým </th> <th> Čas </th> <th> Ověřeno adminem </th> </tr>";
        $i = 1;
        foreach ($allResults as $result) {
          if ($result['category'] != $cat) {
            continue;
          }

          try {
            echo "<tr";
            if ($result['verified'] == NULL) {
              echo " style='opacity: 70%;'";
            }
            echo ">";
            echo "<td> $i. </td>";
            echo "<td>" . $points[$i - 1] . "</td>";
            echo "<td> " . $result['name'] . " </td>";
            echo "<td> " . (new DateTime($row['start_time']))->diff(new DateTime($result['time']))->format("%dd %hh %im %ss") . " </td>";
            echo "<td> <a class='adminverify-txt'>" . (($result['verified'] === NULL) ? "zatím ne" : (($result['verified'] == false) ? "zamítnuto" : "ano")) . "</a>";
            echo "</td> </tr>";
          } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
          }
          $i++;
        }
        echo "</table>";
      }
      ?>
    </div>

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

    function resizeHeaderBg() {
      let id = "#header";
      let header = document.querySelector(id);
      let headerBg = document.querySelector("#header_styletag");

      headerBg.innerHTML =
        id +
        ":before {" +
        "height: " +
        header.clientHeight +
        "px;" +
        "width: " +
        header.clientWidth +
        "px;" +
        "left: " +
        header.offsetLeft +
        "px;" +
        "top: " +
        header.offsetTop +
        "px;" +
        "}";

      // headerBg.style.height = header.clientHeight + "px";
      // headerBg.style.width = header.clientWidth + "px";
      // headerBg.style.left = header.offsetLeft + "px";
      // headerBg.style.top = header.offsetTop + "px";
    }

    window.onresize = resizeHeaderBg();
    addEventListener("resize", (event) => {
      resizeHeaderBg();
    });

    function loadAccount() {
      fetch("accountName.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
        })
        .then((r) => {
          adminperm_styletag = document.getElementById("adminperm_styletag");
          if (r.status != 200) {
            document.getElementById("account").innerHTML =
              "<a href='login.php'>Přihlásit se k týmu</a>";
            adminperm_styletag.innerHTML = ".adminperm {display: none;}";
            return;
          }
          r.text().then((txt) => {
            document.getElementById("account").innerHTML =
              "Přihlášený tým: " +
              txt +
              "<br> <a href='login.php'>Změnit přihlášení</a>";
            if (txt == "admin") {
              adminperm_styletag.innerHTML = ".adminperm {display: block;}";
            } else {
              adminperm_styletag.innerHTML = ".adminperm {display: none;}";
            }
            return;
          });
        })
        .catch(function(err) {
          console.log("Error: " + err);
        });
    }

    timeTimer();
    resizeHeaderBg();
    loadAccount();
  </script>
  <?php
  $conn = null;
  ?>
</body>

</html>