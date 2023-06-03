<section class="footer-sitemap">
    <div class="container"{!! isset($animated) && $animated ? ' data-aos="fade-up"' : '' !!}>
        <h1>Explore More</h1>

        <div class="footer-links">
            <div class="footer-links__category">
                <h2>Server</h2>
                <ul>
                    <li><i class="bullet fas fa-cube"></i> <a href="{{ route('rules') }}">Rules & Guidelines</a></li>
                    <li><i class="bullet fas fa-cube"></i> <a href="{{ route('ranks') }}">Ranks</a></li>
                    <li><i class="bullet fas fa-cube"></i> <a href="{{ route('staff') }}">Staff</a></li>
                    <li><i class="bullet fas fa-cube"></i> <a href="{{ route('maps') }}" target="_blank" rel="noopener noreferrer">Real-Time Maps</a></li>
                    <li><i class="bullet fas fa-cube"></i> <a href="{{ route('3d-maps') }}" target="_blank" rel="noopener noreferrer">3D Maps</a></li>
                </ul>
            </div>

            <div class="footer-links__category">
                <h2>Community</h2>
                <ul>
                    <li><i class="bullet fas fa-cube"></i> <a href="{{ route('wiki') }}">Community Wiki</a></li>
                    <li><i class="bullet fas fa-cube"></i> <a href="{{ route('vote') }}">Vote For Our Server</a></li>
                </ul>
            </div>

            <div class="footer-links__category">
                <h2>Social Media</h2>
                <ul>
                    <li><i class="bullet fab fa-youtube"></i> <a href="https://www.youtube.com/user/PCBMinecraft" target="_blank" rel="noopener noreferrer">YouTube</a></li>
                    <li><i class="bullet fab fa-instagram"></i> <a href="https://www.instagram.com/projectcitybuild" target="_blank" rel="noopener noreferrer">Instagram</a></li>
                    <li><i class="bullet fab fa-facebook-f"></i> <a href="https://www.facebook.com/ProjectCityBuild" target="_blank" rel="noopener noreferrer">Facebook</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
