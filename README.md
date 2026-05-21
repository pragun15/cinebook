# CineBook 🎬

CineBook is a modern full-stack movie ticket booking web application inspired by platforms like BookMyShow and Netflix.  
The system allows users to browse movies, view details, select seats, book tickets, and generate printable movie tickets through a clean cinematic interface.

---

# 🚀 Features

## 🎥 User Features
- Browse currently available movies
- Modern horizontal movie browsing UI
- Search movies by title or genre
- View detailed movie information
- Real-time seat selection system
- Dynamic ticket price calculation
- Booking confirmation system
- My Bookings page
- Printable / downloadable movie ticket generation
- Responsive dark-themed UI

---

## 🛠️ Admin Features
- Add new movies
- Edit existing movies
- Delete movies safely
- Manage movie ticket pricing
- Dynamic poster image support
- Responsive admin dashboard

---

# 🧰 Technologies Used

## Frontend
- HTML5
- CSS3
- JavaScript

## Backend
- PHP

## Database
- MySQL

## Server
- XAMPP / Apache

---

# 📂 Project Structure

```bash
movie-booking-system/
│
├── admin/
│   ├── add-movie.php
│   ├── edit-movie.php
│   ├── delete-movie.php
│   ├── manage-movies.php
│   └── admin.php
│
├── css/
│   └── style.css
│
├── js/
│   └── script.js
│
├── includes/
│   ├── navbar.php
│   └── footer.php
│
├── index.php
├── details.php
├── book-seat.php
├── my-bookings.php
├── ticket.php
├── db.php
└── README.md
```

---

# ⚙️ Installation Guide

## 1. Clone the Repository

```bash
git clone https://github.com/your-username/cinebook.git
```

---

## 2. Move Project to XAMPP htdocs

```bash
xampp/htdocs/movie-booking-system
```

---

## 3. Start Apache and MySQL

Open XAMPP Control Panel and start:
- Apache
- MySQL

---

## 4. Create Database

Create a database named:

```sql
movie_booking_system
```

---

## 5. Import SQL File

Import your SQL database file into phpMyAdmin.

---

## 6. Configure Database Connection

Open:

```php
db.php
```

Update credentials if needed:

```php
$host = "localhost";
$user = "root";
$password = "";
$database = "movie_booking_system";
```

---

## 7. Run the Project

Open browser:

```bash
http://localhost/movie-booking-system/
```

---

# 📸 Main Modules

## 🏠 Homepage
- Movie browsing
- Search functionality
- Responsive cinematic layout

## 🎬 Movie Details
- Movie description
- Show timings
- Ticket pricing

## 🎟️ Seat Booking
- Interactive seat selection
- Dynamic ticket calculation

## 📄 Ticket Generation
- Printable premium movie ticket
- Booking confirmation details

## 👨‍💼 Admin Dashboard
- Full movie CRUD operations
- Dynamic ticket pricing management

---

# 🎯 Project Objectives

- Build a full-stack movie booking platform
- Implement dynamic database integration
- Create a modern responsive UI
- Demonstrate CRUD operations using PHP & MySQL
- Simulate real-world online ticket booking systems

---

# 🔮 Future Enhancements

- User authentication system
- Online payment gateway
- Email ticket confirmation
- Movie trailer integration
- Real QR code ticket validation
- Analytics dashboard
- Multi-user support

---

# 👨‍💻 Developed By

**Pragun**  
Information Science Engineering Student

---

# 📜 License

This project is developed for educational and academic purposes.