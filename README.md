<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel Badge"/>
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP Badge"/>
  <img src="https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge" alt="License Badge"/>
</p>

# Projet API Backend 🚀

Ce projet est une API RESTful développée avec Laravel. Elle fournit les fonctionnalités de base pour une application de gestion de petites annonces, incluant la gestion des utilisateurs, des annonces, des commentaires, des favoris et des "likes".

## 📋 Prérequis

Assurez-vous que votre environnement de développement dispose des éléments suivants :

- 🐘 PHP >= 8.1
- 🎼 Composer
- 🗄️ Un serveur de base de données (MySQL, PostgreSQL, etc.)
- 🟢 Node.js & NPM (pour la compilation des assets)

## ⚙️ Installation

1.  **Clonez le dépôt** 📂
    ```bash
    git clone <url-du-depot>
    cd backend
    ```

2.  **Installez les dépendances PHP** 📦
    ```bash
    composer install
    ```

3.  **Installez les dépendances JavaScript** 📦
    ```bash
    npm install
    ```

4.  **Créez le fichier d'environnement** 🔑
    Copiez le fichier d'exemple et générez votre clé d'application.
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5.  **Configurez votre base de données** 💾
    Modifiez le fichier `.env` avec les informations de connexion à votre base de données (DB_DATABASE, DB_USERNAME, DB_PASSWORD, etc.).

6.  **Exécutez les migrations** 🏗️
    Pour créer les tables dans la base de données :
    ```bash
    php artisan migrate
    ```
    Optionnel : pour peupler la base de données avec des données de test :
    ```bash
    php artisan db:seed
    ```

## ▶️ Démarrage du serveur

Pour lancer le serveur de développement local :

```bash
php artisan serve
```

L'API sera accessible à l'adresse `http://127.0.0.1:8000`.

## 🗺️ Endpoints de l'API

Voici un aperçu des routes principales disponibles :

### 👤 Authentification
- `POST /api/register` : Créer un nouvel utilisateur.
- `POST /api/login` : Connecter un utilisateur et obtenir un token.
- `POST /api/logout` : Déconnecter l'utilisateur (nécessite une authentification).

### 🏡 Annonces (Listings)
- `GET /api/listings` : Lister toutes les annonces.
- `GET /api/listings/{id}` : Afficher une annonce spécifique.
- `POST /api/listings` : Créer une nouvelle annonce (authentifié).
- `PUT /api/listings/{id}` : Mettre à jour une annonce (authentifié).
- `DELETE /api/listings/{id}` : Supprimer une annonce (authentifié).

### 💬 Commentaires (Comments)
- `POST /api/listings/{id}/comments` : Ajouter un commentaire à une annonce (authentifié).

### ⭐ Favoris (Favorites)
- `POST /api/listings/{id}/favorite` : Mettre une annonce en favori (authentifié).

### 👍 Likes
- `POST /api/listings/{id}/like` : "Liker" une annonce (authentifié).

## 🧪 Lancer les tests

Pour exécuter la suite de tests automatisés :

```bash
php artisan test
```
