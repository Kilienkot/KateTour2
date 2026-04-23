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

  // Проверяем мобильное устройство
  const isMobile = window.matchMedia("(max-width: 768px)").matches;

  // Определяем максимальное расстояние, на котором будет происходить анимация
  const triggerPoint = 200; // px
  const progress = Math.min(scrollY / triggerPoint, 1); // значение от 0 до 1

  // Вычисляем параметры для анимации в зависимости от устройства
  let height;
  if (isMobile) {
    height = Math.max(60, 100 - progress * 25);
  } else {
    height = Math.max(80, 100 - progress * 35);
  }

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

//--------------------------------------------------------------------------------------------------------------------------------------------------------------------
//  Бургер меню для мобильной версии
const burgerButton = document.querySelector(".burger-button");
const headerLi = document.querySelectorAll("header li");

burgerButton?.addEventListener("click", () => {
  burgerButton.classList.toggle("active");
  const isActive = burgerButton.classList.contains("active");
  headerLi.forEach((li) => (li.style.display = isActive ? "flex" : ""));
});

//--------------------------------------------------------------------------------------------------------------------------------------------------------------------
//вывод сообщения при авторизации

// Проверяем наличие плашки
const errorMsg = document.querySelector(".error-message");
if (errorMsg) {
  console.log("Error message shown");
  // Показываем с анимацией
  setTimeout(() => {
    errorMsg.classList.add("show");
  }, 100); // Небольшая задержка для применения transition
  // Скрываем плашку через 5 секунд
  setTimeout(() => {
    errorMsg.classList.remove("show");
    // После анимации скрытия убираем display
    setTimeout(() => {
      errorMsg.style.display = "none";
    }, 500); // Время transition
  }, 2000);
}

// Проверяем наличие успешного сообщения
const successMsg = document.querySelector(".success-message");
if (successMsg) {
  console.log("Success message shown");
  // Показываем с анимацией
  setTimeout(() => {
    successMsg.classList.add("show");
  }, 100); // Небольшая задержка для применения transition
  // Скрываем плашку через 5 секунд
  setTimeout(() => {
    successMsg.classList.remove("show");
    // После анимации скрытия убираем display
    setTimeout(() => {
      successMsg.style.display = "none";
    }, 500); // Время transition
  }, 3000);
}

//--------------------------------------------------------------------------------------------------------------------------------------------------------------------
//фильтры в календаре
let activeFilter = null;

function showAllTrips() {
  const trips = document.querySelectorAll(".calendar__card");
  trips.forEach((trip) => {
    trip.style.display = "flex";
  });
}

function hideFilterButtons() {
  document.getElementById("savelong").classList.remove("active");
  document.getElementById("savesmall").classList.remove("active");
}

document.getElementById("savelong").addEventListener("click", function () {
  if (activeFilter === "long") {
    showAllTrips();
    hideFilterButtons();
    activeFilter = null;
    return;
  }
  hideFilterButtons();
  this.classList.add("active");
  activeFilter = "long";
  const trips = document.querySelectorAll(".calendar__card");
  trips.forEach((trip) => {
    if (!trip.classList.contains("long-trip")) {
      trip.style.display = "none";
    } else {
      trip.style.display = "flex";
    }
  });
});

document.getElementById("savesmall").addEventListener("click", function () {
  if (activeFilter === "small") {
    showAllTrips();
    hideFilterButtons();
    activeFilter = null;
    return;
  }
  hideFilterButtons();
  this.classList.add("active");
  activeFilter = "small";
  const trips = document.querySelectorAll(".calendar__card");
  trips.forEach((trip) => {
    if (!trip.classList.contains("small-trip")) {
      trip.style.display = "none";
    } else {
      trip.style.display = "flex";
    }
  });
});

//--------------------------------------------------------------------------------------------------------------------------------------------------------------------
//скрытие текста в карточке

// document.addEventListener("DOMContentLoaded", function () {
//   // Обработка кнопки "Показать всё"
//   const showMoreBtns = document.querySelectorAll(".show-more-btn");

//   showMoreBtns.forEach((btn) => {
//     btn.addEventListener("click", function () {
//       const wrapper = this.closest(".description-wrapper");
//       const shortBlock = wrapper.querySelector(".description-short");
//       const fullBlock = wrapper.querySelector(".description-full");
//       const fullText = this.getAttribute("data-full-text");

//       // Заполняем полный блок текстом
//       const fullTextP = fullBlock.querySelector("p");
//       fullTextP.innerHTML = nl2br(escapeHtml(fullText));

//       // Переключаем видимость
//       shortBlock.style.display = "none";
//       fullBlock.style.display = "block";
//     });
//   });

//   // Обработка кнопки "Скрыть"
//   const showLessBtns = document.querySelectorAll(".show-less-btn");

//   showLessBtns.forEach((btn) => {
//     btn.addEventListener("click", function () {
//       const wrapper = this.closest(".description-wrapper");
//       const shortBlock = wrapper.querySelector(".description-short");
//       const fullBlock = wrapper.querySelector(".description-full");

//       fullBlock.style.display = "none";
//       shortBlock.style.display = "block";

//       // Прокрутка к началу текста
//       wrapper.scrollIntoView({ behavior: "smooth", block: "start" });
//     });
//   });

//   // Вспомогательные функции
//   function escapeHtml(text) {
//     const div = document.createElement("div");
//     div.textContent = text;
//     return div.innerHTML;
//   }

//   function nl2br(text) {
//     return escapeHtml(text).replace(/\n/g, "<br>");
//   }
// });
