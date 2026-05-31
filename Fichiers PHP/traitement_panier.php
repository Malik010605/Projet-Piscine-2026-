<?php
session_start();
require_once "config.php";

function chercherDestination($conn, $nom) {
  $sql = "SELECT id, prix_base FROM destinations WHERE nom = ? AND statut = 'validee'";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "s", $nom);
  mysqli_stmt_execute($stmt);
  $resultat = mysqli_stmt_get_result($stmt);
  return mysqli_fetch_assoc($resultat);
}

function chercherTransport($conn, $type, $destination_id) {
  $sql = "SELECT id, prix FROM transports WHERE type_transport = ? AND destination_id = ? LIMIT 1";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "si", $type, $destination_id);
  mysqli_stmt_execute($stmt);
  $resultat = mysqli_stmt_get_result($stmt);
  return mysqli_fetch_assoc($resultat);
}

function chercherHebergement($conn, $nom, $destination_id) {
  $sql = "SELECT id, prix_nuit FROM hebergements WHERE nom = ? AND destination_id = ? AND disponible = 1";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "si", $nom, $destination_id);
  mysqli_stmt_execute($stmt);
  $resultat = mysqli_stmt_get_result($stmt);
  return mysqli_fetch_assoc($resultat);
}

function chercherActivite($conn, $nom, $destination_id) {
  $sql = "SELECT id, prix FROM activites WHERE nom = ? AND destination_id = ?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "si", $nom, $destination_id);
  mysqli_stmt_execute($stmt);
  $resultat = mysqli_stmt_get_result($stmt);
  return mysqli_fetch_assoc($resultat);
}

$destination = "";
$transport = "";
$hebergement = "";
$activite = "";
$personnes = 1;
$date_depart = "";
$error = "";

if (isset($_POST["ajouter_panier"])) {
  $destination = isset($_POST["destination"]) ? $_POST["destination"] : "";
  $transport = isset($_POST["transport"]) ? $_POST["transport"] : "";
  $hebergement = isset($_POST["hebergement"]) ? $_POST["hebergement"] : "";
  $activite = isset($_POST["activite"]) ? $_POST["activite"] : "";
  $personnes = isset($_POST["personnes"]) ? (int) $_POST["personnes"] : 1;
  $date_depart = isset($_POST["date_depart"]) ? $_POST["date_depart"] : "";

  if (empty($destination)) {
    $error .= "La destination est requise.<br>";
  }

  if (empty($transport)) {
    $error .= "Le transport est requis.<br>";
  }

  if (empty($hebergement)) {
    $error .= "L'hébergement est requis.<br>";
  }

  if (empty($date_depart)) {
    $error .= "La date de départ est requise.<br>";
  }

  if ($personnes <= 0) {
    $error .= "Le nombre de personnes doit etre superieur a 0.<br>";
  }

  if (!$conn) {
    $error .= "Connexion à la base de données impossible.<br>";
  }

  if ($error == "") {
    $destination_db = chercherDestination($conn, $destination);

    if (!$destination_db) {
      $error .= "La destination choisie n'est pas valide.<br>";
    }
  }

  if ($error == "") {
    $transport_db = chercherTransport($conn, $transport, $destination_db["id"]);
    $hebergement_db = chercherHebergement($conn, $hebergement, $destination_db["id"]);
    $activite_db = null;

    if (!$transport_db) {
      $error .= "Le transport choisi n'est pas valide pour cette destination.<br>";
    }

    if (!$hebergement_db) {
      $error .= "L'hébergement choisi n'est pas valide pour cette destination.<br>";
    }

    if (!empty($activite)) {
      $activite_db = chercherActivite($conn, $activite, $destination_db["id"]);

      if (!$activite_db) {
        $error .= "L'activité choisie n'est pas valide pour cette destination.<br>";
      }
    }
  }

  if ($error == "") {
    $_SESSION["sejour"] = array(
      "destination_id" => $destination_db["id"],
      "transport_id" => $transport_db["id"],
      "hebergement_id" => $hebergement_db["id"],
      "activite_id" => $activite_db ? $activite_db["id"] : null,
      "destination" => $destination,
      "transport" => $transport,
      "hebergement" => $hebergement,
      "activite" => $activite,
      "personnes" => $personnes,
      "date_depart" => $date_depart
    );

    $_SESSION["panier"] = array();
    $_SESSION["panier"][] = array("type" => "Destination", "nom" => $destination, "prix" => $destination_db["prix_base"] * $personnes);
    $_SESSION["panier"][] = array("type" => "Transport", "nom" => $transport, "prix" => $transport_db["prix"] * $personnes);
    $_SESSION["panier"][] = array("type" => "Hébergement", "nom" => $hebergement, "prix" => $hebergement_db["prix_nuit"]);

    if (!empty($activite) && $activite_db) {
      $_SESSION["panier"][] = array("type" => "Activité", "nom" => $activite, "prix" => $activite_db["prix"] * $personnes);
    }

    header("Location: ../panier.php");
    exit();
  }
} else {
  $error = "Aucun séjour n'a été envoyé.<br>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>VoyageVista - Panier</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
  <main>
    <section>
      <h1>Ajout impossible</h1>
      <p class="error"><?php echo $error; ?></p>
      <p><a class="button-link" href="../sejour.php">Retour au séjour</a></p>
    </section>
  </main>
</body>
</html>
