function show(){
  document.querySelector('.hamburger').classList.toggle('open')
  document.querySelector('.navigation').classList.toggle('active')
}

// Sayfa tamamen yüklendiğinde animasyonu başlat
window.addEventListener("load", function () {
  // Hedef elementleri seç
  const headlines = document.querySelector(".hero-page-headlines");
  const callIcon = document.querySelector(".call-icon");

  // Animasyon sınıfını ekle
  headlines.classList.add("animate");
  callIcon.classList.add("animate");
});

 //responsive 1 
 document.addEventListener("DOMContentLoaded", function () {
  // Intersection Observer ayarları
  const observerOptions = {
    root: null, // Tüm görünür alanı izle
    rootMargin: "0px", // Ek bir boşluk bırakma
    threshold: 0.1 // Elementin %10'u göründüğünde tetikleme
  };

  // Observer callback fonksiyonu
  const observerCallback = (entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        // Görünen elemente animasyon sınıfı ekle
        entry.target.classList.add("animate");
        // Bir kez çalıştırmak için observer'dan çıkar
        observer.unobserve(entry.target);
      }
    });
  };

  const observer = new IntersectionObserver(observerCallback, observerOptions);

  // İzlemek istediğiniz elementleri seçin
  const leftContent = document.querySelector(".left-content");
  const rightImage = document.querySelector(".right-image");

  // Observer'a elementleri ekleyin
  observer.observe(leftContent);
  observer.observe(rightImage);
});

 //responsive 1  end
 //Hizmetlerimiz start
 document.addEventListener("DOMContentLoaded", function () {
  const observerOptions = {
    root: null, // Tüm görünür alan
    rootMargin: "0px", // Ekstra boşluk yok
    threshold: 0.1 // Elementin %10'u göründüğünde tetikleme
  };

  const observerCallback = (entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add("visible"); // Görünen elemente animasyon sınıfı ekle
        observer.unobserve(entry.target); // Tekrar tekrar tetiklenmesini önle
      }
    });
  };

  const observer = new IntersectionObserver(observerCallback, observerOptions);

  // Hedef elementleri seç ve observer'a ekle
  const elementsToAnimate = document.querySelectorAll(".animate-on-scroll");
  elementsToAnimate.forEach(el => observer.observe(el));
});

 //Hizmetlerimiz End
 //Responsive 2 Start
 document.addEventListener("DOMContentLoaded", function () {
  const observerOptions = {
    root: null, // Tüm görünür alan
    rootMargin: "0px", // Ekstra boşluk yok
    threshold: 0.1 // Elementin %10'u göründüğünde tetikleme
  };

  const observerCallback = (entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add("animate"); // Animasyon sınıfını ekle
        observer.unobserve(entry.target); // Tekrar tekrar tetiklenmesin
      }
    });
  };

  const observer = new IntersectionObserver(observerCallback, observerOptions);

  // Animasyon yapılacak elementleri seç ve observer'a ekle
  const elementsToAnimate = document.querySelectorAll(".left-content-two, .right-image-two");
  elementsToAnimate.forEach(el => observer.observe(el));
});

 //Responsive 2 End
 //Responsive 3 Start
 // Gözlemci için Intersection Observer API
document.addEventListener("DOMContentLoaded", () => {
  const elements = document.querySelectorAll(".animate-right, .animate-left");

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible");
        }
      });
    },
    { threshold: 0.1 } // Elemanın %10'u görünür olduğunda tetiklenir
  );

  elements.forEach((el) => observer.observe(el));
});

 //Responsive 3 End
document.addEventListener("DOMContentLoaded", function () {
  // Wait for the DOM content to be fully loaded
  // Select elements with the classes
  var heroPageImg = document.getElementsByClassName("hero-page-img");
  var aboutImg = document.getElementsByClassName("about-img");
  var propertyImg = document.getElementsByClassName("property-img");

  // Initialize SimpleParallax on each selected element
  new simpleParallax(heroPageImg, {
    delay: 0.6,
    scale: 1.1,
    orientation: "right",
    transition: "cubic-bezier(0,0,0,1)",
  });
  new simpleParallax(aboutImg, {
    delay: 0.6,
    scale: 1.2,
    transition: "cubic-bezier(0.1,0,0,1)",
  });
  new simpleParallax(propertyImg, {
    delay: 0.6,
    scale: 1.1,
    orientation: "left",
    transition: "cubic-bezier(0,0,0,1)",
  });
});
//Footer start
// Footer Animasyonları için Intersection Observer API
document.addEventListener("DOMContentLoaded", () => {
  const footerElements = document.querySelectorAll(".animate-up");

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible");
        }
      });
    },
    { threshold: 0.1 } // Elemanın %10'u görünür olduğunda tetiklenir
  );

  footerElements.forEach((el) => observer.observe(el));
});

//Footer end
// Swipper
const swiper = new Swiper(".swiper", {
  // Optional parameters
  slidesPerView: 3,
  direction: "horizontal",
  lazyLoading: true,
  spaceBetween: 7,
  grabCursor: false,
  centeredSlides: true,
  loop: true,
  keyboard: {
    enabled: true,
  },
  // Responsive breakpoints
  breakpoints: {
    320: {
      slidesPerView: 1,
      spaceBetween: 30,
    },
    768: {
      slidesPerView: 1.5,
      spaceBetween: 10,
    },
    1024: {
      slidesPerView: 3,
      spaceBetween: 10,
    },
    1440: {
      slidesPerView: 3,
      spaceBetween: 10,
    },
  },

  // Navigation arrows
  navigation: {
    nextEl: ".swiper-custom-nav-next",
    prevEl: ".swiper-custom-nav-prev",
  },
});

function toggleAccordion(accordionId) {
  var accordionItem = document.getElementById(accordionId);
  var answer = accordionItem.querySelector(".answer");
  var arrow = accordionItem.querySelector(".fa-arrow-up");

  if (accordionItem.classList.contains("active")) {
    accordionItem.classList.remove("active");
    answer.style.maxHeight = "0";
    arrow.style.transform = "rotate(0deg)";
  } else {
    accordionItem.classList.add("active");
    answer.style.maxHeight = answer.scrollHeight + "px";
    arrow.style.transform = "rotate(180deg)";
  }
}

// Scroll Reveal
const sr = ScrollReveal({
  origin: "bottom",
  distance: "60px",
  duration: 1000,
  delay: 100,
  easing: "ease-in-out",
});

sr.reveal("#hero-slogan");
sr.reveal("#hero-title", { delay: 400 });
sr.reveal("#hero-description", { delay: 500 });
sr.reveal("#hero-btn", { delay: 600 });
sr.reveal(".hero-page-imgs", { delay: 700 });
sr.reveal(".collab");
sr.reveal(".about-imgs");
sr.reveal(".about-headlines", { delay: 400 });
sr.reveal(".about-projects", { delay: 500 });
sr.reveal(".services");
sr.reveal("#properties-title");
sr.reveal("#properties-description", { delay: 400 });
sr.reveal(".swiper", { delay: 500 });
sr.reveal(".faq-header");
sr.reveal(".property-page");
sr.reveal(".service-page-title");
sr.reveal(".service-page-container", { delay: 600 });
sr.reveal(".contact-headline");
sr.reveal(".contact-content", { delay: 600 });

// FAQ Questions Reveal
const delayIncrement = 100; // Delay increment in milliseconds
const totalQuestions = 5; // Number of questions

for (let i = 1; i <= totalQuestions; i++) {
  const selector = `#question${i}`;
  const delay = 400 + i * delayIncrement;

  sr.reveal(selector, { delay });
}

//Menu Açma Kapama
//javascript for navigation bar effect on scroll
window.addEventListener("scroll", function () {
  var header = document.querySelector("header");
  header.classList.toggle("sticky", window.scrollY > 0);
});

//javascript for responsive navigation sidebar menu
var menu = document.querySelector(".menu");
var menuBtn = document.querySelector(".menu-btn");
var closeBtn = document.querySelector(".close-btn");

menuBtn.addEventListener("click", () => {
  menu.classList.add("active");
});

closeBtn.addEventListener("click", () => {
  menu.classList.remove("active");
});

//Menu Açma Kapama 

