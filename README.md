# MiLog - Fuel Mileage & Expense Tracker

A comprehensive, multi-user web application designed to help vehicle owners track their fuel consumption, calculate mileage, and manage vehicle-related expenses.

## 📜 Description

**MiLog** provides a secure and intuitive platform for users to digitally log, manage, and analyze their fuel consumption and vehicle efficiency. It serves as a modern replacement for manual logbooks or spreadsheets, offering automatically calculated statistics and a clean, responsive user interface. The entire application is containerized with Docker for easy and consistent deployment.

## ✨ Features

- **Secure User Authentication:** Complete user registration and login system with session management and secure password hashing.
- **Multi-Vehicle Management:** Users can add and manage profiles for multiple vehicles.
- **Detailed Fuel Logging:** A simple form to log fuel entries with details like odometer reading, price, and volume.
- **Dynamic Dashboards:**
  - A main user dashboard with a high-level summary of all vehicles.
  - A vehicle-specific dashboard with detailed lifetime analytics.
- **PDF Report Generation:** Users can download a complete fuel log history for any vehicle as a professionally formatted PDF.
- **Administrator Panel:** A secure, role-based admin panel for user support, including a password reset feature.

## 🛠️ Tech Stack

- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Web Server:** Apache (within a Docker container)
- **PDF Generation:** FPDF library
- **Containerization:** Docker & Docker Compose
