<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Emailové notifikace předmajálesovky</title>
</head>
<body>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Kanit&display=swap');
  :root {
    --background: hsl(192, 15%, 10%);
    --text: white;
    --accent: red;
    --round-row-height: min(100vh / 3, 250px);
  }

  body {
    font-family: 'Kanit', sans-serif;
    background: var(--background);
    color: var(--text);
  }
</style>
<h1>Předmajálesová hra</h1>
<h2>Registrace emailových notifikací</h2>
<h3><a href="index.php">zpět na úvod</a></h3>
<p>Po registraci emailových notifikací dostanete email pokaždé, když se na webu objeví nová nápověda. Je možné se z nich odhlásit (link na konci každého mailu).</p>
  <form action="add_email.php" method="get">
    <input type="email" placeholder="email" id="email" name="email">
    <input type="submit" value="Registrovat">
  </form>
</body>
</html>