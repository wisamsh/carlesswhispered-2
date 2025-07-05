var right = document.getElementsByClassName("right");
var si = right.length;
var z = 1;
turnRight();
function turnRight() {
  if (si >= 1) {
    si--;
  } else {
    si = right.length - 1;
    function sttmot(i) {
      setTimeout(function () {
        right[i].style.zIndex = "auto";
      }, 300);
    }
    for (var i = 0; i < right.length; i++) {
      right[i].className = "right";
      sttmot(i);
      z = 1;
    }
  }
  right[si].classList.add("flip");
  z++;
  right[si].style.zIndex = z;
}
function turnLeft() {
  if (si < right.length) {
    si++;
  } else {
    si = 1;
    for (var i = right.length - 1; i > 0; i--) {
      right[i].classList.add("flip");
      right[i].style.zIndex = right.length + 1 - i;
    }
  }
  right[si - 1].className = "right";
  setTimeout(function () {
    right[si - 1].style.zIndex = "auto";
  }, 350);
}


let startX = 0;
let endX = 0;

// Detect touch or mouse down
document.querySelector(".container").addEventListener("pointerdown", (e) => {
  startX = e.clientX || e.touches?.[0]?.clientX;
});

// Detect touch or mouse up
document.querySelector(".container").addEventListener("pointerup", (e) => {
  endX = e.clientX || e.changedTouches?.[0]?.clientX;

  if (startX - endX > 50) {
    // swipe left → turn right
    turnRight();
  } else if (endX - startX > 50) {
    // swipe right → turn left
    turnLeft();
  }
});