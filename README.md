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
### 1. Inscription
**Créer un nouveau compte utilisateur**

```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "monmotdepasse"
}
```

**Réponse attendue :**
- Status: `200` ou `201`
- Body: `{"message": "...", "user": {...}}`

### 2. Connexion
**Obtenir un token JWT**

```http
POST /api/login_check
Content-Type: application/json

{
  "username": "john@example.com",
  "password": "monmotdepasse"
}
```

**Réponse attendue :**
- Status: `200`
- Body: `{"token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."}`

### 3. Profil utilisateur
**Récupérer les informations de l'utilisateur connecté**

```http
GET /api/me
Authorization: Bearer {{jwt_token}}
```

**Réponse attendue :**
- Status: `200`
- Body: `{"user": {"id": 1, "name": "John Doe", "email": "john@example.com"}}`

---

## 📋 Gestion des tâches

### 1. Lister toutes les tâches
**Récupérer la liste des tâches**

```http
GET /api/tasks
Authorization: Bearer {{jwt_token}}
```

**Réponse attendue :**
- Status: `200`
- Body: `{"data": [...], "pagination": {"current_page": 1, "total_pages": 1}}`

### 2. Lister avec filtres
**Récupérer les tâches avec filtres et pagination**

```http
GET /api/tasks?status=todo&page=1&limit=5&order=desc
Authorization: Bearer {{jwt_token}}
```

**Paramètres disponibles :**
- `status` : Filtrer par statut (todo, in_progress, done)
- `page` : Numéro de page (défaut: 1)
- `limit` : Nombre d'éléments par page (défaut: 10)
- `order` : Ordre de tri (asc, desc)

### 3. Créer une tâche
**Créer une nouvelle tâche**

```http
POST /api/tasks
Authorization: Bearer {{jwt_token}}
Content-Type: application/json

{
  "title": "Nouvelle tâche API",
  "description": "Description de la tâche créée via API",
  "status": "todo",
  "dueDate": "2024-12-31T23:59:59+00:00"
}
```

**Réponse attendue :**
- Status: `201`
- Body: `{"message": "...", "data": {"id": 1, "title": "...", ...}}`

### 4. Créer une tâche avec utilisateurs assignés
**Créer une tâche et l'assigner à des utilisateurs**

```http
POST /api/tasks
Authorization: Bearer {{jwt_token}}
Content-Type: application/json

{
  "title": "Tâche avec utilisateurs assignés",
  "description": "Cette tâche sera assignée à des utilisateurs",
  "status": "todo",
  "dueDate": "2024-12-31T23:59:59+00:00",
  "assignedUsers": [
    {"id": 1}
  ]
}
```

### 5. Récupérer une tâche par ID
**Obtenir les détails d'une tâche spécifique**

```http
GET /api/tasks/{{task_id}}
Authorization: Bearer {{jwt_token}}
```

**Réponse attendue :**
- Status: `200`
- Body: `{"data": {"id": 1, "title": "...", "status": "...", ...}}`

### 6. Modifier une tâche
**Mettre à jour une tâche existante**

```http
PUT /api/tasks/{{task_id}}
Authorization: Bearer {{jwt_token}}
Content-Type: application/json

{
  "title": "Tâche mise à jour",
  "description": "Description mise à jour via API",
  "status": "in_progress"
}
```

**Réponse attendue :**
- Status: `200`
- Body: `{"message": "...", "data": {"status": "in_progress", ...}}`

### 7. Supprimer une tâche
**Supprimer une tâche**

```http
DELETE /api/tasks/{{task_id}}
Authorization: Bearer {{jwt_token}}
```

**Réponse attendue :**
- Status: `200`
- Body: `{"message": "Tâche supprimée avec succès"}`

---

## 👥 Gestion des utilisateurs

### 1. Lister tous les utilisateurs
**Récupérer la liste des utilisateurs**

```http
GET /api/users
Authorization: Bearer {{jwt_token}}
```

**Réponse attendue :**
- Status: `200`
- Body: `[{"id": 1, "name": "John Doe", "email": "john@example.com"}, ...]`

### 2. Rechercher des utilisateurs
**Rechercher des utilisateurs par nom**

```http
GET /api/users/search?q=John
Authorization: Bearer {{jwt_token}}
```

**Réponse attendue :**
- Status: `200`
- Body: `{"users": [{"id": 1, "name": "John Doe", ...}]}`

---

## ⚠️ Gestion des erreurs

### 1. Accès non autorisé
**Test sans token d'autorisation**

```http
GET /api/tasks
# Pas de header Authorization
```

**Réponse attendue :**
- Status: `401`
- Message: Unauthorized

### 2. Token invalide
**Test avec un token invalide**

```http
GET /api/tasks
Authorization: Bearer invalid_token
```

**Réponse attendue :**
- Status: `401`
- Message: Invalid token

### 3. Ressource non trouvée
**Test avec un ID de tâche inexistant**

```http
GET /api/tasks/99999
Authorization: Bearer {{jwt_token}}
```

**Réponse attendue :**
- Status: `404`
- Body: `{"message": "Tâche non trouvée"}`
