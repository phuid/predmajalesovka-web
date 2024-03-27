<!-- $stmt = $conn->prepare("SELECT * FROM proofs INNER JOIN teams ON teams.id = proofs.team_id WHERE round_id = :roundId AND deleted = false AND (verified IS NULL OR verified = true) GROUP BY team_id ORDER BY time ASC");
    $stmt->bindParam(':roundId', $round['id']);
    $stmt->execute();
    $proof = $stmt->fetch();

    if ($proof) {
      while ($proof) {
        echo "<tr><td>" . $proof['name'] . "</td><td>" . $proof['time'] . "</td></tr>";
        $proof = $stmt->fetch();
      }
      echo "</table>";
    } else {
      echo "<p>Žádné výsledky</p>";
    } -->
lol