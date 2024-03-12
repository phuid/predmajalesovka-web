<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>predmajalesovka</title>


  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="basicstyles.css">
  <script src="script.js" defer></script>
</head>

<body>
  <div id="header">
    <div class="flex flex-row flex-space-between">
      <h3 id="account"></h3>
      <img src="logo.png" alt="logo" id="logo">
    </div>

    <h1>Předmajálesová hra</h1>

    <h3><a href="email_registration.php">Registrace emailových notifikací o nových nápovědách</a></h3>

    <h3><a href="results.php">Průběžné výsledky</a></h3>

    <h3><a href="rules.php">Pravidla</a></h3>

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