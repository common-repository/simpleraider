/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
 
wmtt = null;
document.onmousemove = updateWMTT;
function updateWMTT(e) {
  if (wmtt != null) {
    x = (document.all) ? window.event.x + wmtt.offsetParent.scrollLeft : e.pageX;
    y = (document.all) ? window.event.y + wmtt.offsetParent.scrollTop  : e.pageY;
    wmtt.style.left = (x+10) + "px";
    wmtt.style.top   = (y+10) + "px";
  }
}
function showWMTT(id) {
  wmtt = document.getElementById(id);
  wmtt.style.display = "block"
}
function hideWMTT() {
  wmtt.style.display = "none";
}
