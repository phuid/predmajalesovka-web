<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>predmajalesovka</title>

  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="basicstyles.css">
  <style id="header_styletag"></style>
  <style id="adminperm_styletag"></style>

  <script src="https://cdn.jsdelivr.net/npm/lazyload@2.0.0-rc.2/lazyload.js"></script>
  <script src="script.js" defer></script>
</head>

<body>
  <div id="header">
    <div class="flex flex-row flex-space-between" id="top-bar">
      <h3 id="account"></h3>
      <img src="map_logo.png" alt="logo" id="logo">
    </div>

    <h1>Předmajálesová hra</h1>

    <h3><a href="email_registration.php">Registrace emailových notifikací o nových nápovědách</a></h3>

    <h3><a href="results.php">Průběžné výsledky</a></h3>

    <h3><a href="rules.php">Pravidla</a></h3>

    <h3 class="adminperm" style="cursor: pointer;" onclick="toggleVisibility(document.getElementById('new-round-form')); document.getElementById('new-round-form').scrollTo()"><u>Přidat kolo</u></h3>

    <form action="add_round.php" method="post" enctype="multipart/form-data" class="adminperm" id="new-round-form">
      <h3>Přidat kolo</h3>
      <input type="text" name="nickname" placeholder="nickname">
      <fieldset>
        <legend>Kategorie:</legend>
        <input type="radio" name="category" value="lower">nižší
        <input type="radio" name="category" value="higher">vyšší
        <input type="radio" name="category" value="both">obě
      </fieldset>

      <label for="new-round-end-time">Deadline:</label>
      <input type="datetime-local" name="end" id="new-round-end-time">
      <br>

      <label for="first-hint-img">First hint:</label>
      <input type="file" name="first-hint-img" id="first-hint-img">
      <br>

      <input type="button" value="Přidat" onclick="sendData()">
    </form>

    <script>
      function sendData(data) {
        // Construct a FormData instance
        const myform = document.getElementById('new-round-form');
        const formData = new FormData(myform);

        // let password = document.cookie.replace(/(?:(?:^|.*;\s*)password\s*\=\s*([^;]*).*$)|^.*$/, "$1");
        // console.log(password);
        // formData.append("password", password);

        fetch("add_round.php", {
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
                if (confirm("Round created\nDo you want to go to the new round's page?")) {
                  window.location.replace(txt);
                }
              });
            } else {
              console.log("fail");
              response.text().then(txt => alert("Round create failed, status: " + response.status + "\nmessage: " + txt));
            }
            response.text().then(txt => console.log(txt));
          }
        ).catch(e => console.log(e));
      }
    </script>

  </div>
  <div id="body">
    <div class="flex flex-row flex-space-between" style="flex-wrap: wrap;">
      <h2 style="display: inline;">Kola:</h2>
      <div class="flex flex-row">
        <label for="rounds-order">Seřadit podle:</label>
        <select id="rounds-order" onchange="sortRounds(this.value, document.getElementById('rounds-order-direction').value)">
          <option value="nickname">Název</option>
          <option selected value="id">Číslo kola</option>
          <option value="category">Kategorie</option>
          <option value="start_time">Čas začátku</option>
          <option value="end_time">Čas konce</option>
        </select>
        <select id="rounds-order-direction" onchange="sortRounds(document.getElementById('rounds-order').value, this.value)">
          <option value="asc">Vzestupně</option>
          <option selected value="desc">Sestupně</option>
        </select>
        <u style="padding-left: 1rem; cursor: pointer;" onclick="toggleVisibility(document.getElementById('filters-container')); filterRounds();">
          Filtry
        </u>
      </div>
    </div>
    <div class="flex flex-column" style="align-items: end;">
      <div id="filters-container" style="display: none">
        <h3 style="display: inline;">Filtry:</h3>
        <table>

          <tr>
            <td>
              <label for="filter-category">Kategorie:</label>
            </td>
            <td>
              <select id="filter-category" onchange="filterRounds()">
                <option value="lower">nižší</option>
                <option value="higher">vyšší</option>
                <option selected value="both">obě</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>
              <label for="filter-start-time">Čas začátku:</label>
            </td>
            <td>
              <input type="datetime-local" id="filter-start-time" oninput="filterRounds()">
            </td>
          </tr>
          <tr>
            <td>
              <label for="filter-end-time">Čas konce:</label>
            </td>
            <td>
              <input type="datetime-local" id="filter-end-time" oninput="filterRounds()">
            </td>
          </tr>
        </table>
      </div>
    </div>

    <div id="rounds-container">
      <?php
      try {
        $servername = "localhost";
        $username = "root";
        $password = "";

        $conn = new PDO("mysql:host=$servername;dbname=predmajalesova_hra", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT * FROM rounds ORDER BY id DESC");
        $stmt->execute();
        $result = $stmt->fetch();
        while ($result !== false) {
          echo "<div class='round_row";
          if ($result["end_time"] < date("Y-m-d H:i:s")) {
            echo " round_expired";
          }
          echo "'";

          foreach ($result as $key => $value) {
            echo " data-$key='$value'";
          }

          echo " onclick=\"window.location='round.php?round_id=" . $result["id"] . "';\">";
          echo "<div class='round_nick'><p><b>Kolo " . $result["id"] . ": \"" . $result["nickname"] . "\"</b><br><b>Začátek:</b> " . $result["start_time"] . "<br><b>Konec:</b> " . $result["end_time"] . "</p></div>";

          if ($result["hint_folder"] != "") {
            try {
              $target_dir = $result["hint_folder"];

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
                  echo "<img class='round_img lazyload' data-src='$target_dir/$file'>";
                }
              }
            } catch (Exception $e) {
              echo "Error: " . $e->getMessage();
            }
          }

          echo "</div>";
          $result = $stmt->fetch();
        }
      } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
      }

      ?>
    </div>
  </div>
</body>

</html>