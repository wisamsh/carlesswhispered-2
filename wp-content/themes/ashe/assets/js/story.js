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
const container = document.querySelector(".container");

// Mouse & touch start
container.addEventListener("touchstart", (e) => {
  startX = e.touches[0].clientX;
}, { passive: true });

container.addEventListener("mousedown", (e) => {
  startX = e.clientX;
});

// Mouse & touch end
container.addEventListener("touchend", (e) => {
  endX = e.changedTouches[0].clientX;
  handleSwipe();
});

container.addEventListener("mouseup", (e) => {
  endX = e.clientX;
  handleSwipe();
});

// Swipe logic
function handleSwipe() {
  const deltaX = startX - endX;
  if (deltaX > 50) {
    turnRight(); // swipe left
  } else if (deltaX < -50) {
    turnLeft(); // swipe right
  }
}