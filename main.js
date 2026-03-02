//Гармошка "подробнее"
var acc = document.getElementsByClassName("program__item-more");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function () {
    this.classList.toggle("active");

    var panel = this.nextElementSibling;
    if (panel.style.display === "block") {
      panel.style.display = "none";
    } else {
      panel.style.display = "block";
    }
  });
}

// Функция для обработки скролла и изменения размеров main_img
let ticking = false;

function updateMainImg(scrollY) {
  const mainImg = document.querySelector(".main_img");
  if (!mainImg) return;

  // Определяем максимальное расстояние, на котором будет происходить анимация
  const triggerPoint = 200; // px
  const progress = Math.min(scrollY / triggerPoint, 1); // значение от 0 до 1

  // Вычисляем параметры для анимации
  const height = Math.max(80, 100 - progress * 35); // от 100dvh до 30dvh
  const scale = 1 - progress * 0.1; // масштаб от 1 до 0.7
  const opacity = progress; // для затемнения

  // Применяем изменения с использованием transform для лучшей производительности
  mainImg.style.height = `${height}vh`;
  mainImg.style.transform = `scale(${scale})`;
  mainImg.style.willChange = "transform, height";

  // Обновляем переменную для градиента
  mainImg.style.setProperty("--overlay-opacity", opacity.toFixed(2));
}

function requestTick() {
  if (!ticking) {
    requestAnimationFrame(function () {
      updateMainImg(window.scrollY);
      ticking = false;
    });
    ticking = true;
  }
}

window.addEventListener("scroll", function () {
  requestTick();
});

// Инициализация при загрузке
document.addEventListener("DOMContentLoaded", function () {
  updateMainImg(window.scrollY);
});
