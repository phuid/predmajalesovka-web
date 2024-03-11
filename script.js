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
