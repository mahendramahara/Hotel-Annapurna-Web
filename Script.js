/* --------------------------------- HOME IMAGES SLIDER ------------------------------------------------- */

let currentSlide = 0;
const slides = document.querySelectorAll('.slide');
const totalSlides = slides.length;
const indicatorsContainer = document.querySelector('.slide-indicators');

for (let i = 0; i < totalSlides; i++) {
  const indicator = document.createElement('div');
  indicator.classList.add('indicator');
  indicatorsContainer.appendChild(indicator);
}

const indicators = document.querySelectorAll('.indicator');
updateIndicators();

function updateIndicators() {
  indicators.forEach((indicator, index) => {
    indicator.classList.toggle('active', index === currentSlide);
  });
}

function nextSlide() {
  currentSlide = (currentSlide + 1) % totalSlides;
  updateSlider();
}

function prevSlide() {
  currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
  updateSlider();
}

function updateSlider() {
  const translateValue = -currentSlide * 100 + '%';
  document.querySelector('.slider').style.transform = 'translateX(' + translateValue + ')';
  updateIndicators();
}

setInterval(nextSlide, 5000); // Auto slide every 5 seconds


/* ---------------------------------------- OFFERS IMAGES SLIDER ----------------------------------------- */

const offerSlider = document.querySelector('.offer-images-collection');
const offerSlides = document.querySelectorAll('.offerSlide');
const offerIndicatorsContainer = document.querySelector('.offerSlider-indicators');

let offerCurrentIndex = 0;

function updateOfferSlider() {
  offerSlider.style.transform = `translateX(${-offerCurrentIndex * 100}%)`;

  const offerIndicators = Array.from(offerIndicatorsContainer.children);
  offerIndicators.forEach((indicator, index) => {
    indicator.classList.toggle('active', index === offerCurrentIndex);
  });
}

function nextOfferSlide() {
  offerCurrentIndex = (offerCurrentIndex + 1) % offerSlides.length;
  updateOfferSlider();
}

function prevOfferSlide() {
  offerCurrentIndex = (offerCurrentIndex - 1 + offerSlides.length) % offerSlides.length;
  updateOfferSlider();
}

function createOfferIndicators() {
  offerSlides.forEach((_, index) => {
    const indicator = document.createElement('div');
    indicator.classList.add('offerSlider-indicator');
    indicator.addEventListener('click', () => {
      offerCurrentIndex = index;
      updateOfferSlider();
    });
    offerIndicatorsContainer.appendChild(indicator);
  });
}

createOfferIndicators();
setInterval(nextOfferSlide, 3000); // Auto slide in 3 seconds


/* -------------------------------- GO UP BUTTON ----------------------------------- */

function scrollToTop() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}

window.onscroll = function () {
  var goUpButton = document.querySelector('.go-up');
  if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
    goUpButton.style.display = 'block';
  } else {
    goUpButton.style.display = 'none';
  }
};
/* ----------------------- For Rooms OnClick Redirection -------------------------- */
function viewRooms(loc) {
  window.location.href = loc;
}
/* ----------------------- For Blog OnClick Redirection -------------------------- */
function redirectToBlog(url) {
  window.location.href = url;
}