/* --------------------------------- Search Bar ------------------------------------------------- */
const topMenubar = document.querySelector('.top-menubar');
    const searchIcon = document.querySelector('.search-icon');
    const searchContainer = document.querySelector('.search-container');

    function toggleSearch() {
        topMenubar.classList.toggle('search-active');
        searchContainer.classList.toggle('active');
        searchIcon.classList.toggle('hidden');
    }

    document.addEventListener('click', (event) => {
        if (!topMenubar.contains(event.target) && searchContainer.classList.contains('active')) {
            topMenubar.classList.remove('search-active');
            searchContainer.classList.remove('active');
            searchIcon.classList.remove('hidden');
        }
    });

/* --------------------------------- HOME IMAGES SLIDER ------------------------------------------------- */

document.addEventListener('DOMContentLoaded', () => {
  let currentSlide = 0;
  const slides = document.querySelectorAll('.slide');
  const totalSlides = slides.length;
  const indicatorsContainer = document.querySelector('.slide-indicators');

  // Create indicators dynamically
  for (let i = 0; i < totalSlides; i++) {
      const indicator = document.createElement('div');
      indicator.classList.add('indicator');
      if (i === 0) indicator.classList.add('active');
      indicator.addEventListener('click', () => {
          currentSlide = i;
          updateSlider();
      });
      indicatorsContainer.appendChild(indicator);
  }

  const indicators = document.querySelectorAll('.indicator');

  function updateSlider() {
      const translateValue = -currentSlide * 100 + '%';
      document.querySelector('.slider').style.transform = 'translateX(' + translateValue + ')';
      updateIndicators();
  }

  function updateIndicators() {
      indicators.forEach((indicator, index) => {
          indicator.classList.toggle('active', index === currentSlide);
      });
  }

  document.querySelector('.slider-arrow-left').addEventListener('click', () => {
      currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
      updateSlider();
  });

  document.querySelector('.slider-arrow-right').addEventListener('click', () => {
      currentSlide = (currentSlide + 1) % totalSlides;
      updateSlider();
  });

  // Auto slide every 5 seconds
  setInterval(() => {
      currentSlide = (currentSlide + 1) % totalSlides;
      updateSlider();
  }, 5000);
});



/* ---------------------------------------- OFFERS IMAGES SLIDER ----------------------------------------- */
// Slider JavaScript (unchanged)
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
setInterval(nextOfferSlide, 3000); // Auto slide every 3 seconds

/* ---------------------------------------- Staff card ----------------------------------------- */
const staffCards = document.querySelectorAll('.staff-card');

staffCards.forEach((card) => {
    card.addEventListener('click', () => {
        const staffName = card.querySelector('h2').innerText;
        const staffRole = card.querySelector('.role').innerText;

        alert(`Staff Name: ${staffName}\nRole: ${staffRole}`);
        // You can replace this with a modal or detailed page navigation
    });
});

/* ---------------------------------------- Reviews ----------------------------------------- */
// Review data
const reviews = [
  {
      name: "John",
      image: "https://via.placeholder.com/100x100",
      rating: 5,
      review: "The food was absolutely amazing, and the service was top-notch. Highly recommend!"
  },
  {
      name: "Emily",
      image: "https://via.placeholder.com/100x100",
      rating: 4,
      review: "A beautiful ambiance and delicious menu. Great place for a family dinner."
  },
  {
      name: "Michael",
      image: "https://via.placeholder.com/100x100",
      rating: 5,
      review: "Excellent service and the rooms were luxurious and comfortable. Will visit again!"
  },
  {
      name: "Sarah",
      image: "https://via.placeholder.com/100x100",
      rating: 3,
      review: "Good experience overall, but the wait time could be improved."
  }
];

// Generate review cards
const reviewsGrid = document.getElementById("reviews-grid");

reviews.forEach(({ name, image, rating, review }) => {
  // Create star rating dynamically
  const stars = Array(5)
      .fill('<i class="fa-regular fa-star"></i>')
      .map((star, index) =>
          index < rating ? '<i class="fa-solid fa-star"></i>' : star
      )
      .join("");

  // Create review card
  const reviewCard = `
      <div class="review-card">
          <div class="reviewer-image">
              <img src="${image}" alt="${name}">
          </div>
          <div class="review-content">
              <div class="review-stars">${stars}</div>
              <p class="review-text">"${review}"</p>
              <h3 class="reviewer-name">${name}</h3>
          </div>
      </div>
  `;

  reviewsGrid.insertAdjacentHTML("beforeend", reviewCard);
});


/* -------------------------------- GO UP BUTTON ----------------------------------- */
function scrollToTop() {
  window.scrollTo({
      top: 0,
      behavior: "smooth"
  });
}

window.onscroll = function () {
  var goUpButton = document.querySelector('.scroll-to-top');
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


/* Menu Page JS */


/* Booking page js */
if (window.location.pathname === '/booking.php') {
  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});
}
