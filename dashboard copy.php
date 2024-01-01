<?php
session_start(); // Start a session
// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION["username"])) {
    header("location: login.php");
    exit;
}
$username = $_SESSION['username']; // Get the username of the current user
// Connect to the database
$db = mysqli_connect('localhost', 'root', '', 'carental');
if (!$db) {
    die("Error connecting to the database: " . mysqli_connect_error());
}

// Query to retrieve the cars from the database
$query = "SELECT * FROM cars";
$results = mysqli_query($db, $query);
if (!$results) {
    die("Error in query: " . mysqli_error($db));
}

// Check if the user has an existing reservation
$query_existing_reservation = "SELECT * FROM cars WHERE reserved_by = '$username' AND reserved = 1";
$result_existing_reservation = mysqli_query($db, $query_existing_reservation);
$has_existing_reservation = mysqli_num_rows($result_existing_reservation) > 0;

// Check if the user has submitted the form to reserve a car
if (isset($_POST['reserve'])) {
    // Check if the user has an existing reservation
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

// Check if the user has submitted the form to unreserve a car
if (isset($_POST['unreserve'])) {
    $car_id = mysqli_real_escape_string($db, $_POST['unreserve_car_id']);
    // Check if the current user has reserved the car
    $query = "SELECT * FROM cars WHERE id = $car_id AND reserved_by = '$username'";
    $result = mysqli_query($db, $query);

    if (!$result || mysqli_num_rows($result) == 0) {
        // Redirect user if not the same or if the car is not reserved by the current user
        header("Location: dashboard.php");
        exit();
    }

    // Query to update the reservation status of the car in the database
    $query = "UPDATE cars SET reserved = 0, reserved_by = NULL, reservation_date = NULL, reservation_time = NULL, total_price = NULL WHERE id = $car_id";
    $results = mysqli_query($db, $query);

    if (!$results) {
        die("Error in query: " . mysqli_error($db));
    }
    header("Location: dashboard.php");
    exit();
}

// Check if the user has submitted the form to return a car
if (isset($_POST['return'])) {
    $car_id = mysqli_real_escape_string($db, $_POST['return_car_id']);

    // Check if the current user has reserved the car
    $query = "SELECT * FROM cars WHERE id = $car_id AND reserved_by = '$username'";
    $result = mysqli_query($db, $query);

    if (!$result || mysqli_num_rows($result) == 0) {
        // Redirect user if not the same or if the car is not reserved by the current user
        header("Location: dashboard.php");
        exit();
    }

    // Query to update the reservation status of the car in the database
    $query = "UPDATE cars SET reserved = 0, reserved_by = NULL, reservation_date = NULL, reservation_time = NULL, total_price = NULL WHERE id = $car_id";
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
    <title>Dashboard</title>
    <style>
        .car-model-image {
            max-width: 100px; /* Adjust the maximum width of the image as needed */
            height: auto; /* Maintain aspect ratio */
        }
    </style>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
    />

</head>

<body class="dashboard-bg">
    <div class="container">
        <div style="float: right;">
            <form method="post" action="logout.php">
                <button type="submit" name="logout">Logout</button>
            </form>
        </div>
        <h2>Welcome, <?php echo $username; ?></h2>
        <h3>Available Cars</h3>
        <table border="1">
            <tr>
                <th>Car Name</th>
                <th>Car Model</th>
                <th>Price Per Hour</th>
                <th>Reservation Status</th>
                <th>Reserved By</th>
                <th>Reservation Date</th>
                <th>Reservation Time</th>
                <th>Total Price</th>
                <th>Actions</th>
            </tr>
            <?php while ($car = mysqli_fetch_array($results)) { ?>
                <tr>
                    <tr class="animate__animated animate__fadeInUp">
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
                    <td><?php echo "RM" . number_format($car['price_per_hour'], 2); ?></td>
                    
                    <td>
                        <?php
                        if ($car['reserved'] == 0) {
                            echo "Not Reserved";
                        } else {
                            echo "Reserved";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($car['reserved_by'] === NULL) {
                            echo "-";
                        } else {
                            echo $car['reserved_by'];
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($car['reservation_date'] === NULL) {
                            echo "-";
                        } else {
                            echo $car['reservation_date'];
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($car['reservation_time'] === NULL) {
                            echo "-";
                        } else {
                            // Format Reservation Time to 12-hour format
                            echo date('h:i a', strtotime($car['reservation_time']));
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($car['total_price'] === NULL) {
                            echo "-";
                        } else {
                            echo "RM" . number_format($car['total_price'], 2);
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($car['reserved'] == 0) {
                            echo '<a href="booking.php?car_id=' . $car['id'] . '">Book</a>';
                        } else {
                            if ($car['reserved_by'] === $username) {
                                echo '<form method="post" action="dashboard.php">
                                        <input type="hidden" name="unreserve_car_id" value="' . $car['id'] . '">
                                        <button type="submit" name="unreserve">Return</button>
                                    </form>';
                            } else {
                                echo '<form method="post" action="dashboard.php">
                                        <input type="hidden" name="return_car_id" value="' . $car['id'] . '">
                                        <button type="submit" name="return">Return</button>
                                    </form>';
                            }
                        }
                        ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>

</html>
