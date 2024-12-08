<?php
// Database connection
$db = new mysqli('localhost', 'root', 'AA', 'stayeasy_db'); // Update credentials if necessary
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Fetch guest_id from the GET request
if (isset($_GET['guest_id'])) {
    $guest_id = intval($_GET['guest_id']);
} else {
    die("Guest ID not provided.");
}

// Fetch guest username from the users table
$guest_query = "SELECT username FROM users WHERE id = $guest_id AND user_type = 'guest'";
$guest_result = $db->query($guest_query);

if ($guest_result && $guest_result->num_rows > 0) {
    $guest = $guest_result->fetch_assoc();
} else {
    die("Guest not found or is not of type 'guest'.");
}

// Fetch reviews for the guest from the guest_ratings table
$reviews_query = "
    SELECT rating, review, host_id, created_at
    FROM guest_ratings
    WHERE guest_id = $guest_id
    ORDER BY created_at DESC
";
$reviews_result = $db->query($reviews_query);

if (!$reviews_result) {
    die("Error fetching reviews: " . $db->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Reviews</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #f9f9f9;
        }
        .guest-info, .reviews, .actions {
            margin-bottom: 20px;
        }
        .guest-info {
            background: #eaeaea;
            padding: 15px;
            border-radius: 5px;
        }
        .review {
            background: #fff;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .review .host {
            font-weight: bold;
        }
        .actions button {
            padding: 10px 15px;
            font-size: 14px;
            cursor: pointer;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
        }
        .actions button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="guest-info">
    <h2>Reviews for Guest: <?php echo htmlspecialchars($guest['username']); ?></h2>
</div>

<div class="reviews">
    <h3>Guest Reviews</h3>
    <?php if ($reviews_result->num_rows > 0): ?>
        <?php while ($review = $reviews_result->fetch_assoc()): ?>
            <div class="review">
                <p class="host">Host ID: <?php echo htmlspecialchars($review['host_id']); ?></p>
                <p>Rating: <?php echo htmlspecialchars($review['rating']); ?>/5</p>
                <p>Comment: <?php echo htmlspecialchars($review['review']); ?></p>
                <p>Date: <?php echo htmlspecialchars($review['created_at']); ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No reviews available for this guest.</p>
    <?php endif; ?>
</div>

<div class="actions">
    <a href="host_dashboard.php"><button>Back to Dashboard</button></a>
    <a href="add_review.php?guest_id=<?php echo $guest_id; ?>"><button>Add a Review</button></a>
</div>

</body>
</html>
