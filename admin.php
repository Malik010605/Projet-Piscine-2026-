<?php
session_start();
require_once "Fichiers PHP/config.php";

$utilisateurs = array();
$reservations = array();
$prestations = array();
$message_admin = isset($_SESSION["message_admin"]) ? $_SESSION["message_admin"] : "";
unset($_SESSION["message_admin"]);

function afficherStatut($statut) {
  if ($statut == "validee") {
    return "validée";
  }

  if ($statut == "a_verifier") {
    return "à vérifier";
  }

  return str_replace("_", " ", $statut);
}

if ($conn) {
  $resultat = mysqli_query($conn, "SELECT nom, prenom, email, role FROM utilisateurs ORDER BY nom");
  if ($resultat) {
    while ($ligne = mysqli_fetch_assoc($resultat)) {
      $utilisateurs[] = $ligne;
    }
  }

  $sql = "SELECT r.id, r.statut, r.total, u.nom AS client, d.nom AS destination
          FROM reservations r
          LEFT JOIN utilisateurs u ON r.utilisateur_id = u.id
          LEFT JOIN reservation_details rd ON rd.reservation_id = r.id
          LEFT JOIN destinations d ON rd.destination_id = d.id
          ORDER BY r.date_reservation DESC";
  $resultat = mysqli_query($conn, $sql);
  if ($resultat) {
    while ($ligne = mysqli_fetch_assoc($resultat)) {
      $reservations[] = $ligne;
    }
  }

  $sql = "SELECT id, type_prestation, nom_prestataire, destination, statut FROM demandes_prestataires ORDER BY date_demande DESC";
  $resultat = mysqli_query($conn, $sql);
  if ($resultat) {
    while ($ligne = mysqli_fetch_assoc($resultat)) {
      $prestations[] = $ligne;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>VoyageVista - Administration</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <div class="header-title">
      <img class="site-logo" src="assets/images/logo-voyagevista.png" alt="Logo VoyageVista">
      <div>
        <h1>VoyageVista</h1>
        <p>Administration simple</p>
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
    <?php if (!empty($message_admin)) { ?>
      <section class="info-box">
        <p><?php echo htmlspecialchars($message_admin); ?></p>
      </section>
    <?php } ?>

    <section>
      <h2>Utilisateurs</h2>
      <table>
        <tr>
          <th>Nom</th>
          <th>Email</th>
          <th>Rôle</th>
        </tr>
        <?php foreach ($utilisateurs as $utilisateur) { ?>
          <tr>
            <td><?php echo htmlspecialchars($utilisateur["prenom"] . " " . $utilisateur["nom"]); ?></td>
            <td><?php echo htmlspecialchars($utilisateur["email"]); ?></td>
            <td><?php echo htmlspecialchars($utilisateur["role"]); ?></td>
          </tr>
        <?php } ?>
      </table>
    </section>

    <section>
      <h2>Reservations</h2>
      <table>
        <tr>
          <th>Client</th>
          <th>Destination</th>
          <th>Total</th>
          <th>Statut</th>
        </tr>
        <?php foreach ($reservations as $reservation) { ?>
          <tr>
            <td><?php echo htmlspecialchars($reservation["client"] ? $reservation["client"] : "Invite"); ?></td>
            <td><?php echo htmlspecialchars($reservation["destination"]); ?></td>
            <td><?php echo htmlspecialchars($reservation["total"]); ?> euros</td>
            <td><?php echo htmlspecialchars(afficherStatut($reservation["statut"])); ?></td>
          </tr>
        <?php } ?>
      </table>
    </section>

    <section>
      <h2>Prestations proposées</h2>
      <table>
        <tr>
          <th>Type</th>
          <th>Nom</th>
          <th>Statut</th>
          <th>Action</th>
        </tr>
        <?php foreach ($prestations as $prestation) { ?>
          <tr>
            <td><?php echo htmlspecialchars($prestation["type_prestation"]); ?></td>
            <td><?php echo htmlspecialchars($prestation["nom_prestataire"] . " - " . $prestation["destination"]); ?></td>
            <td><?php echo htmlspecialchars(afficherStatut($prestation["statut"])); ?></td>
            <td>
              <form method="post" action="Fichiers PHP/traitement_admin.php">
                <input type="hidden" name="prestation_id" value="<?php echo htmlspecialchars($prestation["id"]); ?>">
                <button type="submit" name="verifier">Vérifier</button>
              </form>
            </td>
          </tr>
        <?php } ?>
      </table>
    </section>
  </main>

  <footer>
    <small>Copyright &copy; 2026, VoyageVista - Projet Web dynamique - Malik SAADI, Edgar PRADET et Antoine DAGNEAUX</small>
  </footer>

  <script src="script.js"></script>
</body>
</html>
