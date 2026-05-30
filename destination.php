<?php
require_once "Fichiers PHP/config.php";

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$destination = null;

if ($conn && $id > 0) {
  $sql = "SELECT id, nom, categorie, description, prix_base, image FROM destinations WHERE id = ? AND statut = 'validee'";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  $resultat = mysqli_stmt_get_result($stmt);
  $destination = mysqli_fetch_assoc($resultat);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>VoyageVista - Détail destination</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <div class="header-title">
      <img class="site-logo" src="assets/images/logo-voyagevista.png" alt="Logo VoyageVista">
      <div>
        <h1>VoyageVista</h1>
        <p>Détail d'une destination</p>
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
      <?php if ($destination) { ?>
        <span class="tag"><?php echo htmlspecialchars($destination["categorie"]); ?></span>
        <h2><?php echo htmlspecialchars($destination["nom"]); ?></h2>
        <img class="detail-image" src="assets/images/<?php echo htmlspecialchars($destination["image"]); ?>" alt="Vue de <?php echo htmlspecialchars($destination["nom"]); ?>">
        <p><?php echo htmlspecialchars($destination["description"]); ?></p>

        <table>
          <tr>
            <th>Information</th>
            <th>Valeur</th>
          </tr>
          <tr>
            <td>Catégorie</td>
            <td><?php echo htmlspecialchars($destination["categorie"]); ?></td>
          </tr>
          <tr>
            <td>Prix indicatif</td>
            <td><?php echo htmlspecialchars($destination["prix_base"]); ?> euros</td>
          </tr>
          <tr>
            <td>Remarque</td>
            <td>Les transports, hebergements et activites se choisissent dans la page Mon séjour.</td>
          </tr>
        </table>

        <p>
          <a class="button-link" href="sejour.php?destination=<?php echo $id; ?>">Composer un séjour</a>
          <a class="second-link" href="destinations.php">Retour aux destinations</a>
        </p>
      <?php } else { ?>
        <h2>Destination introuvable</h2>
        <p class="error">Aucune destination valide n'a été trouvée.</p>
        <p><a class="button-link" href="destinations.php">Retour aux destinations</a></p>
      <?php } ?>
    </section>
  </main>

  <footer>
    <small>Copyright &copy; 2026, VoyageVista - Projet Web dynamique - Malik SAADI, Edgar PRADET et Antoine DAGNEAUX</small>
  </footer>

  <script src="script.js"></script>
</body>
</html>
