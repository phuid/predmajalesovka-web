<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Emailové notifikace předmajálesovky</title>
  <link rel="stylesheet" href="basicstyles.css">
</head>
<body>
<h1>Předmajálesová hra</h1>
<h4><a href="index.php">zpět na úvod</a></h4>
<h2>Registrace emailových notifikací</h2>
<p>Po registraci emailových notifikací dostanete email pokaždé, když se na webu objeví nová nápověda. Je možné se z nich odhlásit (link na konci každého mailu).</p>
  <form action="add_email.php" method="get">
    <input type="email" placeholder="email" id="email" name="email">
    <input type="submit" value="Registrovat">
  </form>
</body>
</html>