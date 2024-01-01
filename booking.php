<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("location: login.php");
    exit;
}

$username = $_SESSION['username'];
$db = mysqli_connect('localhost', 'root', '', 'carental');
if (!$db) {
    die("Error connecting to the database: " . mysqli_connect_error());
}

if (isset($_GET['car_id'])) {
    $car_id = mysqli_real_escape_string($db, $_GET['car_id']);
    $query = "SELECT * FROM cars WHERE id = $car_id";
    $result = mysqli_query($db, $query);

    if (!$result) {
        die("Error in query: " . mysqli_error($db));
    }

    $car = mysqli_fetch_assoc($result);
}

if (isset($_POST['reserve'])) {
    $reservation_datetime = $_POST['reservation_datetime'];
    $duration = $_POST['duration'];

    if ($has_existing_reservation) {
        header("Location: dashboard.php");
        exit();
    }

    $car_id = mysqli_real_escape_string($db, $_POST['reserve_car_id']);
    $reservation_date = date('Y-m-d', strtotime($_POST['reservation_datetime']));
    $reservation_time = date('H:i:s', strtotime($_POST['reservation_datetime']));
    $duration = $_POST['duration'];

    // Calculate total price based on duration and price per hour
    $query_price = "SELECT price_per_hour FROM cars WHERE id = $car_id";
    $result_price = mysqli_query($db, $query_price);
    $price_per_hour = mysqli_fetch_assoc($result_price)['price_per_hour'];
    $total_price = $duration * $price_per_hour;

    // Query to update the reservation status of the car in the database
    $query = "UPDATE cars SET reserved = 1, reserved_by = '$username', reservation_date = '$reservation_date', reservation_time = '$reservation_time', total_price = $total_price WHERE id = $car_id";

    $results = mysqli_query($db, $query);
    if (!$results) {
        die("Error in query: " . mysqli_error($db));
    }
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Car Booking</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>

<body class="booking-bg">
    <div class="container">
        <h2>Car Reservation</h2>
        <h3>Car Details</h3>
        <table border="1">
            <!-- Display car details here -->
            <tr>
                <th>Car Name</th>
                <th>Car Model</th>
                <!-- Include other car details here -->
            </tr>
            <tr>
                <td><?php echo $car['make']; ?></td>
                <td>
                
                    <?php

                    // Check if the 'model' column contains an image URL
                    if (!empty($car['model'])) {
                        // Output an <img> tag with the URL as the 'src' attribute
                        echo '<img src="' . $car['model'] . '" alt="' . $car['make'] .  '" class="car-model-image" style="width: 100px; height: 100px;">';
                    } else {
                        echo 'No Image Available';
                    } 
                    
                    ?>
                
                </td>
                <!-- Include other car details here -->
            </tr>
        </table>

        <h3>Reservation Form</h3>
        <form method="post" action="booking.php">
            <input type="hidden" name="reserve_car_id" value="<?php echo $car['id']; ?>">
            <label for="reservation_datetime">Reservation Date and Time:</label>
            <input type="datetime-local" name="reservation_datetime" required><br>
            <label for="duration">Duration (hours):</label>
            <input type="number" name="duration" min="1" required><br>
            <button type="submit" name="reserve">Reserve</button>
        </form>

        <form method="post" action="dashboard.php">
            <button type="submit">Go Back to Dashboard</button>
        </form>
    </div>
</body>

</html>
