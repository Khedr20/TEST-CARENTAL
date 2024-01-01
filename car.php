<?php

class Car
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllCars()
    {
        $query = "SELECT * FROM cars";
        $results = mysqli_query($this->db->getConnection(), $query);
        return $results;
    }

    public function getExistingReservation($username)
    {
        $query = "SELECT * FROM cars WHERE reserved_by = '$username' AND reserved = 1";
        $result = mysqli_query($this->db->getConnection(), $query);
        return mysqli_num_rows($result) > 0;
    }

    public function reserveCar($carId, $username, $reservationDate, $reservationTime, $duration)
    {
        // Update the reservation status of the car in the database
        $query_price = "SELECT price_per_hour FROM cars WHERE id = $carId";
        $result_price = mysqli_query($this->db->getConnection(), $query_price);
        $pricePerHour = mysqli_fetch_assoc($result_price)['price_per_hour'];
        $totalPrice = $duration * $pricePerHour;

        $query = "UPDATE cars SET reserved = 1, reserved_by = '$username', reservation_date = '$reservationDate', reservation_time = '$reservationTime', total_price = $totalPrice WHERE id = $carId";

        $results = mysqli_query($this->db->getConnection(), $query);
        if (!$results) {
            die("Error in query: " . mysqli_error($this->db->getConnection()));
        }
    }

    public function unreserveCar($carId, $username)
    {
        // Update the reservation status of the car in the database
        $query = "UPDATE cars SET reserved = 0, reserved_by = NULL, reservation_date = NULL, reservation_time = NULL, total_price = NULL WHERE id = $carId";
        $results = mysqli_query($this->db->getConnection(), $query);

        if (!$results) {
            die("Error in query: " . mysqli_error($this->db->getConnection()));
        }
    }

    public function returnCar($carId, $username)
    {
        // Check if the current user has reserved the car
        $query = "SELECT * FROM cars WHERE id = $carId AND reserved_by = '$username'";
        $result = mysqli_query($this->db->getConnection(), $query);

        if (!$result || mysqli_num_rows($result) == 0) {
            // Redirect user if not the same or if the car is not reserved by the current user
            header("Location: dashboard.php");
            exit();
        }

        // Update the reservation status of the car in the database
        $query = "UPDATE cars SET reserved = 0, reserved_by = NULL, reservation_date = NULL, reservation_time = NULL, total_price = NULL WHERE id = $carId";
        $results = mysqli_query($this->db->getConnection(), $query);

        if (!$results) {
            die("Error in query: " . mysqli_error($this->db->getConnection()));
        }
    }
}

?>
