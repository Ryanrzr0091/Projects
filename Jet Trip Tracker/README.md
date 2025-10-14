# ✈️ Jet Trip Tracker

A lightweight PHP web application that lets users **log, visualize, and explore their flights in 3D**.  
Built with **PHP**, **JavaScript (Globe.gl + Three.js)**, and **CSS**, the Jet Trip Tracker turns simple text-based data into an interactive global visualization.

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

## 🚀 Quick Start (Run from GitHub)

### Option A — GitHub Codespaces (recommended)
1. On your repository page → **Code** → **Codespaces** → **Create codespace on main**  
2. Wait for the Codespace to initialize (PHP + Apache environment)  
3. In the **PORTS** tab, expose port **80** (set it to *public* if you want to share)  
4. Click the URL — the site will open directly from your GitHub instance  

### `.devcontainer/devcontainer.json`
Include this file at the root of your repository:

```json
{
  "name": "Jet Trip Tracker",
  "image": "mcr.microsoft.com/devcontainers/php:8.2-apache-bullseye",
  "workspaceFolder": "/workspaces/${localWorkspaceFolderBasename}",
  "forwardPorts": [80],
  "postCreateCommand": "sudo rm -rf /var/www/html && sudo ln -s ${containerWorkspaceFolder} /var/www/html && apache2ctl -D FOREGROUND",
  "portsAttributes": {
    "80": { "label": "Jet Trip Tracker", "visibility": "public" }
  }
}