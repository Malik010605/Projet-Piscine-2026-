<?php
require_once "Fichiers PHP/config.php";

$destinationChoisie = isset($_GET["destination"]) ? (int) $_GET["destination"] : 0;
$destinations = array();
$transports = array();
$hebergements = array();
$activites = array();

if ($conn) {
  $resultat = mysqli_query($conn, "SELECT id, nom FROM destinations WHERE statut = 'validee' ORDER BY nom");
  if ($resultat) {
    while ($ligne = mysqli_fetch_assoc($resultat)) {
      $destinations[] = $ligne;
    }
  }

  $resultat = mysqli_query($conn, "SELECT t.type_transport, d.nom AS destination FROM transports t INNER JOIN destinations d ON t.destination_id = d.id ORDER BY d.nom, t.type_transport");
  if ($resultat) {
    while ($ligne = mysqli_fetch_assoc($resultat)) {
      $transports[] = $ligne;
    }
  }

  $resultat = mysqli_query($conn, "SELECT h.nom, d.nom AS destination FROM hebergements h INNER JOIN destinations d ON h.destination_id = d.id WHERE h.disponible = 1 ORDER BY d.nom, h.nom");
  if ($resultat) {
    while ($ligne = mysqli_fetch_assoc($resultat)) {
      $hebergements[] = $ligne;
    }
  }

  $resultat = mysqli_query($conn, "SELECT a.nom, d.nom AS destination FROM activites a INNER JOIN destinations d ON a.destination_id = d.id ORDER BY d.nom, a.nom");
  if ($resultat) {
    while ($ligne = mysqli_fetch_assoc($resultat)) {
      $activites[] = $ligne;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>VoyageVista - Mon séjour</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <div class="header-title">
      <img class="site-logo" src="assets/images/logo-voyagevista.png" alt="Logo VoyageVista">
      <div>
        <h1>VoyageVista</h1>
        <p>Composer un séjour</p>
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
      <h2>Choix du séjour</h2>
      <p>Ce formulaire prepare les choix du voyageur à partir des éléments enregistrés dans la base.</p>

      <form method="post" action="Fichiers PHP/traitement_panier.php">
        <label for="destination">Destination :</label>
        <select id="destination" name="destination" required>
          <option value="">Choisir une destination</option>
          <?php foreach ($destinations as $destination) { ?>
            <option value="<?php echo htmlspecialchars($destination["nom"]); ?>" <?php if ($destinationChoisie == $destination["id"]) { echo "selected"; } ?>>
              <?php echo htmlspecialchars($destination["nom"]); ?>
            </option>
          <?php } ?>
        </select>

        <label for="transport">Transport :</label>
        <select id="transport" name="transport" required>
          <option value="">Choisir un transport</option>
          <?php foreach ($transports as $transport) { ?>
            <option value="<?php echo htmlspecialchars($transport["type_transport"]); ?>">
              <?php echo htmlspecialchars($transport["type_transport"] . " vers " . $transport["destination"]); ?>
            </option>
          <?php } ?>
        </select>

        <label for="hebergement">Hébergement :</label>
        <select id="hebergement" name="hebergement" required>
          <option value="">Choisir un hébergement</option>
          <?php foreach ($hebergements as $hebergement) { ?>
            <option value="<?php echo htmlspecialchars($hebergement["nom"]); ?>">
              <?php echo htmlspecialchars($hebergement["nom"] . " - " . $hebergement["destination"]); ?>
            </option>
          <?php } ?>
        </select>

        <label for="activite">Activité :</label>
        <select id="activite" name="activite">
          <option value="">Aucune activité</option>
          <?php foreach ($activites as $activite) { ?>
            <option value="<?php echo htmlspecialchars($activite["nom"]); ?>">
              <?php echo htmlspecialchars($activite["nom"] . " - " . $activite["destination"]); ?>
            </option>
          <?php } ?>
        </select>

        <label for="personnes">Nombre de personnes :</label>
        <input type="number" id="personnes" name="personnes" value="1" min="1" required>

        <label for="date_depart">Date de départ :</label>
        <input type="date" id="date_depart" name="date_depart" required>

        <button type="submit" name="ajouter_panier">Ajouter au panier</button>
      </form>
    </section>

    <section class="info-box">
      <h2>Regles simples prevues</h2>
      <p>Les dates, les places disponibles et la cohérence entre destination, transport, hébergement et activité seront contrôlées dans les traitements PHP.</p>
    </section>
  </main>

  <footer>
    <small>Copyright &copy; 2026, VoyageVista - Projet Web dynamique - Malik SAADI, Edgar PRADET et Antoine DAGNEAUX</small>
  </footer>

  <script src="script.js"></script>
</body>
</html>
