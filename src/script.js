function toggleVisibility(x) {
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}

function loadAccount() {
  fetch("accountName.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
  })
    .then((r) => {
      adminperm_styletag = document.getElementById("adminperm_styletag");
      if (r.status != 200) {
        document.getElementById("account").innerHTML =
          "<a href='login.php'>Přihlásit se k týmu</a>";
        adminperm_styletag.innerHTML = ".adminperm {display: none;}";
        return;
      }
      r.text().then((txt) => {
        document.getElementById("account").innerHTML =
          "Přihlášený tým: " +
          txt +
          "<br> <a href='login.php'>Změnit přihlášení</a>";
        if (txt == "admin") {
          adminperm_styletag.innerHTML = ".adminperm {display: block;}";
        } else {
          adminperm_styletag.innerHTML = ".adminperm {display: none;}";
        }
        return;
      });
    })
    .catch(function (err) {
      console.log("Error: " + err);
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

var r = document.querySelector(":root");
function resizeHeader() {
  r.style.setProperty("--", "lightblue");
}

function resizeHeaderBg() {
  let id = "#header";
  let header = document.querySelector(id);
  let headerBg = document.querySelector("#header_styletag");

  headerBg.innerHTML =
    id +
    ":before {" +
    "height: " +
    header.clientHeight +
    "px;" +
    "width: " +
    header.clientWidth +
    "px;" +
    "left: " +
    header.offsetLeft +
    "px;" +
    "top: " +
    header.offsetTop +
    "px;" +
    "}";

  // headerBg.style.height = header.clientHeight + "px";
  // headerBg.style.width = header.clientWidth + "px";
  // headerBg.style.left = header.offsetLeft + "px";
  // headerBg.style.top = header.offsetTop + "px";
}

function resize() {
  resizeRoundNicks();
  resizeHeader();
  resizeHeaderBg();
}

window.onresize = resize();
addEventListener("resize", (event) => {
  resize();
});

loadAccount();
