# Project Neptune — CSG Readiness Console

A compact web app that answers a decisive question fast: **Is the Carrier Strike Group GO or NO‑GO?**  
Built with **PHP + MySQL (PDO)**, **Ajax (JSON)**, **vanilla JavaScript**, and **CSS**. Runs locally with Docker or in one click via GitHub Codespaces.

---

## 🔭 Overview

- **Single GO/NO‑GO indicator** (pulses when RED) for the whole CSG.
- **Limiting assets** line shows which ships/squadrons are driving NO‑GO.
- **Editable readiness table** per asset (Fuel, Crew, Ammunition, Weapons GO, Comms GO, Remarks).
- **Bulk report submit** — updates multiple assets in one JSON POST.
- **Threshold highlighting**: ≤ **50** → RED, ≤ **78** → AMBER, otherwise GREEN.
- **Sticky first columns** and **opaque zebra striping** for readability over textured background.
- **First‑run seeding**: database auto‑creates tables and inserts realistic demo data.

> Backgrounds: landing uses `image/fleet.jpeg`; dashboard uses `image/texture.jpg`.

---

## 🚀 Quick Start (Local with Docker)

Requirements: Docker Desktop (or Docker Engine + Compose plugin).

```bash
# 1) Clone your fork or this repo
git clone https://github.com/<your-username>/<your-repo>.git
cd <your-repo>

# 2) Build & run the stack (PHP+Apache + MariaDB)
docker compose up --build

# 3) Open the app
#   http://localhost:8080
```

First page load will auto‑create tables and seed sample assets.  
Open DevTools → Network to see **Ajax** calls to the **JSON** endpoints.

---

## 🌐 One‑Click in GitHub Codespaces (Optional but handy)

1. On the repo page → **Code** → **Codespaces** → **Create codespace on main**.  
2. Wait for the devcontainer to start and Docker to bring up services.  
3. In the **PORTS** panel, set **8080** visibility to **Public** (to share) and open it.

> This repo includes a `.devcontainer/devcontainer.json` bound to `docker-compose.yml` so the exact same stack runs in Codespaces.

---

## 🧭 Project Structure

```
.
├── index.php                 # Landing (lightweight username gate)
├── dash.php                  # Dashboard UI (GO/NO-GO + table/form)
├── style.css                 # Styling for landing + dashboard
├── dynamic.js                # Ajax, rendering, bulk submit
├── Data/
│   ├── db.php                # PDO connector + first-run schema/seed
│   └── api/
│       ├── csg_summary.php   # GET  → JSON summary + assets
│       └── report_submit.php # POST → bulk update + snapshot
└── image/
    ├── fleet.jpeg            # Landing background
    └── texture.jpg           # Dashboard texture
```

---

## 🧪 API (JSON)

### `GET /Data/api/csg_summary.php`
Returns the CSG roll‑up and per‑asset data.
```json
{
  "csg": { "color": "AMBER", "go": true, "red_count": 1, "amber_count": 3, "total": 16 },
  "assets": [
    {
      "id": 2, "kind": "DESTROYER", "name": "USS Hopper", "hull": "DDG-70",
      "color": "GREEN", "fuel": 92, "crew": 94, "ammunition": 87,
      "weapons_go": true, "comms_go": true, "remarks": "Systems nominal"
    }
  ]
}
```

### `POST /Data/api/report_submit.php`
Accepts a bulk report. Blank numeric fields mean **no change**.
```json
{
  "assets": [
    { "id": 2, "fuel": 45, "remarks": "Fuel transfer still in progress" },
    { "id": 7, "comms_go": 0 }
  ]
}
```

---

## 🧠 Status Logic

- **Carrier** may use `overall_percent` (if provided) to derive color; otherwise min(Fuel, Crew, Ammunition).  
- **Destroyers/Subs/Squadrons** use the **minimum** of the three percentages.  
- **Color thresholds**: `<=50 → RED`, `<=78 → AMBER`, `>=79 → GREEN`.

---

## 🔧 Troubleshooting

- **Nothing loads / white page**: check `docker compose logs -f web` and enable PHP errors if needed.  
- **Images missing**: ensure both files exist under `image/`.  
- **API JSON check**:
  ```bash
  curl -s http://localhost:8080/Data/api/csg_summary.php | head
  ```
- **Port conflict**: edit `docker-compose.yml` and change `8080:80` to another host port.

---

## 📋 Tech Stack

- **PHP 8 + Apache**, **PDO MySQL**, **MariaDB 10** (via Docker)  
- **Vanilla JS** + `fetch` (Ajax, JSON)  
- **CSS** with sticky columns, zebra stripes, and status pills

---

## 📄 License / Credit
- © 2025 Project Neptune.
