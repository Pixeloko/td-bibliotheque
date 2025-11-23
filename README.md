# 📚 Gestion de bibliothèque 📚

Page html/php permettant de gérer les données dans la base de données 'bibliotheque', contenant trois schémas (membres, emprunts, livres).

## Table des matières
- [Installation](#installation)
- [Fonctionnalités](#fonctionnalites)
- [Configuration](#configuration)

## Installation
1. Cloner en local ce projet
2. Créer la base de données 
3. Tester sur seveur local

```bash
git clone https://github.com/yourusername/yourproject.git](https://github.com/Pixeloko/td-bibliotheque
```

## Fonctionnalités
### Fonctions livres
- Retrouver des infos sur les livres ayant tel id, telle catégorie
- Vérifier la disponibilité d'un livre
- Incrémenter/Décrémenter des copies

### Fonctions membres
- Retrouver des infos sur un membre avec son id, son email
- Créer un nouveau membre

### Fonctions emprunts
- Retrouver des infos sur les emprunts actifs via l'id membre ou  via l'id emprunt
- Créer un emprunt
- Marquer l'emprunt comme retourné

## Configuration

Ressources nécessaire pour la mise en place de l'environnement.
Création des directories et des fichiers dans le dépôt td-bibliotheque

```
td-bibliotheque/
├── config/
│   └── database.php          # Configuration et connexion PDO
├── functions/
│   ├── books.php             # Fonctions de gestion des livres
│   ├── members.php           # Fonctions de gestion des membres
│   └── borrowings.php        # Fonctions de gestion des emprunts
├── includes/
│   └── helpers.php           # Fonctions utilitaires
└── index.php                 # Point d'entrée et démonstration
```

## Contact
https://pixeloko.github.io/CV/ # td-bibliotheque
