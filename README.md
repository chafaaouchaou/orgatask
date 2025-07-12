# OrgaTask - Documentation

## Informations importantes

### Configuration Email
- **Adresse email** : `orgatask.app@gmail.com`
- **Mot de passe tiers** : `chmcixjfbzguihum`
- **Note** : Cette boîte mail sera supprimée à la fin de l'évaluation

### Variables d'environnement requises

```bash
# Configuration email
MAILER_DSN=smtp://orgatask.app@gmail.com:chmcixjfbzguihum@smtp.gmail.com:587
MAILER_SENDER=orgatask.app@gmail.com

# Configuration base de données
DATABASE_URL="mysql://taskmasteruser:Chafaa2025@127.0.0.1:3306/task_manager?serverVersion=8.0.32&charset=utf8mb4"
```

### Configuration base de données
- **Utilisateur** : `taskmasteruser`
- **Mot de passe** : `Chafaa2025`
- **Base de données** : `task_manager`

### Déploiement
Le projet sera déployé après soumission. Un email avec le lien vers l'application de test sera envoyé car l'installation locale peut être laborieuse.

## 📋 Fonctionnalités implémentées

### ✅ Complétées
- tout le projet à part .......

### ❌ Non implémentées (manque de temps)
- **Système de notifications** après création de tâche
  - **Idée proposée** : Champ `notif` dans la table `user` (boolean ou entier)
  - **Logique** : +1 quand l'utilisateur est concerné par une tâche, remise à 0 quand consulté
  - **Mise à jour** : HTTP classique ou protocole instantané avec Mercure

- **Automatisation des tests** après un push sur GitHub

  - **Idée proposée** : Utilisation d'une **pipeline CI/CD**



## 🧪 Tests

### Collection Postman
Une collection Postman complète est disponible dans le dossier `postman/` avec des tests pour tous les endpoints API.

### Exécution des tests
```bash
php bin/phpunit tests/Entity/ tests/Service/ tests/Controller/Api/
```

## 🗄️ Structure de la base de données

```plantuml
@startuml OrgaTask_Database_Structure
!define ENTITY class
!define PK <color:red><b>
!define FK <color:blue>
!define UK <color:green>

ENTITY User {
    PK id : int
    --
    UK email : string(128)
    password : string(255)
    name : string(128)
    roles : array
    isVerified : bool
}

ENTITY Task {
    PK id : int
    --
    title : string(255)
    description : text
    status : string(20)
    dueDate : datetime
    FK createdBy : int
}

ENTITY task_user {
    FK task_id : int
    FK user_id : int

}

' Relations
User ||--o{ Task : "creates (createdBy)"
User }o--o{ Task : "assigned to (assignedUsers)"

' Relation Many-to-Many via table de jointure
User ||--o{ task_user : ""
Task ||--o{ task_user : ""

note bottom of task_user : Table de jointure générée\nautomatiquement par Doctrine\npour la relation ManyToMany

@enduml
```



## 📁 Structure des dossiers

```
OrgaTask/
├── bin/                    # Scripts exécutables
├── config/                 # Configuration Symfony
│   ├── packages/          # Configuration des bundles
│   └── routes/            # Configuration des routes
├── docs/                   # Documentation
├── migrations/             # Migrations de base de données
├── postman/               # Collection Postman
├── public/                # Fichiers publics (CSS, JS, images)
├── src/                   # Code source
│   ├── Controller/        # Contrôleurs web et API
│   ├── Entity/            # Entités Doctrine
│   ├── Form/              # Formulaires Symfony
│   ├── Repository/        # Repositories Doctrine
│   ├── Security/          # Sécurité et authentification
│   └── Service/           # Services métier
├── templates/             # Templates Twig
└── tests/                 # Tests unitaires et d'intégration
```

## 📊 Entités principales

### User
- **id** : Identifiant unique
- **email** : Email unique (128 caractères)
- **password** : Mot de passe hashé (255 caractères)
- **name** : Nom d'utilisateur (128 caractères)
- **roles** : Rôles au format JSON
- **isVerified** : Statut de vérification email

### Task
- **id** : Identifiant unique
- **title** : Titre de la tâche (255 caractères)
- **description** : Description détaillée (texte)
- **status** : Statut de la tâche (20 caractères)
- **dueDate** : Date d'échéance (optionnelle)
- **createdBy** : Créateur de la tâche
- **assignedUsers** : Utilisateurs assignés (relation ManyToMany)

## 🔐 Sécurité

### Authentification
- JWT (JSON Web Token) via LexikJWTAuthenticationBundle pour l'api
- Hashage des mots de passe avec Symfony Security


## Installation locale

## 📚 API Endpoints

### Authentification
- `POST /api/login` - Connexion utilisateur
- `POST /api/register` - Inscription utilisateur

### Utilisateurs
- `GET /api/users` - Liste des utilisateurs
- `GET /api/users/{id}` - Détails d'un utilisateur
- `POST /api/users` - Créer un utilisateur (admin)
- `PUT /api/users/{id}` - Modifier un utilisateur

### Tâches
- `GET /api/tasks` - Liste des tâches
- `GET /api/tasks/{id}` - Détails d'une tâche
- `POST /api/tasks` - Créer une tâche
- `PUT /api/tasks/{id}` - Modifier une tâche
- `DELETE /api/tasks/{id}` - Supprimer une tâche

## 🛠️ Technologies utilisées

- **Backend** : Symfony 7.x
- **ORM** : Doctrine
- **API** : API Platform
- **Authentification** : Semfony securite
- **Authentification** : JWT
- **Email** : Symfony Mailer
- **Tests** : PHPUnit
- **Base de données** : MySQL

## 📈 Améliorations futures

1. **Système de notifications en temps réel**
   - Intégration Mercure pour les notifications push

2. **Fonctionnalités avancées**
-Consulter une tache en détail 


---
# OrgaTask - Documentation des Routes

## 📝 Routes de l'Application

Cette documentation liste toutes les routes disponibles dans l'application OrgaTask.

---

## 🌐 Routes Web (Interface Utilisateur)

### 🏠 **Pages Principales**

| Route | Méthode | Chemin | Description |
|-------|---------|---------|-------------|
| `app_home` | GET | `/` | Page d'accueil de l'application |
| `app_auth` | GET/POST | `/auth` | Page de gestion du profil utilisateur |
| `app_data` | GET | `/data` | Page de visualisation des données |

### 🔐 **Authentification**

| Route | Méthode | Chemin | Description |
|-------|---------|---------|-------------|
| `app_login` | GET/POST | `/login` | Page de connexion |
| `app_register` | GET/POST | `/register` | Page d'inscription |
| `app_logout` | GET | `/logout` | Déconnexion de l'utilisateur |

### 📋 **Gestion des Tâches**

| Route | Méthode | Chemin | Description |
|-------|---------|---------|-------------|
| `app_task` | GET | `/task` | Liste des tâches de l'utilisateur |
| `app_task_new` | GET/POST | `/task/new` | Création d'une nouvelle tâche |

---

## 🔌 API REST (Endpoints)

### 🛡️ **Authentification API**

| Route | Méthode | Chemin | Description |
|-------|---------|---------|-------------|
| `api_login_check` | POST | `/api/login_check` | Connexion JWT |
| `api_register` | POST | `/api/register` | Inscription utilisateur |
| `api_me` | GET | `/api/me` | Profil utilisateur connecté |

### 📋 **API Tâches**

#### Via API Platform (Format JSON-LD)

| Route | Méthode | Chemin | Description |
|-------|---------|---------|-------------|
| `_api_/tasks{._format}_get_collection` | GET | `/api/tasks.{_format}` | Liste des tâches (JSON-LD) |
| `_api_/tasks{._format}_post` | POST | `/api/tasks.{_format}` | Créer une tâche (JSON-LD) |
| `_api_/tasks/{id}{._format}_get` | GET | `/api/tasks/{id}.{_format}` | Détails d'une tâche (JSON-LD) |
| `_api_/tasks/{id}{._format}_put` | PUT | `/api/tasks/{id}.{_format}` | Modifier une tâche (JSON-LD) |
| `_api_/tasks/{id}{._format}_delete` | DELETE | `/api/tasks/{id}.{_format}` | Supprimer une tâche (JSON-LD) |

#### Via Contrôleur Personnalisé

| Route | Méthode | Chemin | Description |
|-------|---------|---------|-------------|
| `api_tasks_list` | GET | `/api/tasks` | Liste des tâches |
| `api_tasks_get` | GET | `/api/tasks/{id}` | Détails d'une tâche |
| `api_tasks_create` | POST | `/api/tasks` | Créer une tâche |
| `api_tasks_update` | PUT | `/api/tasks/{id}` | Modifier une tâche |
| `api_tasks_delete` | DELETE | `/api/tasks/{id}` | Supprimer une tâche |

### 👥 **API Utilisateurs**

| Route | Méthode | Chemin | Description |
|-------|---------|---------|-------------|
| `_api_/users{._format}_get_collection` | GET | `/api/users.{_format}` | Liste des utilisateurs |
| `_api_/users/{id}{._format}_get` | GET | `/api/users/{id}.{_format}` | Détails d'un utilisateur |
| `_api_/users{._format}_post` | POST | `/api/users.{_format}` | Créer un utilisateur |
| `_api_/users/{id}{._format}_put` | PUT | `/api/users/{id}.{_format}` | Modifier un utilisateur |
| `_api_/users/{id}{._format}_delete` | DELETE | `/api/users/{id}.{_format}` | Supprimer un utilisateur |
| `api_users_search` | GET | `/api/users/search` | Recherche d'utilisateurs |

---

## 🔧 **Routes Techniques**

### 📚 **API Platform**

| Route | Méthode | Chemin | Description |
|-------|---------|---------|-------------|
| `api_doc` | GET | `/api/docs.{_format}` | Documentation API |
| `api_entrypoint` | GET | `/api/{index}.{_format}` | Point d'entrée de l'API |
| `api_jsonld_context` | GET | `/api/contexts/{shortName}.{_format}` | Contexte JSON-LD |
| `api_genid` | GET | `/api/.well-known/genid/{id}` | Génération d'ID |
| `api_validation_errors` | GET | `/api/validation_errors/{id}` | Erreurs de validation |

### 🚨 **Gestion des Erreurs**

| Route | Méthode | Chemin | Description |
|-------|---------|---------|-------------|
| `_api_errors` | GET | `/api/errors/{status}.{_format}` | Erreurs API |
| `_api_validation_errors_problem` | GET | `/api/validation_errors/{id}` | Erreurs de validation (Problem) |
| `_api_validation_errors_hydra` | GET | `/api/validation_errors/{id}` | Erreurs de validation (Hydra) |
| `_api_validation_errors_jsonapi` | GET | `/api/validation_errors/{id}` | Erreurs de validation (JSON API) |
| `_preview_error` | ANY | `/_error/{code}.{_format}` | Prévisualisation d'erreur |


