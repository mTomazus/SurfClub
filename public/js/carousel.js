let currentIndex = 0;
let images = document.querySelectorAll('.carousel-image');
const totalImages = images.length;
const carousel = document.querySelector('.carousel');

if (images === "") {

// Clone first and last images
const firstImageClone = images[1].cloneNode(true);
const lastImageClone = images[images.length - 1].cloneNode(true);
// Add the clones to the beginning and end of the carousel
carousel.appendChild(firstImageClone);
carousel.insertBefore(lastImageClone, images[1]);

// Update the images NodeList after adding the clones
images = document.querySelectorAll('.carousel-image');

// Set up initial position (move to the real first image)
carousel.style.transform = `translateX(-100%)`;

};

// Function to show the slide
function showSlide(index) {
    // Prevent infinite index wrap (e.g., going beyond the bounds)
    currentIndex = index;

    const offset = -(currentIndex + 1) * 100;
    carousel.style.transition = 'transform 0.5s ease-in-out'; // Add the transition back
    carousel.style.transform = `translateX(${offset}%)`;
}

// Function to handle slide change
function changeSlide(direction) {
    currentIndex += direction;
    showSlide(currentIndex);

    // When reaching the first clone (left-most), jump back to the real last image
    if (currentIndex === -1) {
        setTimeout(() => {
            carousel.style.transition = 'none'; // Disable transition for instant jump
            currentIndex = totalImages - 1;
            const offset = -(currentIndex + 1) * 100;
            carousel.style.transform = `translateX(${offset}%)`;
        }, 500); // Match this duration with the CSS transition (0.5s)
    }

    // When reaching the last clone (right-most), jump back to the real first image
    if (currentIndex === totalImages) {
        setTimeout(() => {
            carousel.style.transition = 'none'; // Disable transition for instant jump
            currentIndex = 0;
            const offset = -(currentIndex + 1) * 100;
            carousel.style.transform = `translateX(${offset}%)`;
        }, 500); // Match this duration with the CSS transition (0.5s)
    }
}


