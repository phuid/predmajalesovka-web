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
  <h2 id="account"></h2>

</body>
<script>
  function accountName() {
    fetch('accountName.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      })
      .then((r) => {
        if (r.status != 200) {
          document.getElementById("account").innerHTML = "Přihlášení se nezdařilo, zadejte heslo a přihlaste se.";
          return
        }
        r.text().then((txt) => {
          document.getElementById("account").innerHTML = "Přihlášený tým: " + txt;
        })
      }).catch(function(err) {
        console.log('Error: ' + err);
      });
  }

  function savePassword() {
    document.cookie = "password=" + document.getElementById("password").value + "; expires=01 May 2024 00:00:00 UTC;";
    document.getElementById("password").value = "";
    accountName();
  }
  accountName();
</script>

</html>