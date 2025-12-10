# backend/Dockerfile
FROM php:8.4-fpm-alpine

# --- Étape 1: Installation des dépendances Système et PHP ---

# Mise à jour et installation des paquets système nécessaires pour les outils et PostgreSQL
RUN apk update && apk add --no-cache \
    git \
    unzip \
    postgresql-dev \
    curl \
    libpq-dev \
    bash \
    icu-dev \
    \
    && docker-php-ext-install pdo pdo_pgsql intl \
    \
    && curl -sS https://get.symfony.com/cli/installer | bash \
    # Déplacer l'exécutable dans un répertoire accessible par le PATH
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony \
    # Nettoyer les paquets temporaires (facultatif mais recommandé pour réduire la taille)
    && apk del curl \
    # Ajout d'autres outils (ex: pour le développement/debugging)
    bash \
    # Nettoyage
    && rm -rf /var/cache/apk/*
    
# Installation et activation des extensions PHP (pdo, pdo_pgsql)
RUN docker-php-ext-install pdo pdo_pgsql

# Nouvelle étape pour créer et configurer le php.ini
# La base Alpine ne fournit pas de php.ini par défaut.
RUN { \
    echo 'short_open_tag = Off'; \
    echo 'memory_limit = 512M'; \
    echo 'date.timezone = Europe/Paris'; \
    } > /usr/local/etc/php/conf.d/custom.ini
# --- Étape 2: Installation de Composer ---

# Copier l'exécutable Composer depuis une image Composer officielle (Multi-stage build)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# --- Étape 3: Gestion des Dépendances PHP (Optimisation du Cache) ---

# 1. Copier UNIQUEMENT les fichiers de définition des dépendances
# Docker invalide le cache seulement si ces fichiers changent.
COPY composer.json symfony.lock ./

# 2. Installer les dépendances
# Les flags recommandés pour les builds Docker
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-scripts

# --- Étape 4: Copie du Code Applicatif ---

# Copier tout le reste du code du projet
# Cette étape est rapide et ne se fait qu'une seule fois, après l'étape lente 'composer install'
COPY . .

# Définir l'utilisateur pour les étapes de runtime
# RUN chown -R www-data:www-data /var/www/html
USER www-data

# Commande par défaut (laisse PHP-FPM démarrer)
CMD ["php-fpm"]