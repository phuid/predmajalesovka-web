<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>predmajalesovka</title>

  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="basicstyles.css">
  <script src="script.js" defer></script>
  <style id="header_styletag"></style>
  <style id="adminperm_styletag"></style>
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

    <h3 class="adminperm" onclick="toggleVisibility(document.getElementById('new-round-form')); document.getElementById('new-round-form').scrollTo()"><u>Přidat kolo</u></h3>

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
    <div id="flex-container">
      <?php
      $rounds_count = 14;
      for ($i = 0; $i < $rounds_count; $i++) {
        echo "<div class='round_row'>";
        echo "<div class='round_nick'><p>15.3. \"ani nahoře, ani dole \"</p></div>";

        $img_width = rand(9, 20) * 40;
        $img_height = rand(9, 20) * 40;
        echo "<img class='round_img' src='https://picsum.photos/$img_width/$img_height'>";

        $img_width = rand(9, 20) * 40;
        $img_height = rand(9, 20) * 40;
        echo "<img class='round_img' src='https://picsum.photos/$img_width/$img_height'>";

        $img_width = rand(9, 20) * 40;
        $img_height = rand(9, 20) * 40;
        echo "<img class='round_img' src='https://picsum.photos/$img_width/$img_height'>";

        echo "</div>";
      }
      ?>
    </div>
  </div>
</body>

</html>