
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cabinet Dr. El Halimi - Votre Santé, Notre Priorité</title>
    <link href="{{ asset('css/index.css') }}" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <div class="logo">
            <span class="logo-icon">🩺</span>
            <span>Cabinet Dr. El Halimi</span>
        </div>
        <nav class="header-nav">
            <a href="{{ route('login') }}">Connexion</a>
            <a href="{{ route('register') }}" class="btn-primary">S'inscrire</a>
        </nav>
    </header>

    <section class="hero-section">
        <div class="hero-content">
            <span class="hero-tag">Votre santé, notre priorité</span>
            <h1>Votre Cabinet Médical Moderne et Connecté</h1>
            <p>Profitez d'une expérience médicale innovante avec la prise de rendez-vous en ligne, le suivi personnalisé et l'accès à votre dossier médical 24/24.</p>

            <div class="hero-actions">
                <a href="{{ route('register') }}" class="btn-primary">Créer mon compte &rarr;</a>
                <a href="{{ route('login') }}" class="btn-secondary">Se connecter</a>
            </div>

            <div class="hero-features">
                <span>✅ Inscription gratuite</span>
                <span>✅ Données sécurisées</span>
            </div>
        </div>
        <div class="hero-image-container">
            <div class="hero-image-placeholder">
                <img src="{{ asset('images/photo-1631217868264-e5b90bb7e133.jpg') }}" alt="Doctor Consulting">
            </div>
            <div class="hero-stats-card">
                <p>Patients actifs</p>
                <strong>5,000+</strong>
            </div>
        </div>
    </section>

    <section class="stats-bar">
        <div class="stats-grid">
            <div class="stat">
                <strong>5000+</strong>
                <p>Patients satisfaits</p>
            </div>
            <div class="stat">
                <strong>15+</strong>
                <p>Années d'expérience</p>
            </div>
            <div class="stat">
                <strong>98%</strong>
                <p>Taux de satisfaction</p>
            </div>
            <div class="stat">
                <strong>24/7</strong>
                <p>Service en ligne</p>
            </div>
        </div>
    </section>

    <section class="services-highlight">
        <h2>Pourquoi Choisir Notre Cabinet ?</h2>
        <p>Découvrez nos services modernes pensés pour faciliter votre parcours de soins</p>

        <div class="service-cards-grid">
            <div class="service-card blue-card">
                <div class="card-icon">🗓️</div>
                <h3 class="card-title">Prise de rendez-vous en ligne</h3>
                <p class="card-description">Réservez vos consultations 24h/24 en quelques clics</p>
            </div>
            <div class="service-card green-card">
                <div class="card-icon">📄</div>
                <h3 class="card-title">Dossier médical numérique</h3>
                <p class="card-description">Accédez à votre historique médical à tout moment</p>
            </div>
            <div class="service-card purple-card">
                <div class="card-icon">⏱️</div>
                <h3 class="card-title">Suivi en temps réel</h3>
                <p class="card-description">Recevez des rappels et notifications pour vos RDV</p>
            </div>
            <div class="service-card red-card">
                <div class="card-icon">🛡️</div>
                <h3 class="card-title">Sécurité garantie</h3>
                <p class="card-description">Vos données médicales sont protégées et confidentielles</p>
            </div>
        </div>
    </section>

    <section class="cta-banner">
        <h2>Prêt à Prendre Soin de Votre Santé ?</h2>
        <p>Créez votre compte gratuitement et prenez votre premier rendez-vous en quelques minutes</p>
        <div class="cta-actions">
            <a href="{{ route('register') }}" class="btn-cta-main">S'inscrire maintenant &rarr;</a>
            <a href="{{ route('login') }}" class="btn-cta-secondary">J'ai déjà un compte</a>
        </div>
    </section>

    <section class="contact-section">
        <h2>Nous Contacter</h2>
        <p>Notre équipe est à votre disposition</p>

        <div class="contact-cards-grid">
            <div class="contact-card phone-card">
                <div class="card-icon">📞</div>
                <h3 class="card-title">Téléphone</h3>
                <p class="contact-detail">05 23 45 67 89</p>
                <p class="contact-hours">Lun - Vend : 8h - 18h</p>
            </div>

            <div class="contact-card email-card">
                <div class="card-icon">✉️</div>
                <h3 class="card-title">Email</h3>
                <p class="contact-detail break-all">Dr.ElHalimi.AlWarda@gmail.com</p>
                <p class="contact-hours">Réponse sous 24h</p>
            </div>

            <div class="contact-card address-card" >
                <div class="card-icon">📍</div>
                <h3 class="card-title">Adresse</h3>
                <p class="contact-detail">Boulevard Hassan I rue gharnata, résidence ELHALIMI 2eme étage N8, Nador.</p>
                <p class="contact-hours">62000 Nador, Maroc</p>
            </div>
        </div>
    </section>

    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-col footer-branding">
                <h3 class="footer-title">Cabinet Dr. Dubois</h3>
                <p class="footer-description">Votre santé est notre priorité. Cabinet médical moderne et connecté.</p>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">Services</h4>
                <ul>
                    <li><a href="#">Consultations</a></li>
                    <li><a href="#">Téléconsultation</a></li>
                    <li><a href="#">Urgences</a></li>
                    <li><a href="#">Vaccinations</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">Informations</h4>
                <ul>
                    <li><a href="#">À propos</a></li>
                    <li><a href="#">Horaires</a></li>
                    <li><a href="#">Tarifs</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">Légal</h4>
                <ul>
                    <li><a href="#">Mentions légales</a></li>
                    <li><a href="#">Confidentialité</a></li>
                    <li><a href="#">CGU</a></li>
                    <li><a href="#">Cookies</a></li>
                </ul>
            </div>
        </div>
    </footer>

</body>
</html>