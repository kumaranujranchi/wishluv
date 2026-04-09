<?php
session_start();
$num1 = rand(1, 9);
$num2 = rand(1, 9);
$_SESSION['contact_captcha'] = $num1 + $num2;
$_SESSION['form_load_time'] = time();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Wishluv Buildcon</title>
    <?php include "include.php" ?>
</head>

<body>
    <?php include "./navbar.php" ?>
    
    <section class="common-bg">
        <div class="container">
            <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="common-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="contact-info-card">
                        <div class="about-headline">
                            <h5>Contact Us</h5>
                            <h3 class="home-title"><span>Get In Touch</span> With Us</h3>
                        </div>
                        <p class="mb-4">Have questions about our plots or upcoming projects? Reach out to us, and our team will get back to you shortly.</p>
                        
                        <div class="contact-detail-item d-flex mb-3">
                            <div class="icon-box me-3">
                                <i class="fas fa-map-marker-alt text-danger"></i>
                            </div>
                            <div>
                                <h6>Office Address</h6>
                                <p>Wishluv Buildcon Pvt Ltd, L-3/9 Opp SK Puri Park (Main Gate), SK Puri, Patna-800001</p>
                            </div>
                        </div>

                        <div class="contact-detail-item d-flex mb-3">
                            <div class="icon-box me-3">
                                <i class="fas fa-phone-alt text-danger"></i>
                            </div>
                            <div>
                                <h6>Phone Number</h6>
                                <p><a href="tel:9200800600" class="text-dark text-decoration-none">+91-9200800600</a></p>
                            </div>
                        </div>

                        <div class="contact-detail-item d-flex mb-3">
                            <div class="icon-box me-3">
                                <i class="fas fa-envelope text-danger"></i>
                            </div>
                            <div>
                                <h6>Email Address</h6>
                                <p><a href="mailto:enquiry@wishluvbuildcon.com" class="text-dark text-decoration-none">enquiry@wishluvbuildcon.com</a></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 contact-form-card">
                        <div class="card-body p-4">
                            <form id="contactForm" class="wishluv-form">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                                </div>
                                
                                <!-- Honeypot Field (Hidden from users) -->
                                <div style="display:none;">
                                    <input type="text" name="website" id="website">
                                </div>

                                <div class="mb-3">
                                    <label for="captcha" class="form-label">Security Question: <?php echo "$num1 + $num2 = ?"; ?></label>
                                    <input type="number" class="form-control" id="captcha" name="captcha" placeholder="Enter result" required>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-danger w-100 py-2">Send Message</button>
                                </div>
                                <div id="formResponse" class="mt-3"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="map-section mt-5">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3597.7073655724257!2d85.11014257539553!3d25.614638577444573!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39ed568d83000001%3A0x571117ae6feec0e3!2sWishLuv%20Buildcon%20Pvt.%20Ltd.!5e0!3m2!1sen!2sin!4v1725441783188!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </section>

    <?php include "footer.php" ?>
    <?php include "vendor.php" ?>

    <script>
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const responseDiv = document.getElementById('formResponse');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
        
        fetch('api/contact_form.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                responseDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                this.reset();
            } else {
                responseDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            responseDiv.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again later.</div>';
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Send Message';
        });
    });
    </script>
</body>

</html>