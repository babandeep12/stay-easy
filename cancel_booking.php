<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'guest') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    
    $db = new mysqli('localhost', 'root', 'AA', 'stayeasy_db');
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }
    
    // Delete the booking
    $query = "DELETE FROM bookings WHERE booking_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $booking_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "Booking canceled successfully. , we will contact you for refund :)";
    } else {
        $_SESSION['error'] = "Failed to cancel booking. Please try again.";
    }

    $stmt->close();
    $db->close();

    // Redirect back to the bookings page
    header("Location: my_bookings.php");
    exit();
} else {
    header("Location: my_bookings.php");
    exit();
}
