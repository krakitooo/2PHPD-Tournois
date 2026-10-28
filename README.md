# Plateforme de Gestion de Tournois de Sport

## Présentation générale

Ce projet est une plateforme de gestion de tournois de sport, réalisée en Symfony 7. Il permet de :

- Gérer des **tournois**,  
- Gérer des **joueurs (utilisateurs)**,  
- Gérer les **inscriptions** aux tournois,  
- Gérer les **parties (matchs)**,  
- Offrir une **interface d’administration** (dashboard) sécurisée,  
- Offrir des **notifications** et une **API RESTful**.

Le projet respecte les normes RESTful, inclut une interface admin en **Twig**, des **tests unitaires**, des **fixtures**, ainsi qu’une **commande Symfony personnalisée**.


## Installation

1. **Récupérer le projet :**

```
Dézipper le projet
Ouvrir un terminal dans le dossier du projet
```

2. **Installer les dépendances :**

```bash
composer install
```

3. **Configurer la base de données :**

```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/SportTournament?serverVersion=mariadb-10.4.32""
```

4. **Créer la base & appliquer les migrations :**

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. **Clés JWT :**
Les clés sont dans les fichiers, mais dans le cas où il faudrait les générer à nouveau :
```
openssl genrsa -aes256 -out config/jwt/private.pem 4096

openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```
Bien mettre la passphrase **Supinf0!** quand demandé


6. **Charger les fixtures (données de test) :**

```bash
php bin/console doctrine:fixtures:load
```

7. **Lancer le serveur :**

```bash
symfony server:start
```


## 🛣️ Routes API principales

| Méthode | Route                                                            | Description                   |
| ------- | ---------------------------------------------------------------- | ----------------------------- |
| GET     | `/api/tournaments`                                               | Liste des tournois            |
| POST    | `/api/tournaments`                                               | Créer un tournoi              |
| GET     | `/api/tournaments/{id}`                                          | Détails d’un tournoi          |
| PUT     | `/api/tournaments/{id}`                                          | Modifier un tournoi           |
| DELETE  | `/api/tournaments/{id}`                                          | Supprimer un tournoi          |
| GET     | `/api/players`                                                   | Liste des joueurs             |
| POST    | `/register`                                                      | Créer un utilisateur          |
| GET     | `/api/players/{id}`                                              | Détails d’un joueur           |
| PUT     | `/api/players/{id}`                                              | Modifier un joueur            |
| DELETE  | `/api/players/{id}`                                              | Supprimer un joueur           |
| GET     | `/api/tournaments/{id}/registrations`                            | Inscriptions d’un tournoi     |
| POST    | `/api/tournaments/{id}/registrations`                            | Inscrire un joueur            |
| DELETE  | `/api/tournaments/{idTournament}/registrations/{idRegistration}` | Supprimer une inscription     |
| GET     | `/api/tournaments/{id}/sport-matchs`                             | Liste des matchs d’un tournoi |
| POST    | `/api/tournaments/{id}/sport-matchs`                             | Créer un match                |
| GET     | `/api/tournaments/{idTournament}/sport-matchs/{idSportMatchs}`   | Détails d’un match            |
| PUT     | `/api/tournaments/{idTournament}/sport-matchs/{idSportMatchs}`   | Modifier les scores           |
| DELETE  | `/api/tournaments/{idTournament}/sport-matchs/{idSportMatchs}`   | Supprimer un match            |

Authentification obligatoire (ROLE\_USER) sur l'API, sauf `/register`.


## 🏛️ Interface d’administration (Dashboard)

Accessible via `localhost:8000/admin`, l’interface est **sécurisée par rôle `ROLE_ADMIN`**.

Elle permet de :

* Visualiser / créer / éditer / supprimer les **joueurs**,
* Visualiser / créer / éditer / supprimer les **tournois**,
* Visualiser / créer / éditer / supprimer les **inscriptions**,
* Visualiser / créer / éditer / supprimer les **matchs**.

Toutes les pages utilisent **Twig** et un style custom.


## Notifications

- Notification envoyée aux participants d’un tournoi lorsque celui-ci est **remporté**.

- Notification envoyée à l’adversaire lorsqu’un joueur **met à jour son score** (si admin → pas de notif).

Les notifications sont **stockées en BDD** et accessibles via `/api/notifications` pour l’utilisateur authentifié.


## Commande Symfony

Une commande est disponible :

```bash
php bin/console app:player:stats {playerId} [tournamentId]
```

Elle affiche le **nombre de victoires/défaites** d’un joueur globalement ou dans un tournoi donné.


## Tests unitaires

Les **tests unitaires** sont présents dans `/tests` et couvrent :

- Entité `User`,
- Entité `Tournament`,
- Commande `app:players:stats`,
- Contrôleurs principaux.

Exécution :

```bash
php bin/phpunit
```


## Notes importantes

* Le **statut d’un tournoi** est dynamique et calculé lors des requêtes GET en fonction des dates.
* Le **statut d’un match** est mis automatiquement à **"terminé"** si les deux scores sont renseignés.
* Les **joueurs d’un match** doivent être **inscrits et confirmés** dans le tournoi concerné.


## Authentification

Authentification basée sur **sessions** côté dashboard et **JWT tokens** côté API.  
Clé publique/privée générée dans `/config/jwt`.

Dans les fixtures, on a généré des utilisateurs X allant de 1 à 5, pour chacun, voici les identifiants, en remplaçant X par le chiffre en question :
```
Identifiant : playerX
Mot de passe : passX
```
Et pour l'admin :
```
Identifiant : admin
Mot de passe : adminpass
```


## Technologies principales

* Symfony 7.x
* Doctrine ORM
* Twig
* JWT Authentication
* PHPUnit


## Auteurs

* Axel GIOVANNAI
* Marvin LOUVEL
* Bastien LIENHARD
