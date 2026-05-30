<?php
require_once "Fichiers PHP/config.php";

$destinations = array();
$categories = array();
$recherche = isset($_GET["recherche"]) ? $_GET["recherche"] : "";
$categorie = isset($_GET["categorie"]) ? $_GET["categorie"] : "";

if ($conn) {
  $resultat_categories = mysqli_query($conn, "SELECT DISTINCT categorie FROM destinations WHERE statut = 'validee' ORDER BY categorie");
  if ($resultat_categories) {
    while ($ligne = mysqli_fetch_assoc($resultat_categories)) {
      $categories[] = $ligne;
    }
  }

  if (!empty($recherche) && !empty($categorie)) {
    $sql = "SELECT id, nom, categorie, description, prix_base, image FROM destinations WHERE statut = 'validee' AND (nom LIKE ? OR description LIKE ?) AND categorie = ? ORDER BY nom";
    $stmt = mysqli_prepare($conn, $sql);
    $recherche_like = "%" . $recherche . "%";
    mysqli_stmt_bind_param($stmt, "sss", $recherche_like, $recherche_like, $categorie);
    mysqli_stmt_execute($stmt);
    $resultat = mysqli_stmt_get_result($stmt);
  } elseif (!empty($recherche)) {
    $sql = "SELECT id, nom, categorie, description, prix_base, image FROM destinations WHERE statut = 'validee' AND (nom LIKE ? OR description LIKE ?) ORDER BY nom";
    $stmt = mysqli_prepare($conn, $sql);
    $recherche_like = "%" . $recherche . "%";
    mysqli_stmt_bind_param($stmt, "ss", $recherche_like, $recherche_like);
    mysqli_stmt_execute($stmt);
    $resultat = mysqli_stmt_get_result($stmt);
  } elseif (!empty($categorie)) {
    $sql = "SELECT id, nom, categorie, description, prix_base, image FROM destinations WHERE statut = 'validee' AND categorie = ? ORDER BY nom";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $categorie);
    mysqli_stmt_execute($stmt);
    $resultat = mysqli_stmt_get_result($stmt);
  } else {
    $sql = "SELECT id, nom, categorie, description, prix_base, image FROM destinations WHERE statut = 'validee' ORDER BY nom";
    $resultat = mysqli_query($conn, $sql);
  }

  if ($resultat) {
    while ($ligne = mysqli_fetch_assoc($resultat)) {
      $destinations[] = $ligne;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>VoyageVista - Destinations</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <div class="header-title">
      <img class="site-logo" src="assets/images/logo-voyagevista.png" alt="Logo VoyageVista">
      <div>
        <h1>VoyageVista</h1>
        <p>Catalogue des destinations</p>
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
      <h2>Destinations disponibles</h2>
      <p>Cette page présente les destinations enregistrées dans la base de données.</p>

      <form method="get" action="destinations.php">
        <label for="recherche">Rechercher une destination :</label>
        <input type="text" id="recherche" name="recherche" placeholder="Exemple : Paris" value="<?php echo htmlspecialchars($recherche); ?>">

        <label for="categorie">Catégorie :</label>
        <select id="categorie" name="categorie">
          <option value="">Toutes les categories</option>
          <?php foreach ($categories as $cat) { ?>
            <option value="<?php echo htmlspecialchars($cat["categorie"]); ?>" <?php if ($categorie == $cat["categorie"]) { echo "selected"; } ?>>
              <?php echo htmlspecialchars($cat["categorie"]); ?>
            </option>
          <?php } ?>
        </select>

        <button type="submit">Rechercher</button>
      </form>
    </section>

    <section>
      <h2>Catalogue</h2>

      <div class="card-list">
        <?php foreach ($destinations as $destination) { ?>
          <article>
            <img class="card-image" src="assets/images/<?php echo htmlspecialchars($destination["image"]); ?>" alt="Vue de <?php echo htmlspecialchars($destination["nom"]); ?>">
            <span class="tag"><?php echo htmlspecialchars($destination["categorie"]); ?></span>
            <h3><?php echo htmlspecialchars($destination["nom"]); ?></h3>
            <p><?php echo htmlspecialchars($destination["description"]); ?></p>
            <p><strong>Prix indicatif :</strong> <?php echo htmlspecialchars($destination["prix_base"]); ?> euros</p>
            <p><a class="button-link" href="destination.php?id=<?php echo $destination["id"]; ?>">Voir le détail</a></p>
          </article>
        <?php } ?>
      </div>

      <?php if (count($destinations) == 0) { ?>
        <p class="info-box">Aucune destination ne correspond à la recherche.</p>
      <?php } ?>
    </section>
  </main>

  <footer>
    <small>Copyright &copy; 2026, VoyageVista - Projet Web dynamique - Malik SAADI, Edgar PRADET et Antoine DAGNEAUX</small>
  </footer>

  <script src="script.js"></script>
</body>
</html>
