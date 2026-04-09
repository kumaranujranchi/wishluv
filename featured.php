<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Featured Projects - Wishluv Buildcon</title>
    <?php include "include.php" ?>
    <style>
        .project-card {
            overflow: hidden;
            border-radius: 15px;
            transition: all 0.4s ease;
            position: relative;
        }
        .project-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
        }
        .project-img-wrapper {
            position: relative;
            overflow: hidden;
            height: 250px;
        }
        .project-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .project-card:hover .project-img-wrapper img {
            transform: scale(1.1);
        }
        .project-status {
            position: absolute;
            top: 20px;
            right: 20px;
            background: var(--Bean-Red);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            z-index: 10;
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
                    <li class="breadcrumb-item active" aria-current="page">Featured Projects</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="common-section">
        <div class="container">
            <div class="about-headline text-center mb-5">
                <h5>Our Portfolio</h5>
                <h3 class="home-title"><span>Featured</span> Projects</h3>
                <p class="mt-3">Discover our premium residential and commercial developments. Every project is a testament to our commitment to quality, smart planning, and customer satisfaction.</p>
            </div>

            <div class="row">
                <!-- Project 1 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card project-card border-0 shadow-sm h-100">
                        <div class="project-status">Ongoing</div>
                        <div class="project-img-wrapper">
                            <img src="./images/featured-1.webp" alt="Officers Colony">
                        </div>
                        <div class="card-body p-4">
                            <h4 class="mb-2">Officers Colony</h4>
                            <p class="text-danger small mb-3"><i class="fas fa-map-marker-alt me-1"></i> Chiraura, Patna</p>
                            <p class="text-muted small">A premium residential enclave designed for the modern lifestyle. Offering secure, well-planned plots with state-of-the-art amenities.</p>
                            <a href="officers-colony.php" class="btn btn-outline-danger btn-sm rounded-pill px-4 mt-2">View Details</a>
                        </div>
                    </div>
                </div>

                <!-- Project 2 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card project-card border-0 shadow-sm h-100">
                        <div class="project-status">Ongoing</div>
                        <div class="project-img-wrapper">
                            <img src="./images/featured-2.webp" alt="Crystal City">
                        </div>
                        <div class="card-body p-4">
                            <h4 class="mb-2">Crystal City</h4>
                            <p class="text-danger small mb-3"><i class="fas fa-map-marker-alt me-1"></i> Naubatpur, Patna</p>
                            <p class="text-muted small">Meticulously planned township offering premium residential plots in one of Patna's fastest-growing areas.</p>
                            <a href="https://crystalcity.wishluvbuildcon.com/" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill px-4 mt-2">View Website</a>
                        </div>
                    </div>
                </div>

                <!-- Project 3 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card project-card border-0 shadow-sm h-100">
                        <div class="project-status">Ongoing</div>
                        <div class="project-img-wrapper">
                            <img src="./images/featured-3.webp" alt="Wish Town">
                        </div>
                        <div class="card-body p-4">
                            <h4 class="mb-2">Wish Town</h4>
                            <p class="text-danger small mb-3"><i class="fas fa-map-marker-alt me-1"></i> Patut Bihta, Patna</p>
                            <p class="text-muted small">Experience the perfect blend of modern comfort and natural beauty at Wish Town. A sanctuary in the thriving Bihta region.</p>
                            <a href="wishluv-town.php" class="btn btn-outline-danger btn-sm rounded-pill px-4 mt-2">View Details</a>
                        </div>
                    </div>
                </div>

                <!-- Project 4 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card project-card border-0 shadow-sm h-100">
                        <div class="project-status bg-success">Completed</div>
                        <div class="project-img-wrapper">
                            <img src="./images/complete-1.webp" alt="Wishluv City">
                        </div>
                        <div class="card-body p-4">
                            <h4 class="mb-2">Wishluv City</h4>
                            <p class="text-danger small mb-3"><i class="fas fa-map-marker-alt me-1"></i> Chiraura, Patna</p>
                            <p class="text-muted small">Our landmark completed project in Chiraura. A vibrant community that stands as a benchmark of our delivery promise.</p>
                            <button class="btn btn-outline-success btn-sm rounded-pill px-4 mt-2" disabled>Completed</button>
                        </div>
                    </div>
                </div>

                <!-- Project 5 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card project-card border-0 shadow-sm h-100">
                        <div class="project-status bg-success">Completed</div>
                        <div class="project-img-wrapper">
                            <img src="./images/complete-2.webp" alt="Silver City">
                        </div>
                        <div class="card-body p-4">
                            <h4 class="mb-2">Silver City</h4>
                            <p class="text-danger small mb-3"><i class="fas fa-map-marker-alt me-1"></i> Patna South</p>
                            <p class="text-muted small">A successfully delivered residential project known for its excellent connectivity and thoughtful layout.</p>
                            <button class="btn btn-outline-success btn-sm rounded-pill px-4 mt-2" disabled>Completed</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include "footer.php" ?>
    <?php include "vendor.php" ?>
</body>

</html>