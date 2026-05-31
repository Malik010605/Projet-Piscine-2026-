<?php
session_start();
require_once "Fichiers PHP/config.php";

$utilisateur = isset($_SESSION["utilisateur"]) ? $_SESSION["utilisateur"] : null;
$notifications = array();

if ($conn && $utilisateur && isset($utilisateur["id"])) {
  $utilisateur_id = (int) $utilisateur["id"];
  $sql = "SELECT message, date_notification FROM notifications WHERE utilisateur_id = ? ORDER BY date_notification DESC";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "i", $utilisateur_id);
  mysqli_stmt_execute($stmt);
  $resultat = mysqli_stmt_get_result($stmt);

  if ($resultat) {
    while ($ligne = mysqli_fetch_assoc($resultat)) {
      $notifications[] = $ligne;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>VoyageVista - Mon compte</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <div class="header-title">
      <img class="site-logo" src="assets/images/logo-voyagevista.png" alt="Logo VoyageVista">
      <div>
        <h1>VoyageVista</h1>
        <p>Espace utilisateur</p>
      </div>
    </div>

    <nav>
      <ul>
        <li><a href="index.html">Accueil</a></li>
        <li><a href="destinations.php">Destinations</a></li>
        <li><a href="transports.php">Transports</a></li>
        <li><a href="hebergements.php">Hébergements</a></li>
        <li><a href="activites.php">Activités</a></li>
        <li><a href="sejour.php">Mon séjour</a></li>
        <li><a href="panier.php">Panier</a></li>
        <li><a href="connexion.php">Connexion</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <section>
      <h2>Mon compte</h2>

      <?php if ($utilisateur) { ?>
        <p>Bienvenue, <?php echo htmlspecialchars($utilisateur["prenom"]); ?>.</p>
        <p><strong>Email :</strong> <?php echo htmlspecialchars($utilisateur["email"]); ?></p>
        <p><strong>Rôle :</strong> <?php echo htmlspecialchars($utilisateur["role"]); ?></p>

        <h3>Notifications</h3>
        <?php if (count($notifications) > 0) { ?>
          <ul>
            <?php foreach ($notifications as $notification) { ?>
              <li><?php echo htmlspecialchars($notification["message"]); ?> - <?php echo htmlspecialchars($notification["date_notification"]); ?></li>
            <?php } ?>
          </ul>
        <?php } else { ?>
          <p>Aucune notification pour le moment.</p>
        <?php } ?>

        <p><a class="button-link" href="Fichiers PHP/traitement_deconnexion.php">Déconnexion</a></p>
      <?php } else { ?>
        <p class="info-box">Vous devez etre connecte pour acceder a votre compte.</p>
        <p><a class="button-link" href="connexion.php">Se connecter</a></p>
      <?php } ?>
    </section>
  </main>

  <footer>
    <small>Copyright &copy; 2026, VoyageVista - Projet Web dynamique - Malik SAADI, Edgar PRADET et Antoine DAGNEAUX</small>
  </footer>

  <script src="script.js"></script>
</body>
</html>
