<?php
// include topbar and auth checks
include 'topbar.php';
include 'auth.php';
include 'db.php'; // database connection

// Initialize upload message
$uploadMessage = '';
$uploadError = '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profilePicture'])) {
    $username = mysqli_real_escape_string($con, $_SESSION['username']);
    $file = $_FILES['profilePicture'];
    
    // Validate file
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        $uploadError = "Only JPG, PNG, and GIF images are allowed.";
    } elseif ($file['size'] > $maxSize) {
        $uploadError = "File size must be less than 5MB.";
    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $uploadError = "Upload failed. Please try again.";
    } else {
        // Generate unique filename
        $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = $username . '_' . time() . '.' . $fileExt;
        $uploadDir = 'images/';
        $uploadPath = $uploadDir . $newFilename;
        
        // Move file to images folder
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Update database with new filename
            $updateQuery = "UPDATE users SET pfp = '$newFilename' WHERE username = '$username'";
            if (mysqli_query($con, $updateQuery)) {
                $uploadMessage = "Profile picture updated successfully!";
                // Refresh userPfp variable
                $userPfp = $uploadPath;
            } else {
                $uploadError = "Database update failed.";
                unlink($uploadPath); // Delete uploaded file if DB update fails
            }
        } else {
            $uploadError = "Failed to save image file.";
        }
    }
}

// default fallback
$userPfp = 'images/pfp.png';

if (!empty($_SESSION['username'])) {
    $username = mysqli_real_escape_string($con, $_SESSION['username']);
    $query = "SELECT pfp FROM users WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (!empty($row['pfp'])) {
            $pfpValue = trim($row['pfp']);
            if (strpos($pfpValue, 'images/') === 0) {
                $userPfp = htmlspecialchars($pfpValue);
            } else {
                $userPfp = 'images/' . htmlspecialchars($pfpValue);
            }
            if (!file_exists($userPfp)) {
                $userPfp = 'images/pfp.png';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Account</title>
</head>
<body class="body">
    <div class="profile">
        <p class="profile-text">Welcome <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    </div>

    <div class="profilepfp">
        <img src="<?php echo $userPfp; ?>" alt="Profile Picture" />
    </div>

    <?php if (!empty($uploadMessage)): ?>
        <p style="color: green; text-align: center; font-size: 1.2rem;"><?php echo $uploadMessage; ?></p>
    <?php endif; ?>
    
    <?php if (!empty($uploadError)): ?>
        <p style="color: red; text-align: center; font-size: 1.2rem;"><?php echo $uploadError; ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" style="text-align: center; margin: 2rem 0;">
        <input type="file" id="profilePicture" name="profilePicture" accept="image/jpeg, image/png, image/gif" required style="padding: 0.5rem;">
        <input type="submit" value="Upload Profile Picture" style="padding: 0.5rem 1rem; background-color: #b3b3b3; border: 2px solid black; cursor: pointer; font-size: 1rem;">
    </form>

    <div class="profile">
        <p class="profile-text"><a href="dashboard.php">Dashboard</a></p>
    </div>

    <div class="profile">
        <a href="logout.php"><p class="profile-text">Logout</p></a>
    </div>

    <?php mysqli_close($con); ?>
</body>
<?php
include 'footer.php';
?>
</html>