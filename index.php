<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>predmajalesovka</title>


  <link rel="stylesheet" href="styles.css">
  <script src="script.js" defer></script>
</head>

<body>
  <div id="header">
    <h1>Předmajálesová hra</h1>
    <h3>tohle je some text ja nevim asi ig</h3>
    Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's
    standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a
    type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting,
    remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing
    Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of
    Lorem Ipsum.

    <?php

    ?>
  </div>
  <div id="body">
    <div id="flex-container">
      <?php
      $rounds_count = 14;
      for ($i = 0; $i < $rounds_count; $i++) {
        echo "<div class='round_row'>";
        echo "<div class='round_nick'><p>15.3. \"ani nahoře, ani dole awawaw a w a sda sd asdasd asd\"</p></div>";

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
  <div id="navbar"></div>
</body>

</html>