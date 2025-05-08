
document.addEventListener('DOMContentLoaded', function() {
    // Get carousel elements
    const carouselTrack = document.getElementById('carouselTrack');
    const carouselDots = document.getElementById('carouselDots');
    
    if (!carouselTrack) {
        console.error('Carousel track element not found');
        return;
    }
    
    // Function to load dynamic content
    function loadDynamicContent() {
        // Fetch the JSON content from the PHP endpoint
        fetch('get_carousel_content.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (!Array.isArray(data) || data.length === 0) {
                    console.log('No dynamic content to display');
                    return;
                }
                
                console.log('Loaded dynamic content:', data.length, 'items');
                
                // Only add the dynamic content after existing static slides
                data.forEach((item, index) => {
                    // Create new carousel slide
                    const slide = document.createElement('figure');
                    slide.className = 'carousel-slide';
                    
                    // Create image element
                    const img = document.createElement('img');
                    img.src = item.image;
                    img.alt = item.title || '';
                    img.loading = 'lazy';
                    
                    // Create figcaption with content
                    const figcaption = document.createElement('figcaption');
                    
                    // Add title
                    const title = document.createElement('h2');
                    title.textContent = item.title || '';
                    
                    // Add description
                    const description = document.createElement('p');
                    description.textContent = item.description || '';
                    
                    // Add button with link if provided
                    const button = document.createElement('button');
                    button.className = 'read-more';
                    button.textContent = 'Read more';
                    
                    if (item.link) {
                        button.classList.add('open-overlay');
                        button.addEventListener('click', function() {
                            window.location.href = item.link;
                        });
                    }
                    
                    // Assemble the slide
                    figcaption.appendChild(title);
                    figcaption.appendChild(description);
                    figcaption.appendChild(button);
                    
                    slide.appendChild(img);
                    slide.appendChild(figcaption);
                    
                    // Add the slide to the carousel
                    carouselTrack.appendChild(slide);
                });
                
                // Update carousel dots and navigation
                updateCarouselDots();
                updateCarouselNavigation();
            })
            .catch(error => {
                console.error('Error loading dynamic content:', error);
            });
    }
    
    // Function to update carousel dots
    function updateCarouselDots() {
        if (!carouselDots) return;
        
        // Clear existing dots
        carouselDots.innerHTML = '';
        
        // Count slides
        const slides = document.querySelectorAll('.carousel-slide');
        
        // Create dots for each slide
        slides.forEach((_, index) => {
            const dot = document.createElement('button');
            dot.className = 'carousel-dot';
            dot.setAttribute('aria-label', `Go to slide ${index + 1}`);
            dot.dataset.slideIndex = index.toString();
            
            // Add event listener to navigate to slide when dot is clicked
            dot.addEventListener('click', function() {
                navigateToSlide(index);
            });
            
            carouselDots.appendChild(dot);
        });
        
        // Highlight the current dot
        updateActiveDot();
    }
    
    // Function to update the active dot
    function updateActiveDot() {
        const currentIndex = getCurrentSlideIndex();
        const dots = document.querySelectorAll('.carousel-dot');
        
        dots.forEach((dot, index) => {
            if (index === currentIndex) {
                dot.classList.add('active');
                dot.setAttribute('aria-current', 'true');
            } else {
                dot.classList.remove('active');
                dot.removeAttribute('aria-current');
            }
        });
    }
    
    // Function to get current slide index
    function getCurrentSlideIndex() {
        // This function should return the index of the currently visible slide
        const slides = document.querySelectorAll('.carousel-slide');
        for (let i = 0; i < slides.length; i++) {
            if (slides[i].classList.contains('active')) {
                return i;
            }
        }
        return 0; // Default to first slide if none are active
    }
    
    // Function to navigate to a specific slide
    function navigateToSlide(index) {
        // Implement slide navigation based on your carousel's logic
        console.log('Navigate to slide:', index);
        
        // This is a placeholder. Replace with your actual carousel navigation code
        const slides = document.querySelectorAll('.carousel-slide');
        
        // Remove active class from all slides
        slides.forEach(slide => slide.classList.remove('active'));
        
        // Add active class to target slide
        if (slides[index]) {
            slides[index].classList.add('active');
        }
        
        // Update the active dot
        updateActiveDot();
    }
    
    // Function to update carousel navigation buttons
    function updateCarouselNavigation() {
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        
        if (prevBtn && nextBtn) {
            // Ensure navigation buttons handle all slides including the dynamic ones
            prevBtn.addEventListener('click', function() {
                navigateToPrevSlide();
            });
            
            nextBtn.addEventListener('click', function() {
                navigateToNextSlide();
            });
        }
    }
    
    // Function to navigate to previous slide
    function navigateToPrevSlide() {
        const currentIndex = getCurrentSlideIndex();
        const slides = document.querySelectorAll('.carousel-slide');
        
        let newIndex = currentIndex - 1;
        if (newIndex < 0) {
            newIndex = slides.length - 1; // Loop to last slide
        }
        
        navigateToSlide(newIndex);
    }
    
    // Function to navigate to next slide
    function navigateToNextSlide() {
        const currentIndex = getCurrentSlideIndex();
        const slides = document.querySelectorAll('.carousel-slide');
        
        let newIndex = currentIndex + 1;
        if (newIndex >= slides.length) {
            newIndex = 0; // Loop to first slide
        }
        
        navigateToSlide(newIndex);
    }
    
    // Load dynamic content when the page loads
    loadDynamicContent();
});
