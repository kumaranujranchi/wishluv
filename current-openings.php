<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Openings - Wishluv Buildcon</title>
    <?php include "include.php" ?>
    <style>
        .job-card {
            border-left: 5px solid var(--Bean-Red);
            transition: all 0.3s ease;
        }
        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
        .job-tag {
            font-size: 0.8rem;
            padding: 5px 12px;
            border-radius: 20px;
            background-color: #f8f9fa;
            color: #666;
            margin-right: 10px;
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
                    <li class="breadcrumb-item active" aria-current="page">Current Openings</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="common-section">
        <div class="container">
            <div class="about-headline text-center mb-5">
                <h5>Careers</h5>
                <h3 class="home-title">Current <span>Openings</span></h3>
                <p class="mt-3">Join our growing team and build your career with Patna's leading real estate developer. We are always looking for talented individuals who are passionate about excellence and innovation.</p>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <!-- Job 1 -->
                    <div class="card job-card shadow-sm border-0 mb-4">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="mb-2">Sales Executive</h4>
                                    <div class="mb-3">
                                        <span class="job-tag"><i class="fas fa-map-marker-alt me-1"></i> Patna</span>
                                        <span class="job-tag"><i class="fas fa-briefcase me-1"></i> 1-3 Years Experience</span>
                                        <span class="job-tag"><i class="fas fa-clock me-1"></i> Full Time</span>
                                    </div>
                                    <p class="text-muted mb-0">We are looking for result-oriented Sales Executives to join our residential plots division. Candidate should have good communication skills and a deep understanding of the real estate market in Patna.</p>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <a href="apply.php?position=Sales%20Executive" class="btn btn-danger rounded-pill px-4">Apply Now</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job 2 -->
                    <div class="card job-card shadow-sm border-0 mb-4">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="mb-2">Marketing Manager</h4>
                                    <div class="mb-3">
                                        <span class="job-tag"><i class="fas fa-map-marker-alt me-1"></i> Patna</span>
                                        <span class="job-tag"><i class="fas fa-briefcase me-1"></i> 3-5 Years Experience</span>
                                        <span class="job-tag"><i class="fas fa-clock me-1"></i> Full Time</span>
                                    </div>
                                    <p class="text-muted mb-0">seeking an experienced Marketing Manager to develop and execute marketing strategies for our upcoming real estate projects. Experience in digital marketing and brand management is preferred.</p>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <a href="apply.php?position=Marketing%20Manager" class="btn btn-danger rounded-pill px-4">Apply Now</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job 3 -->
                    <div class="card job-card shadow-sm border-0 mb-4">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="mb-2">Office Assistant</h4>
                                    <div class="mb-3">
                                        <span class="job-tag"><i class="fas fa-map-marker-alt me-1"></i> Patna</span>
                                        <span class="job-tag"><i class="fas fa-briefcase me-1"></i> 0-2 Years Experience</span>
                                        <span class="job-tag"><i class="fas fa-clock me-1"></i> Full Time</span>
                                    </div>
                                    <p class="text-muted mb-0">We require an organized Office Assistant to handle day-to-day administrative tasks at our SK Puri office. Freshers with good computer knowledge are welcome to apply.</p>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <a href="apply.php?position=Office%20Assistant" class="btn btn-danger rounded-pill px-4">Apply Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5 p-5 bg-light rounded shadow-sm">
                <h4>Don't see a matching role?</h4>
                <p>We are always looking for great talent. Send us your resume and we'll keep you in mind for future openings.</p>
                <a href="apply.php" class="btn btn-outline-danger px-5 rounded-pill mt-2">Send General Application</a>
            </div>
        </div>
    </section>

    <?php include "footer.php" ?>
    <?php include "vendor.php" ?>
</body>

</html>