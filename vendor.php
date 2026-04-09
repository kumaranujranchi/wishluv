 <!-- <button onclick="topFunction()" id="myBtn" title="Go to top" class="back-to-top"><i class="fa-solid fa-arrow-up"></i></button> -->

 <!-- WhatsApp Floating Widget -->
 <a href="https://wa.me/917280008102?text=Hi%2C%20I%27m%20interested%20in%20Wishluv%20Buildcon%20properties.%20Please%20provide%20more%20information."
    class="whatsapp-float"
    target="_blank"
    rel="noopener noreferrer"
    title="Chat with us on WhatsApp"
    aria-label="Contact us on WhatsApp">
    <i class="fab fa-whatsapp"></i>
 </a>

 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
 <script src="js/owl.carousel.min.js"></script>
 <script src="js/scripts.js"></script>
 <script src="js/wow.min.js"></script>
 <script src="js/main.js"></script>
 <script src="js/touchTouch.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js" integrity="sha512-uURl+ZXMBrF4AwGaWmEetzrd+J5/8NRkWAvJx5sbPSSuOb0bZLqf+tOzniObO00BjHa/dD7gub9oCGMLPQHtQA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

 <!-- WhatsApp Widget JavaScript -->
 <script>
 document.addEventListener('DOMContentLoaded', function() {
     // WhatsApp widget functionality
     const whatsappWidget = document.querySelector('.whatsapp-float');

     if (whatsappWidget) {
         // Add click tracking for analytics
         whatsappWidget.addEventListener('click', function() {
             // Track WhatsApp click event (can be used with Google Analytics)
             if (typeof gtag !== 'undefined') {
                 gtag('event', 'whatsapp_click', {
                     'event_category': 'contact',
                     'event_label': 'whatsapp_widget'
                 });
             }

             // Track with Facebook Pixel if available
             if (typeof fbq !== 'undefined') {
                 fbq('track', 'Contact', {
                     content_name: 'WhatsApp Widget',
                     content_category: 'Contact'
                 });
             }
         });

         // Show widget after page load with animation
         setTimeout(function() {
             whatsappWidget.style.opacity = '0';
             whatsappWidget.style.display = 'flex';
             whatsappWidget.style.transform = 'scale(0.8)';

             setTimeout(function() {
                 whatsappWidget.style.transition = 'all 0.5s ease';
                 whatsappWidget.style.opacity = '1';
                 whatsappWidget.style.transform = 'scale(1)';
             }, 100);
         }, 1000);

         // Hide widget when scrolling to footer (optional)
         let lastScrollTop = 0;
         window.addEventListener('scroll', function() {
             const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
             const windowHeight = window.innerHeight;
             const documentHeight = document.documentElement.scrollHeight;

             // Hide widget when near footer (last 200px)
             if (scrollTop + windowHeight >= documentHeight - 200) {
                 whatsappWidget.style.opacity = '0.7';
             } else {
                 whatsappWidget.style.opacity = '1';
             }

             lastScrollTop = scrollTop;
         });
     }
 });
 </script>