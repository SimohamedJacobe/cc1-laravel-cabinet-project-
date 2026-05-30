# Cabinet Médical - Système de Réservation de Rendez-vous

Un système complet et moderne de gestion des réservations de rendez-vous pour un cabinet médical, conçu en PHP avec Laravel 11, Tailwind CSS, Alpine.js et Livewire.

## Description du Projet

Cette application web permet aux patients de réserver facilement leurs consultations en ligne, et aux praticiens (médecins, administrateurs) de suivre et d'administrer l'emploi du temps du cabinet en temps réel.

### Fonctionnalités clés :
- **Gestion Complète des Rendez-vous (CRUD)** : Réservation, modification, consultation et annulation de rendez-vous.
- **Authentification Sécurisée & Rôles** : Accès contrôlé par rôle (Administrateur, Médecin, Patient).
- **Interface Utilisateur Moderne** : Un design épuré, adaptatif (Responsive) et compatible avec le mode sombre (Dark Mode).
- **Modales Alpine.js** : Réservation rapide et confirmation d'annulation fluides sans rechargement de page.
- **Recherche temps réel avec Axios** : Filtrage instantané et asynchrone des rendez-vous par patient, email, service médical ou notes.
- **Internationalisation (i18n)** : Traduction complète de l'interface en Français (FR) et Anglais (EN).
- **Notifications Automatisées** : Envoi automatique d'un email HTML de confirmation lors de la réservation d'un rendez-vous.
- **API REST** : Points de terminaison pour lister les rendez-vous et réserver depuis des systèmes externes.

---

## Instructions d'Installation

L'application utilise une base de données SQLite native par défaut, ce qui rend l'installation extrêmement rapide et ne nécessite aucun serveur de base de données externe.

### Étapes d'installation :

1. **Cloner le dépôt et accéder au dossier** :
   ```bash
   git clone <repository-url>
   cd medical-cabinet
   ```

2. **Installer les dépendances PHP (Composer)** :
   ```bash
   composer install
   ```

3. **Configurer les variables d'environnement** :
   Copier le fichier d'exemple `.env` (il est préconfiguré pour utiliser SQLite et le pilote de mail `log`) :
   ```bash
   cp .env.example .env
   # Sur Windows (PowerShell) :
   # Copy-Item .env.example .env
   ```

4. **Générer la clé d'application** :
   ```bash
   php artisan key:generate
   ```

5. **Créer la base de données SQLite et exécuter les migrations & seeders** :
   ```bash
   # Crée le fichier SQLite s'il n'existe pas
   touch database/database.sqlite
   
   # Exécute les migrations et injecte les données de test
   php artisan migrate:fresh --seed
   ```

6. **Installer et compiler les dépendances Front-end (NPM)** :
   ```bash
   npm install
   npm run build
   ```

7. **Lancer le serveur de développement Laravel** :
   ```bash
   php artisan serve
   ```
   L'application sera accessible sur : `http://localhost:8000`

---

## Identifiants de Connexion par Défaut

Les comptes de test suivants ont été créés via les seeders :

| Rôle | Email | Mot de passe | Description |
| :--- | :--- | :--- | :--- |
| **Administrateur** | `admin@example.com` | `password` | Accès total, gestion de tous les rendez-vous et patients. |
| **Médecin** | `doctor@example.com` | `password` | Vue d'ensemble de tous les rendez-vous de tous les patients. |
| **Patient** | `patient@example.com` | `password` | Réservation et suivi de ses propres rendez-vous uniquement. |

---

## Documentation de l'API REST

L'application expose des endpoints REST documentés ci-dessous pour interagir avec des systèmes externes.

### 1. Lister les rendez-vous
Retourne tous les rendez-vous présents dans le cabinet, incluant les relations de l'utilisateur (patient) et du service médical associé au format JSON.

- **Méthode** : `GET`
- **URL** : `/api/appointments`
- **Exemple de réponse JSON (200 OK)** :
  ```json
  [
    {
      "id": 1,
      "user_id": 3,
      "service_id": 2,
      "appointment_date": "2026-06-15T14:30:00.000000Z",
      "status": "pending",
      "notes": "Consultation annuelle",
      "created_at": "2026-05-30T14:00:00.000000Z",
      "updated_at": "2026-05-30T14:00:00.000000Z",
      "user": {
        "id": 3,
        "name": "Patient User",
        "email": "patient@example.com"
      },
      "service": {
        "id": 2,
        "name": "Cardiology Checkup",
        "price": 120,
        "duration_minutes": 45
      }
    }
  ]
  ```

### 2. Réserver un rendez-vous (requête externe)
Permet à un service tiers de planifier une nouvelle consultation. Un email de confirmation HTML est automatiquement envoyé au patient.

- **Méthode** : `POST`
- **URL** : `/api/appointments`
- **En-têtes requis** : `Content-Type: application/json`, `Accept: application/json`
- **Champs de validation requis** :
  - `user_id` : ID de l'utilisateur patient (doit exister dans la table `users`)
  - `service_id` : ID du service médical choisi (doit exister dans la table `services`)
  - `appointment_date` : Date et heure de rendez-vous valide dans le futur
  - `notes` : Texte optionnel (chaîne de caractères)
- **Exemple de payload JSON (Request)** :
  ```json
  {
    "user_id": 3,
    "service_id": 2,
    "appointment_date": "2026-06-20 10:00:00",
    "notes": "Réservation effectuée via API externe."
  }
  ```
- **Exemple de réponse JSON (201 Created)** :
  ```json
  {
    "id": 26,
    "user_id": 3,
    "service_id": 2,
    "appointment_date": "2026-06-20T10:00:00.000000Z",
    "notes": "Réservation effectuée via API externe.",
    "status": "pending",
    "created_at": "2026-05-30T14:42:00.000000Z",
    "updated_at": "2026-05-30T14:42:00.000000Z",
    "user": {
      "id": 3,
      "name": "Patient User",
      "email": "patient@example.com"
    },
    "service": {
      "id": 2,
      "name": "Cardiology Checkup",
      "price": 120,
      "duration_minutes": 45
    }
  }
  ```
- **Réponse d'erreur de validation (422 Unprocessable Entity)** :
  ```json
  {
    "message": "The appointment date must be a date after now.",
    "errors": {
      "appointment_date": [
        "The appointment date must be a date after now."
      ]
    }
  }
  ```
