<?php
session_start();

require_once 'Database.php';
require_once 'Car.php';
require_once 'User.php';

$db = new Database();
$car = new Car($db);
$user = new User($db);

if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit;
}

$username = $_SESSION['username'];

$results = $car->getAllCars();
$has_existing_reservation = $car->getExistingReservation($username);

if (isset($_POST['reserve'])) {
    if ($has_existing_reservation) {
        header("Location: dashboard.php");
        exit();
    }

    $carId = mysqli_real_escape_string($db->getConnection(), $_POST['reserve_car_id']);
    $reservationDate = date('Y-m-d', strtotime($_POST['reservation_datetime']));
    $reservationTime = date('H:i:s', strtotime($_POST['reservation_datetime']));
    $duration = $_POST['duration'];

    $car->reserveCar($carId, $username, $reservationDate, $reservationTime, $duration);

    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['unreserve'])) {
    $carId = mysqli_real_escape_string($db->getConnection(), $_POST['unreserve_car_id']);
    $car->unreserveCar($carId, $username);

    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['return'])) {
    $carId = mysqli_real_escape_string($db->getConnection(), $_POST['return_car_id']);
    $car->returnCar($carId, $username);

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
