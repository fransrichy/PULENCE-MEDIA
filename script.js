// This script provides functionality for the Galaxy Printing website,
// including dynamic content updates and animated counters.

document.addEventListener('DOMContentLoaded', () => {

    // Set the current year in the footer
    // This function automatically updates the copyright year.
    const currentYear = new Date().getFullYear();
    const yearSpan = document.getElementById('current-year');
    if (yearSpan) {
        yearSpan.textContent = currentYear;
    }

    // Function for the animated number counters on the projects page.
    // This creates a smooth counting effect when the user scrolls to the section.
    function startCounters() {
        const counters = document.querySelectorAll('.counter');
        const speed = 200; // The lower the speed, the faster the animation

        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText;

                // Calculate the increment value
                const inc = target / speed;

                if (count < target) {
                    // Add the increment to the current count and update the inner text
                    counter.innerText = Math.ceil(count + inc);
                    // Use a timeout to create the animation loop
                    setTimeout(updateCount, 1);
                } else {
                    counter.innerText = target;
                }
            };

            updateCount();
        });
    }

    // Intersection Observer to start the counter animation when in view.
    // This is more efficient than a scroll event listener.
    const countersSection = document.querySelector('.counters-section');
    if (countersSection) {
        const observerOptions = {
            root: null, // use the viewport as the root
            rootMargin: '0px',
            threshold: 0.5 // trigger when 50% of the section is visible
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    startCounters();
                    // Stop observing once the animation has started
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        observer.observe(countersSection);
    }
});
