<?php
// Database connection
$db = new mysqli('localhost', 'root', 'AA', 'stayeasy_db'); // Corrected database name
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Fetch guest_id from the GET request
if (isset($_GET['guest_id'])) {
    $guest_id = intval($_GET['guest_id']);
} else {
    die("Guest ID not provided.");
}

// Fetch guest information
$guest_query = "SELECT username FROM users WHERE id = ?";
$guest_stmt = $db->prepare($guest_query);
$guest_stmt->bind_param("i", $guest_id);
$guest_stmt->execute();
$guest_result = $guest_stmt->get_result();
$guest = $guest_result->fetch_assoc();

if (!$guest) {
    die("Guest not found.");
}

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host_id = 1; // Replace this with the logged-in host's ID
    $rating = intval($_POST['rating']);
    $review = trim($_POST['review']);

    if ($rating < 1 || $rating > 5) {
        $message = "Rating must be between 1 and 5.";
    } elseif (empty($review)) {
        $message = "Review cannot be empty.";
    } else {
        $insert_query = "INSERT INTO guest_ratings (guest_id, host_id, rating, review, created_at, updated_at) 
                         VALUES (?, ?, ?, ?, NOW(), NOW())";
        $insert_stmt = $db->prepare($insert_query);
        $insert_stmt->bind_param("iiis", $guest_id, $host_id, $rating, $review);

        if ($insert_stmt->execute()) {
            $message = "Review added successfully!";
        } else {
            $message = "Failed to add review. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Review</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #f9f9f9;
        }
        .guest-info, .form-container, .message {
            margin-bottom: 20px;
        }
        .guest-info {
            background: #eaeaea;
            padding: 10px;
            border-radius: 5px;
        }
        .form-container form {
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-container label {
            display: block;
            margin-bottom: 8px;
        }
        .form-container input, .form-container textarea, .form-container button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-container button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #0056b3;
        }
        .message {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>

<div class="guest-info">
    <h2>Add Review for Guest: <?php echo htmlspecialchars($guest['username']); ?></h2>
</div>

<div class="message">
    <?php if ($message): ?>
        <p class="<?php echo strpos($message, 'successfully') !== false ? '' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>
</div>

<div class="form-container">
    <form method="POST" action="">
    <div class="form-group">
                <label for="rating">Rating (1-5):</label>
                <select name="rating" id="rating" required>
                    <option value="">Select</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>

        <label for="review">Review:</label>
        <textarea name="review" id="review" rows="4" required></textarea>

        <button type="submit">Submit Review</button>
    </form>
</div>

<div>
    <a href="view_guest_reviews.php?guest_id=<?php echo $guest_id; ?>"><button>Back to Guest Reviews</button></a>
</div>

</body>
</html>
