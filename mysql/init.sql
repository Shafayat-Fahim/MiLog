CREATE TABLE IF NOT EXISTS fuel_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_name VARCHAR(100) NOT NULL,
    odometer_km FLOAT NOT NULL,
    fuel_liters FLOAT NOT NULL,
    date_added DATE NOT NULL
);
