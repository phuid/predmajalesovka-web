function toggleVisibility(x) {
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}

function loadAccount() {
  fetch('accountName.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    }
  })
  .then((r) => {
    if (r.status != 200) {
      document.getElementById("account").innerHTML = "<a href='login.php'>Přihlásit se k týmu</a>";
      return;
    }
    r.text().then((txt) => {
      document.getElementById("account").innerHTML = "Přihlášený tým: " + txt + "<br> <a href='login.php'>Změnit přihlášení</a>";
      return;
    })
  }).catch(function(err) {
    console.log('Error: ' + err);
  });
}

function resizeRoundNicks() {
  var roundNicks = document.getElementsByClassName("round_nick");
  for (var i = 0; i < roundNicks.length; i++) {
    var nick = roundNicks[i];
    var nickWidth = nick.children[0].clientHeight;
    var nickHeight = nick.children[0].clientWidth;
    nick.style.width = nickWidth + "px";
    nick.style.transform =
      "translate(-" + (nickHeight / 2 - nickWidth / 2) + "px, 0)";
  }
}

var r = document.querySelector(':root');
function resizeHeader() {
  r.style.setProperty('--', 'lightblue');
}

function resize() {
  resizeRoundNicks();
  resizeHeader();
}

window.onresize = resize();

loadAccount();