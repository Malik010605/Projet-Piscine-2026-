<?php
session_start();
require_once "config.php";

$message = "";

if (isset($_POST["verifier"])) {
  $prestation_id = isset($_POST["prestation_id"]) ? (int) $_POST["prestation_id"] : 0;

  if ($prestation_id <= 0) {
    $message = "Aucune prestation n'a été sélectionnée.";
  } elseif (!$conn) {
    $message = "Connexion à la base de données impossible.";
  } else {
    $statut = "validee";
    $sql = "UPDATE demandes_prestataires SET statut = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $statut, $prestation_id);

    if (mysqli_stmt_execute($stmt)) {
      $_SESSION["message_admin"] = "La prestation a été marquée comme vérifiée.";
      header("Location: ../admin.php");
      exit();
    } else {
      $message = "La prestation n'a pas pu etre modifiee.";
    }
  }
} else {
  $message = "Aucune action administrateur n'a été envoyée.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>VoyageVista - Admin</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
  <main>
    <section>
      <h1>Action impossible</h1>
      <p class="error"><?php echo htmlspecialchars($message); ?></p>
      <p><a class="button-link" href="../admin.php">Retour administration</a></p>
    </section>
  </main>
</body>
</html>
