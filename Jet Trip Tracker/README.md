# ✈️ Jet Trip Tracker

A lightweight PHP web application that lets users **log, visualize, and explore their flights in 3D**.  
Built with **PHP**, **JavaScript (Globe.gl + Three.js)**, and **CSS**, the Jet Trip Tracker turns simple text-based data into an interactive global visualization. Visit [JTT Here!](https://fuzzy-pancake-97q95r7gq546cx6q4-8080.app.github.dev/)

---

## 🌍 Overview

- **3D Globe Visualization:** Real-time rendering of flight paths using Globe.gl / Three.js.  
- **Flat-File Storage:** City coordinates and user flights are stored in plain text files—no database required.  
- **Account System:** Users can register, log in, and have their own saved flight data.  
- **Trip Dashboard:** Displays logged flights and destination stats, then visualizes them on a 3D globe.  
- **Session-based Authentication:** PHP sessions keep users logged in securely without external libraries.  
- **Responsive Design:** Aviation-themed backgrounds and a clean CSS layout optimized for desktop.

> The app was designed to run in a simple PHP/Apache environment and can be launched directly from GitHub using Codespaces.

---

## 🗂️ Data Model

| File | Purpose | Example Content |
|------|----------|-----------------|
| `data/city_coords.txt` | Master list of 74 cities and their coordinates | `Toronto\|43.6532\|-79.3832` |
| `data/flights_Ryan.txt` | User-specific trips | `New York -> Johannesburg` |
| `data/flights_Jill.txt` | One per user | `Boston -> Houston` |

Each user’s flight file is created automatically the first time they save a trip.

---

## 🖼️ Screenshots

| Screenshot | Description |
|-------------|--------------|
| ![Home Page](images/ss1.png) | **Home Page** shows home page. |
| ![Login Page](images/ss2.png) | **Login screen** showing the user authentication interface. |
| ![Dashboard](images/ss3.png) | **User dashboard** listing saved flights and travel mileage. |
| ![3D Globe](images/ss4.png) | **3D visualization** of global flight paths. |