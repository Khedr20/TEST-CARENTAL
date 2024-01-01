<?php
session_start();
require_once 'Database.php';
require_once 'User.php';
require_once 'Car.php';
require_once 'LuxuryCar.php';
require_once 'ExoticsCar.php';
require_once 'NormalCar.php';

$db = new Database();
$user = new User($db);
$car = new Car($db);
// Use the appropriate car class instance
$luxuryCar = new LuxuryCar($db); // Assuming 'LuxuryCar' is the appropriate class for your use case
$exoticsCar = new ExoticsCar($db); // Assuming 'ExoticsCar' is the appropriate class for your use case
$normalCar = new NormalCar($db); // Assuming 'NormalCar' is the appropriate class for your use case


if (!isset($_SESSION["username"])) {
    header("location: login.php");
    exit;
}

$username = $_SESSION['username'];

$results = $car->getAllCars();
$hasExistingReservation = $car->getExistingReservation($username);
// Assuming the function names are tailored to specific car categories
$resultsLuxury = $luxuryCar->getAllLuxuryCars();
$resultsExotics = $exoticsCar->getAllExoticCars();
$resultsNormal = $normalCar->getAllNormalCars();

$hasExistingReservationLuxury = $luxuryCar->getExistingLuxuryReservation($username);
$hasExistingReservationExotics = $exoticsCar->getExistingExoticReservation($username);
$hasExistingReservationNormal = $normalCar->getExistingNormalReservation($username);

// Check if the user has submitted the form to reserve a car
if (isset($_POST['reserve'])) {
    if ($hasExistingReservation) {
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

// Check if the user has submitted the form to unreserve a car
if (isset($_POST['unreserve'])) {
    $carId = mysqli_real_escape_string($db->getConnection(), $_POST['unreserve_car_id']);
    $car->unreserveCar($carId, $username);

    header("Location: dashboard.php");
    exit();
}

// Check if the user has submitted the form to return a car
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
    <link rel="stylesheet" type="text/css" href="styles.css">
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
                    <td><?php echo $car['make']; ?></td>
                    <td><?php echo $car['model']; ?></td>
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
                            echo '<form method="post" action="dashboard.php">
                                    <input type="hidden" name="reserve_car_id" value="' . $car['id'] . '">
                                    <input type="datetime-local" name="reservation_datetime" required>
                                    <label for="duration">Duration (hours): </label>
                                    <input type="number" name="duration" min="1" required>
                                    <button type="submit" name="reserve">Book</button>
                                </form>';
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
