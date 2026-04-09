<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Wishluv Buildcon</title>
    <?php include "include.php" ?>
    <style>
        .faq-accordion .accordion-item {
            border: none;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-radius: 10px !important;
            overflow: hidden;
        }
        .faq-accordion .accordion-button {
            font-weight: 600;
            color: #333;
            background: #fff;
            padding: 20px;
        }
        .faq-accordion .accordion-button:not(.collapsed) {
            color: var(--Bean-Red);
            background: var(--White-Smoke);
            box-shadow: none;
        }
        .faq-accordion .accordion-button::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ed1b24'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        }
    </style>
</head>

<body>
    <?php include "./navbar.php" ?>
    
    <section class="common-bg">
        <div class="container">
            <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">FAQ</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="common-section">
        <div class="container">
            <div class="about-headline text-center mb-5">
                <h5>Frequently Asked Questions</h5>
                <h3 class="home-title">Your <span>Questions</span> Answered</h3>
                <p class="mt-3">Got questions? We've got answers. Here are some of the most common queries about our projects, buying process, and services.</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="accordion faq-accordion" id="faqAccordion">
                        <!-- Q1 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    What documents are required to buy a plot at Wishluv Buildcon?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    To purchase a plot, you will generally need your PAN card, Aadhaar card, and passport-size photographs. Our team will guide you through the specific documentation required for registration and mutation.
                                </div>
                            </div>
                        </div>

                        <!-- Q2 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Are your projects approved by RERA?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, all our plotting projects are developed in compliance with local regulations and RERA guidelines where applicable. We ensure all paperwork is in order before we open any project for sale.
                                </div>
                            </div>
                        </div>

                        <!-- Q3 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    What kind of development do you provide at the site?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Our projects feature wide black-top roads, street lighting, drainage systems, gated compound walls, and 3 years of maintenance. Specific amenities like parks and rainwater harvesting are also included in most projects.
                                </div>
                            </div>
                        </div>

                        <!-- Q4 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Do you offer home/plot loans?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    While we don't provide loans directly, we have tie-ups with several leading banks and financial institutions to facilitate easy loan processing for our customers.
                                </div>
                            </div>
                        </div>

                        <!-- Q5 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    Can I visit the site before booking?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Absolutely! We encourage all our potential customers to visit the site. You can book a site visit through our contact form or by calling us at +91-9200800600.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-5">
                        <p>Still have more questions?</p>
                        <a href="contact.php" class="btn btn-danger rounded-pill px-5 py-3">Talk to our Experts</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include "footer.php" ?>
    <?php include "vendor.php" ?>
</body>

</html>