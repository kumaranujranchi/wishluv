<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plot Services - Wishluv Buildcon</title>
    <?php include "include.php" ?>
    <style>
        .service-feature-card {
            border-bottom: 4px solid #eee;
            transition: all 0.3s ease;
        }
        .service-feature-card:hover {
            border-bottom-color: var(--Bean-Red);
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
        }
        .service-icon {
            font-size: 2.5rem;
            color: var(--Bean-Red);
            margin-bottom: 20px;
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
                    <li class="breadcrumb-item active" aria-current="page">Plot Services</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="common-section">
        <div class="container">
            <div class="about-headline text-center mb-5">
                <h5>Our Expertise</h5>
                <h3 class="home-title">Comprehensive <span>Plotting Services</span></h3>
                <p class="mt-3">We provide end-to-end solutions for residential and commercial land development. From strategic land acquisition to world-class infrastructure, we ensure every plot we deliver is a foundation for your dreams.</p>
            </div>

            <div class="row">
                <!-- Service 1 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card service-feature-card border-0 shadow-sm h-100 p-4">
                        <div class="service-icon"><i class="fas fa-search-location"></i></div>
                        <h4>Land Acquisition</h4>
                        <p class="text-muted">We identify and acquire land in high-growth corridors. Our team performs strict due diligence to ensure clear titles and legal compliance.</p>
                    </div>
                </div>

                <!-- Service 2 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card service-feature-card border-0 shadow-sm h-100 p-4">
                        <div class="service-icon"><i class="fas fa-drafting-compass"></i></div>
                        <h4>Strategic Planning</h4>
                        <p class="text-muted">Meticulous master planning that incorporates wide roads, green spaces, and efficient drainage systems to create livable communities.</p>
                    </div>
                </div>

                <!-- Service 3 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card service-feature-card border-0 shadow-sm h-100 p-4">
                        <div class="service-icon"><i class="fas fa-road"></i></div>
                        <h4>Infrastructure Development</h4>
                        <p class="text-muted">Developing world-class infrastructure including black-top roads, street lighting, and secure gated perimeters for all our projects.</p>
                    </div>
                </div>

                <!-- Service 4 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card service-feature-card border-0 shadow-sm h-100 p-4">
                        <div class="service-icon"><i class="fas fa-tools"></i></div>
                        <h4>3-Year Maintenance</h4>
                        <p class="text-muted">We take care of the project infrastructure for 3 years post-completion, ensuring the community remains pristine and well-managed.</p>
                    </div>
                </div>

                <!-- Service 5 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card service-feature-card border-0 shadow-sm h-100 p-4">
                        <div class="service-icon"><i class="fas fa-file-contract"></i></div>
                        <h4>Documentation Support</h4>
                        <p class="text-muted">Facilitating smooth registration and mutation processes. Our legal team assists every customer with the paperwork needed for land ownership.</p>
                    </div>
                </div>

                <!-- Service 6 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card service-feature-card border-0 shadow-sm h-100 p-4">
                        <div class="service-icon"><i class="fas fa-hand-holding-usd"></i></div>
                        <h4>Investment Consulting</h4>
                        <p class="text-muted">Helping our clients choose locations with the highest potential for appreciation, making land ownership a profitable investment.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include "footer.php" ?>
    <?php include "vendor.php" ?>
</body>

</html>