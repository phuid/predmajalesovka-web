<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>predmajalesovka Login</title>
  <link rel="stylesheet" href="basicstyles.css">
</head>
<body>
  <h1>Předmajálesová hra</h1>
  <h4><a href="index.php">zpět na úvod</a></h4>
  <h2>Login</h2>

  <form>
    <input type="password" placeholder="password" id="password" name="password">
    <input type="submit" value="Uložit heslo" onclick="savePassword()">
  </form>

</body>
  <script>
    function savePassword() {
      document.cookie = "password=" + document.getElementById("password").value;
      document.getElementById("password").value = "";
    }
  </script>
</html>