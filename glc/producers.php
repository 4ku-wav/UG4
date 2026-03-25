<?php
include 'topbar.php';
include 'db.php'; // Include database connection

?>

<head>
  <title>Producers</title>
</head>
<body class="body">

    
  <main class="producers-list">
    <h1 class="title">Producers Page</h1>
<p class = "text">This is the producers page. Where you can see all of the producers who are currently using Greensfield Local Hub, see their products and more!</p>
    <?php
    // Query to get producers where isproducer is true (1)
    $query = "SELECT username, pfp, description FROM users WHERE isproducer = 1";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $producerName = htmlspecialchars($row['username']);
            $producerDesc = !empty($row['description']) ? htmlspecialchars($row['description']) : 'No description provided yet.';

            // normalize pfp path from db
            $producerImage = 'images/pfp.png'; // default
            if (!empty($row['pfp'])) {
                $rawPfp = trim($row['pfp']);
                if (strpos($rawPfp, 'images/') === 0) {
                    $producerImage = htmlspecialchars($rawPfp);
                } else {
                    $producerImage = 'images/' . htmlspecialchars($rawPfp);
                }
                if (!file_exists($producerImage)) {
                    $producerImage = 'images/pfp.png';
                }
            }
            ?>
            <a href="producerdetail.php?username=<?php echo urlencode($producerName); ?>">
            <div class="producers-card">
              <img class="producers-photo" src="<?php echo $producerImage; ?>" alt="<?php echo $producerName; ?>">
              <div class="producers-info">
            <h2><?php echo $producerName; ?></h2>
            <p><?php echo $producerDesc; ?></p>
              </div>
            </div>
            </a>
            <?php
        }
    } else {
        echo "<p class = 'text'>No producers were found.</p>";
    }

    mysqli_close($con);
    ?>

  </main>
</body>

<?php
include 'footer.php';
?>
</html>