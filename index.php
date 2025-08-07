<?php
// Main entry point for the PubAd Website

// --- Installation Check ---
// If the config file doesn't exist, redirect to the installer.
if (!file_exists('app/config.php')) {
    header('Location: installer/index.php');
    exit;
}

// Include Header
// This contains the <head> section, meta tags, and opening <body> tag.
include 'app/views/_header.php';

// Include Navigation Bar
// This contains the main navigation menu and logo.
include 'app/views/_navbar.php';
?>

<!-- Main Content Area -->
<main>

    <!--
    ============================================================
    Hero Section
    ============================================================
    -->
    <section id="hero" class="hero-section">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Maximize Your Ad Revenue with PubAd</h1>
                    <p>Get the highest-paying direct advertisers for your websites</p>
                    <a href="#join-now" class="btn btn-primary btn-lg">Join Now</a>
                </div>
                <div class="hero-image">
                    <!-- Placeholder for a high-quality dashboard/report illustration -->
                    <img src="https://placehold.co/600x400/E2E8F0/4A5568?text=Dashboard+Illustration" alt="Dashboard Illustration">
                </div>
            </div>
        </div>
    </section>

    <!--
    ============================================================
    Why Choose Us Section
    ============================================================
    -->
    <section id="why-choose-us" class="why-choose-us-section">
        <div class="container">
            <div class="section-header">
                <h2>Why Choose Us</h2>
                <p>We provide the tools and expertise to help you succeed.</p>
            </div>
            <div class="features-grid">
                <!-- Feature Block 1 -->
                <div class="feature-block">
                    <img src="https://placehold.co/80x80/E2E8F0/4A5568?text=Icon" alt="Icon" class="feature-icon">
                    <h3>Direct High-Paying Advertisers</h3>
                </div>
                <!-- Feature Block 2 -->
                <div class="feature-block">
                    <img src="https://placehold.co/80x80/E2E8F0/4A5568?text=Icon" alt="Icon" class="feature-icon">
                    <h3>On-Time Payments, Every Month</h3>
                </div>
                <!-- Feature Block 3 -->
                <div class="feature-block">
                    <img src="https://placehold.co/80x80/E2E8F0/4A5568?text=Icon" alt="Icon" class="feature-icon">
                    <h3>Advanced Reporting & Transparency</h3>
                </div>
            </div>
        </div>
    </section>

    <!--
    ============================================================
    Ad Formats We Support Section
    ============================================================
    -->
    <section id="ad-formats" class="ad-formats-section">
        <div class="container">
            <div class="section-header">
                <h2>Ad Formats We Support</h2>
            </div>
            <!-- Slider container will be managed by JS -->
            <div class="ad-formats-slider">
                <!-- Slides will be managed by JS, but we'll list them here for static rendering -->
                <div class="slider-item active">
                    <img src="https://placehold.co/100x100/E2E8F0/4A5568?text=Icon" alt="Banner Ads">
                    <h4>Banner Ads</h4>
                    <p>Classic display ads in various sizes.</p>
                </div>
                <div class="slider-item">
                    <img src="https://placehold.co/100x100/E2E8F0/4A5568?text=Icon" alt="Native Ads">
                    <h4>Native Ads</h4>
                    <p>Ads that match the look and feel of your content.</p>
                </div>
                <div class="slider-item">
                    <img src="https://placehold.co/100x100/E2E8F0/4A5568?text=Icon" alt="Interstitial Ads">
                    <h4>Interstitial Ads</h4>
                    <p>Full-screen ads shown at natural transition points.</p>
                </div>
                <div class="slider-item">
                    <img src="https://placehold.co/100x100/E2E8F0/4A5568?text=Icon" alt="In-Page Push">
                    <h4>In-Page Push</h4>
                    <p>User-friendly push-style ads on your website.</p>
                </div>
                <div class="slider-item">
                    <img src="https://placehold.co/100x100/E2E8F0/4A5568?text=Icon" alt="Video Ads">
                    <h4>Video Ads</h4>
                    <p>Engaging video content for higher revenue.</p>
                </div>
                <div class="slider-item">
                    <img src="https://placehold.co/100x100/E2E8F0/4A5568?text=Icon" alt="Popunder">
                    <h4>Popunder</h4>
                    <p>A new tab or window appearing behind the main browser.</p>
                </div>
                <div class="slider-item">
                    <img src="https://placehold.co/100x100/E2E8F0/4A5568?text=Icon" alt="Sticky Ads">
                    <h4>Sticky Ads</h4>
                    <p>Ads that remain fixed as the user scrolls.</p>
                </div>
                <div class="slider-item">
                    <img src="https://placehold.co/100x100/E2E8F0/4A5568?text=Icon" alt="Custom Placement">
                    <h4>Custom Placement Ads</h4>
                    <p>Tailored ad solutions to fit your unique layout.</p>
                </div>
            </div>
            <!-- Simple Slider Controls -->
            <div class="slider-controls">
                <button class="slider-prev">&lt;</button>
                <button class="slider-next">&gt;</button>
            </div>
        </div>
    </section>

    <!--
    ============================================================
    Key Features Section
    ============================================================
    -->
    <section id="features" class="key-features-section">
        <div class="container">
            <div class="section-header">
                <h2>Key Features</h2>
            </div>
            <div class="key-features-grid">
                <!-- Feature items will be populated here -->
                <div class="key-feature-item">100% Fill Rate</div>
                <div class="key-feature-item">Multiple Ad Types</div>
                <div class="key-feature-item">Real-Time Analytics</div>
                <div class="key-feature-item">Easy Integration</div>
                <div class="key-feature-item">Dedicated Support</div>
                <div class="key-feature-item">Anti-Adblock Bypass</div>
                <div class="key-feature-item">Fast Payment</div>
                <div class="key-feature-item">Worldwide Advertisers</div>
                <div class="key-feature-item">CPM, CPC, and Smart Bidding Options</div>
                <div class="key-feature-item">High eCPM for all GEOs</div>
            </div>
        </div>
    </section>

    <!--
    ============================================================
    Blog-Ready Section
    ============================================================
    -->
    <section id="blog" class="blog-section">
        <div class="container">
            <div class="section-header">
                <h2>From Our Blog</h2>
                <p>Placeholder section for future blog posts.</p>
            </div>
            <!-- Placeholder content -->
        </div>
    </section>

</main>

<?php
// Include Footer
// This contains the footer content and closing </body> and </html> tags.
include 'app/views/_footer.php';
?>
