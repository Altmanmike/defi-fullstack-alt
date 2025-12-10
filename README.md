## 🎯 Aperçu du Défi Technique

Le cœur du défi est de créer une solution pour **calculer la distance entre deux stations de train** en utilisant des données fournies (`stations.json`, `distances.json`) et d'intégrer des fonctionnalités de **statistiques** liées aux trajets créés.

| Composant | Technologie Principale | Objectif |
| :--- | :--- | :--- |
| **Backend (API)** | PHP 8.4 (Framework optionnel) | Implémenter strictement l'API REST selon la spécification **OpenAPI fournie** pour le routage de train et les statistiques. |
| **Frontend (UI)** | TypeScript 5 + Vue.js 3 / Vuetify 3 | Créer une interface pour la **saisie du trajet** (Station A $\rightarrow$ Station B + Type de Trajet) et la **consultation des statistiques**. |
| **Infrastructure** | Docker Engine 25 + Docker Compose | Fournir un environnement complet (backend, frontend, DB, proxy) démarrable en **une commande** (`docker compose up -d`). |
| **Qualité & Processus** | TDD, DDD, CI/CD, PHPUnit, Vitest/Jest | Démontrer une approche de développement professionnelle (tests, lint, sécurité, versioning). |

---

## 🏗️ Recommandations d'Architecture et d'Étapes

### 1. ⚙️ Initialisation de l'Infrastructure (Docker)

C'est la première étape cruciale pour respecter le livrable "déploiement en une commande".

* **Structure de Répertoire :** Créez des dossiers séparés (`backend`, `frontend`, `docker`).
* **`docker-compose.yml` :** Définissez au moins quatre services :
    * `backend` (basé sur une image PHP 8.4 avec extensions nécessaires).
    * `frontend` (basé sur Node.js pour le build, et Nginx pour la production).
    * `database` (PostgreSQL ou MariaDB, comme suggéré).
    * `reverse_proxy` (Nginx ou Caddy, si vous voulez gérer le HTTPS/les secrets pour la sécurité).
* **Instructions Claires :** Assurez-vous d'avoir un fichier d'instructions (dans le README de votre solution) pour le lancement (`docker compose up -d`).

### 2. 🛡️ Le Backend (PHP 8.4)

* **Parsing des Données :** Démarrez par lire et structurer les données de `stations.json` et `distances.json`.
* **Design Domain-Driven (DDD) :** Même sans utiliser un Framework complet, structurez votre code autour de concepts métiers clairs : `Station`, `Trajet`, `Ligne`, `Réseau` (ou `Graphique`).
* **Implémentation de l'API :**
    * Le point le plus critique est de **respecter la spécification OpenAPI fournie**.
    * Si un Framework (comme **Symfony**) est utilisé, commencez par définir les routes et les contrôleurs.
* **Point Bonus (Algorithme) :** Implémenter **Dijkstra** ou un algorithme de recherche de chemin (comme A*) est fortement recommandé pour le routage. Cela démontre une compétence algorithmique.
* **Tests (TDD) :** Écrivez les tests PHPUnit avant ou en parallèle du code. Visez une couverture élevée, notamment sur l'algorithme de routage.

### 3. 🖥️ Le Frontend (TypeScript 5, Vue 3, Vuetify)

* **Setup TypeScript :** Assurez-vous que le projet Vue/TS est correctement configuré.
* **Interface de Saisie :** Deux `selects` ou `autocompletes` pour la station de départ et d'arrivée, et un `select` pour le type de trajet (code analytique).
* **Affichage du Résultat :** Afficher la distance calculée en consommant l'API backend.
* **Statistiques :** Développez un écran pour afficher les statistiques agrégées (Point Bonus).
* **Tests :** Utilisez Vitest ou Jest pour les tests unitaires des composants et des logiques métier (si elles existent côté client).

### 4. 🚀 CI/CD, Qualité et Sécurité

Ceci est un critère d'évaluation majeur ! Même si vous utilisez un repo GitHub, vous pouvez utiliser **GitHub Actions** pour simuler le pipeline.

* **Build :** Un job pour construire les images Docker (`backend` et `frontend`).
* **Qualité :** Jobs pour exécuter :
    * **Lint** (PHPCS, ESLint, Prettier).
    * **Tests** (PHPUnit, Vitest/Jest) avec échec si le seuil de couverture n'est pas atteint.
* **Sécurité (SAST) :** Utilisez des outils comme **PHPStan** (PHP) et **Trivy** (pour scanner les images Docker et les dépendances npm/composer).
* **Sécurité (Authentification/HTTPS) :** Gérez les communications sécurisées (HTTPS) via le reverse proxy Docker (par exemple, en utilisant des certificats auto-signés pour le développement ou en documentant l'utilisation de Let's Encrypt).

### 5. Installation en local :

* **Docker :** docker compose up -d (si problèmes docker compose down -v puis docker compose up -d --build --force-recreate)
* **Backend :** docker compose run --rm backend composer install
* **Frontend :** cd frontend puis npm install
---