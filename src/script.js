function loadAccount() {
  console.log("Loading account");
  var account = document.getElementById("account");
  var password = document.cookie.replace(/(?:(?:^|.*;\s*)password\s*\=\s*([^;]*).*$)|^.*$/, "$1");

  if (password) {
    if (accountName) {
      console.log("Account name found");
      account.innerHTML = "Účet:" + accountName + ", <a href=\"login.php\">Změnit přihlášení</a>";
    }
    else {
      console.log("No account name found");
      account.innerHTML = "Přihlášení se nezdařilo, <a href=\"login.php\">zkuste to znovu.</a>";
    }
  } else {
    console.log("No password found");
    account.innerHTML = "<a href=\"login.php\">Příhlásit se</a>";
  }
}

function resizeRoundNicks() {
  var roundNicks = document.getElementsByClassName("round_nick");
  for (var i = 0; i < roundNicks.length; i++) {
    var nick = roundNicks[i];
    var nickWidth = nick.children[0].clientHeight;
    var nickHeight = nick.children[0].clientWidth;
    nick.style.width = nickWidth + "px";
    nick.style.transform = "translate(-" + (nickHeight / 2 - nickWidth / 2) + "px, 0)";
  }
}

function resize() {
  resizeRoundNicks();
}

window.onresize = resize();

loadAccount();