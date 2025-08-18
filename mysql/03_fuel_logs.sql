CREATE TABLE IF NOT EXISTS fuel_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    odometer DECIMAL(10,2) NOT NULL,
    fuel_price DECIMAL(6,2) NOT NULL,
    fuel_liters DECIMAL(5,2) NOT NULL,
    fuel_cost DECIMAL(7,2) NOT NULL,
    fuel_type VARCHAR(20),
    location VARCHAR(100),
    note TEXT,
    filled_at DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
);
