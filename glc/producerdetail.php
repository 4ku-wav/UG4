<?php
// Include topbar, auth, and database
include 'topbar.php';
include 'auth.php';
include 'db.php';

// Get producer username from URL
$producerUsername = '';
if (isset($_GET['username'])) {
    $producerUsername = mysqli_real_escape_string($con, $_GET['username']);
} else {
    // No producer specified, redirect back
    header("Location: producers.php");
    exit();
}

// Query database for this producer
$query = "SELECT username, pfp, description FROM users WHERE username = '$producerUsername' AND isproducer = 1 LIMIT 1";
$result = mysqli_query($con, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    // Producer not found or not a producer
    header("Location: producers.php");
    exit();
}

$producer = mysqli_fetch_assoc($result);
$producerName = htmlspecialchars($producer['username']);
$producerDesc = htmlspecialchars($producer['description']);
$producerImage = 'images/pfp.png';

if (!empty($producer['pfp'])) {
    $pfpValue = trim($producer['pfp']);
    if (strpos($pfpValue, 'images/') === 0) {
        $producerImage = htmlspecialchars($pfpValue);
    } else {
        $producerImage = 'images/' . htmlspecialchars($pfpValue);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $producerName; ?> - Producer</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body class="body">
    <h1 class="title">Producer specific Page</h1>

    <div style="max-width: 800px; margin: 2rem auto; padding: 2rem; text-align: center;">
        <h2 class="title"><?php echo $producerName; ?></h2>
        
        <div class="profilepfp" style="margin: 2rem 0;">
            <img src="<?php echo $producerImage; ?>" alt="<?php echo $producerName; ?>" />
        </div>

        <p class="text" style="margin: 2rem 0;">
            <?php echo $producerDesc; ?>
        </p>

        <div style="border: 2px solid black; padding: 2rem; margin-top: 2rem; border-radius: 10px;">
            <h3 class="title" style="font-size: 2rem;">Producers Menu</h3>
            <p class="text">Products and menu items will appear here</p>
        </div>
    </div>

    <?php mysqli_close($con); ?>
</body>
</html>
<?php
include 'footer.php';
?>
