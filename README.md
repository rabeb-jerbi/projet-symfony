<div align="center">

<img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white" />
<img src="https://img.shields.io/badge/Symfony-7.3-000000?style=flat-square&logo=symfony&logoColor=white" />
<img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=flat-square&logo=mysql&logoColor=white" />
<img src="https://img.shields.io/badge/Doctrine-ORM-orange?style=flat-square" />
<img src="https://img.shields.io/badge/Status-Academic%20Project-purple?style=flat-square" />

# 🚗 YourCar

**Car Sales & Rental Management Platform**

*Full-stack web application for managing vehicle listings, orders, and client accounts.*

[Overview](#-overview) · [Features](#-features) · [Architecture](#-architecture) · [Installation](#-installation) · [Usage](#-usage)

</div>

---

## 🎯 Overview

**YourCar** is a web application built with **Symfony 7.3** for managing a car dealership and rental agency. It provides a complete platform for vehicle inventory management, client order processing, rental/purchase announcements, and payment tracking — with separate interfaces for administrators and clients.

---

## 🎓 Academic Context

> Developed as an academic project at **TEK'UP** (2025–2026) as part of an engineering degree in Information and Communication Technologies, specializing in Network and System Security.

---

## ✨ Features

### 👤 Authentication & Access Control
- Form-based authentication with email + password
- CSRF token protection on all forms
- Role-Based Access Control (RBAC): `ROLE_ADMIN` and `ROLE_CLIENT`
- Automatic redirect on login based on user role
- Single Table Inheritance: `Utilisateur` → `Client` / `Administrateur`

### 🔧 Admin Panel
- **Vehicle management** — full CRUD: add/edit/delete cars with photo, documents, pricing, mileage, license plate, and status
- **Order management** — manage rental and purchase orders linked to clients and vehicles
- **Announcement management** — publish vehicle listings with title, description, publication date, and status
- **Client management** — view and manage registered clients
- **Payment tracking** — track payments associated with orders

### 👥 Client Portal
- Dedicated client dashboard
- Browse available vehicle catalogue
- Place rental or purchase orders
- Submit reviews (Avis)
- View order and payment history

---

## 🏗️ Architecture

### Data Model

```
Utilisateur (base - Single Table Inheritance)
├── Client          → Commande (1-N) → LigneCommande, Paiement
└── Administrateur

Voiture → LigneCommande
       → Annonce
```

### Entities

| Entity | Description | Key Fields |
|---|---|---|
| `Utilisateur` | Base user (STI) | email, password, nom, roles, type |
| `Client` | Customer (extends Utilisateur) | adresse, numTeleph |
| `Administrateur` | Admin (extends Utilisateur) | — |
| `Voiture` | Vehicle | matricule, marque, modele, annee, kilometrage, prixAchat, prixLocationJour, photo, statut |
| `Commande` | Order (rental or purchase) | type, prixCmd, date, statut, client |
| `LigneCommande` | Order line item | links Commande ↔ Voiture |
| `Paiement` | Payment | linked to Commande |
| `Annonce` | Vehicle listing/advertisement | titre, description, datePublication, statut, voiture |
| `Avis` | Customer review | linked to client |
| `Notification` | System notification | — |

### Routes

| Route | Method | Access | Description |
|---|---|---|---|
| `/` | GET | Public | Home — redirects by role |
| `/login` | GET/POST | Public | Login form |
| `/register` | GET/POST | Public | Client registration |
| `/catalogue` | GET | Public | Vehicle catalogue |
| `/admin/voiture` | GET | Admin | List all vehicles |
| `/admin/voiture/new` | GET/POST | Admin | Add new vehicle |
| `/admin/voiture/{id}/edit` | GET/POST | Admin | Edit vehicle |
| `/admin/commande` | GET | Admin | List all orders |
| `/admin/commande/new` | GET/POST | Admin | Create order |
| `/admin/annonce` | GET | Admin | List announcements |
| `/client/dashboard` | GET | Client | Client home |

---

## 🛠️ Technology Stack

| Category | Technology |
|---|---|
| Backend | PHP 8.2+, Symfony 7.3 |
| ORM | Doctrine ORM 3.5 + Migrations 3.7 |
| Templating | Twig 3.0 |
| Frontend | Symfony AssetMapper, Stimulus, UX Turbo |
| Database | MySQL |
| Authentication | Symfony Security Bundle (form login) |
| Messaging | Symfony Messenger (Doctrine transport) |
| Mailer | Symfony Mailer |
| Testing | PHPUnit |
| DevOps | Docker / Docker Compose |

---

## ⚙️ Installation

### Prerequisites
- PHP >= 8.2
- Composer
- MySQL
- Symfony CLI (optional but recommended)

### Clone the Repository
```bash
git clone https://github.com/rabeb-jerbi/projet-symfony.git
cd projet-symfony
```

### Install Dependencies
```bash
composer install
```

### Configure Environment
Create a `.env.local` file with your local settings:
```env
APP_SECRET=your_generated_secret_here
DATABASE_URL="mysql://root:your_password@127.0.0.1:3306/yourcar"
```

### Set Up the Database
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### Start the Development Server
```bash
symfony server:start
# or
php -S localhost:8000 -t public/
```

Access the app at: `http://localhost:8000`

---

## 🚀 Usage

### Default Roles

| Role | Access | Description |
|---|---|---|
| `ROLE_ADMIN` | `/admin/*` | Full vehicle, order, and client management |
| `ROLE_CLIENT` | `/client/*` | Browse catalogue, place orders, view history |

### Create an Admin User
```bash
php bin/console app:create-admin
# or register and manually update the role in the database
```

---

## 📁 Project Structure

```
yourcar/
├── assets/                  # Frontend JS/CSS (Stimulus, Turbo)
├── config/                  # Symfony configuration
│   ├── packages/            # Bundle configs (security, doctrine…)
│   └── routes/              # Route files
├── migrations/              # Doctrine database migrations
├── public/                  # Web root (index.php)
├── src/
│   ├── Controller/          # Controllers (Admin, Client, API)
│   │   ├── Admin/
│   │   └── Client/
│   ├── Entity/              # Doctrine entities
│   ├── Form/                # Symfony form types
│   ├── Repository/          # Doctrine repositories
│   └── Security/            # Authentication logic
├── templates/               # Twig templates
│   ├── admin/
│   ├── client/
│   ├── voiture/
│   ├── commande/
│   └── security/
├── tests/                   # PHPUnit tests
├── .env                     # Environment defaults
├── compose.yaml             # Docker Compose
└── composer.json
```

---

## ⚠️ Disclaimer

This project was developed for academic purposes. The `.env` file contains placeholder credentials only — configure your own `APP_SECRET` and `DATABASE_URL` in `.env.local` before running locally.

---

<div align="center">

**🚗 YourCar** — Vehicle sales & rental management built with Symfony 7.

</div>