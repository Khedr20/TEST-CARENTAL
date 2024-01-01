<?php
class NormalCar
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllNormalCars()
    {
        $query = "SELECT * FROM cars WHERE category = 'normal'";
        $results = mysqli_query($this->db->getConnection(), $query);
        return $results;
    }

    public function getExistingNormalReservation($username)
    {
        $query = "SELECT * FROM cars WHERE reserved_by = '$username' AND reserved = 1 AND category = 'normal'";
        $result = mysqli_query($this->db->getConnection(), $query);
        return mysqli_num_rows($result) > 0;
    }

    public function reserveNormalCar($carId, $username, $reservationDate, $reservationTime, $duration)
    {
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

    public function unreserveNormalCar($carId, $username)
    {
        $query = "UPDATE cars SET reserved = 0, reserved_by = NULL, reservation_date = NULL, reservation_time = NULL, total_price = NULL WHERE id = $carId";
        $results = mysqli_query($this->db->getConnection(), $query);

        if (!$results) {
            die("Error in query: " . mysqli_error($this->db->getConnection()));
        }
    }

    public function returnNormalCar($carId, $username)
    {
        $query = "SELECT * FROM cars WHERE id = $carId AND reserved_by = '$username'";
        $result = mysqli_query($this->db->getConnection(), $query);

        if (!$result || mysqli_num_rows($result) == 0) {
            header("Location: dashboard.php");
            exit();
        }

        $query = "UPDATE cars SET reserved = 0, reserved_by = NULL, reservation_date = NULL, reservation_time = NULL, total_price = NULL WHERE id = $carId";
        $results = mysqli_query($this->db->getConnection(), $query);

        if (!$results) {
            die("Error in query: " . mysqli_error($this->db->getConnection()));
        }
    }
}
?>
