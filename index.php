<?php
session_start();
require_once 'includes/header.php';
?>

<main class="container">

    <section class="hero">

        <h1>Bienvenue sur le Planificateur de bénévoles</h1>

        <p>
            Gérez efficacement les bénévoles, leurs disponibilités et les missions
            de votre événement culturel.
        </p>

        <a href="login.php" class="btn">
            Se connecter
        </a>

    </section>

    <section class="cards">

        <div class="card">
            <h2>📅 Disponibilités</h2>
            <p>Les bénévoles déclarent leurs créneaux disponibles.</p>
        </div>

        <div class="card">
            <h2>👥 Affectations</h2>
            <p>Les coordinateurs répartissent les bénévoles sur les missions.</p>
        </div>

        <div class="card">
            <h2>📊 Suivi</h2>
            <p>Le responsable visualise les missions couvertes et incomplètes.</p>
        </div>

    </section>

</main>

<?php
require_once 'includes/footer.php';
?>