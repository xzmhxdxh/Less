// Navigation & UI Toggles
function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    if (menu.classList.contains('hidden')) {
        menu.classList.remove('hidden');
    } else {
        menu.classList.add('hidden');
    }
}

function toggleSubmenu(id) {
    const submenu = document.getElementById(id);
    const icon = document.getElementById('icon-' + id);
    if (submenu.classList.contains('hidden')) {
        submenu.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        submenu.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}

// Dark Mode Logic
// Check for saved user preference handled in head, this handles toggle
function toggleDarkMode() {
    if (document.documentElement.classList.contains('dark')) {
        document.documentElement.classList.remove('dark');
        localStorage.theme = 'light';
    } else {
        document.documentElement.classList.add('dark');
        localStorage.theme = 'dark';
    }
}

// Search Modal
function toggleSearch() {
    const modal = document.getElementById('search-modal');
    if (!modal) return; // Guard clause

    if (modal.classList.contains('hidden')) {
        // Open
        const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
        document.body.style.paddingRight = `${scrollbarWidth}px`;
        document.body.style.overflow = 'hidden'; // Prevent scrolling
        document.documentElement.style.overflow = 'hidden'; // Prevent scrolling on html
        
        modal.classList.remove('hidden');
        // Small delay to allow display:block to apply before opacity transition
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('div').classList.remove('scale-95');
        }, 10);
    } else {
        // Close
        modal.classList.add('opacity-0');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
            document.body.style.paddingRight = '';
        }, 300);
    }
}

// Initialize Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Search Modal Outside Click
    const searchModal = document.getElementById('search-modal');
    if (searchModal) {
        searchModal.addEventListener('click', function(event) {
            if (event.target === this) {
                toggleSearch();
            }
        });
    }

    // Hero Slider Logic
    const sliderContainer = document.getElementById('hero-slider');
    if (sliderContainer) {
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide-item');
        const dots = document.querySelectorAll('#slider-dots button');
        const totalSlides = slides.length;
        let slideInterval;

        function showSlide(index) {
            // Wrap around index
            if (index >= totalSlides) index = 0;
            if (index < 0) index = totalSlides - 1;
            
            currentSlide = index;

            // Update slides
            slides.forEach((slide, i) => {
                if (i === currentSlide) {
                    slide.classList.remove('opacity-0', 'z-0');
                    slide.classList.add('opacity-100', 'z-10');
                } else {
                    slide.classList.remove('opacity-100', 'z-10');
                    slide.classList.add('opacity-0', 'z-0');
                }
            });

            // Update dots
            if (dots.length > 0) {
                dots.forEach((dot, i) => {
                    if (i === currentSlide) {
                        dot.classList.remove('opacity-50');
                        dot.classList.add('opacity-100');
                    } else {
                        dot.classList.remove('opacity-100');
                        dot.classList.add('opacity-50');
                    }
                });
            }
        }

        function nextSlide() {
            showSlide(currentSlide + 1);
        }

        function startSlideShow() {
            // Clear existing interval to avoid duplicates
            if (slideInterval) clearInterval(slideInterval);
            
            // Get interval from localized script, default to 5000ms
            const interval = (typeof less_ajax !== 'undefined' && less_ajax.hero_interval) ? parseInt(less_ajax.hero_interval) : 5000;
            
            slideInterval = setInterval(nextSlide, interval);
        }

        function stopSlideShow() {
            clearInterval(slideInterval);
        }

        // Global function to be called from HTML onclick if needed
        window.goToSlide = function(index) {
            showSlide(index);
            stopSlideShow();
            startSlideShow(); // Restart timer
        };

        // Initialize
        if (slides.length > 0) {
            startSlideShow();
        }

        // Pause on hover
        sliderContainer.addEventListener('mouseenter', stopSlideShow);
        sliderContainer.addEventListener('mouseleave', startSlideShow);
    }
});

// Post Like Logic (jQuery)
jQuery(document).ready(function($) {
    $('.js-like-post').on('click', function(e) {
        e.preventDefault();
        var $button = $(this);
        var post_id = $button.data('post-id');
        
        // Optional: Check if already liked visually (though backend handles count)
        // if ($button.hasClass('liked')) return;

        $.ajax({
            url: less_ajax.ajax_url,
            type: 'post',
            data: {
                action: 'less_like',
                post_id: post_id,
                nonce: less_ajax.nonce
            },
            success: function(response) {
                // Update all buttons for this post
                var $buttons = $('.js-like-post[data-post-id="' + post_id + '"]');
                
                $buttons.each(function() {
                    var $btn = $(this);
                    $btn.find('.like-count').text(response);
                    
                    // Add liked styling
                    $btn.addClass('liked');
                    
                    // Toggle Icons (for SVG)
                    $btn.find('.icon-regular').addClass('hidden');
                    $btn.find('.icon-solid').removeClass('hidden');
                    
                    // Update text if exists
                    $btn.find('.like-text').text('已赞');
                    
                    // For legacy FontAwesome (if any)
                    $btn.find('i').removeClass('far').addClass('fas');
                });
            }
        });
    });

    // Load More Posts
    $('#load-more-posts').on('click', function(e) {
        e.preventDefault();
        var $button = $(this);
        
        if ($button.hasClass('loading')) return;
        
        var page = $button.data('page');
        var max = $button.data('max');
        var vars = $button.data('vars'); // jQuery automatically parses JSON in data attributes
        
        if (page >= max) return;
        
        $button.addClass('loading').text('加载中...');
        
        $.ajax({
            url: less_ajax.ajax_url,
            type: 'post',
            data: {
                action: 'less_load_more',
                page: page,
                query_vars: JSON.stringify(vars), 
                nonce: less_ajax.nonce
            },
            success: function(response) {
                if (response) {
                    $('#post-list').append(response);
                    $button.data('page', page + 1);
                    $button.removeClass('loading').text('加载更多');
                    
                    if (page + 1 >= max) {
                        $button.parent().remove(); // Remove the button container
                    }
                } else {
                    $button.parent().remove();
                }
            },
            error: function() {
                 $button.removeClass('loading').text('加载更多');
                 // alert('加载失败，请重试'); // Optional: notify user
            }
        });
    });
});
