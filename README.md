# 🎓 JPO Connect - La Plateforme\_

Une application web permettant aux futurs étudiants de s'inscrire aux Journées Portes Ouvertes (JPO) dans tous les campus de La Plateforme\_.

## 🚀 Objectifs

- Inscription / désinscription aux JPO
- Notifications par email
- Recherche de JPO par localisation
- Système de commentaires + modération
- Tableau de bord pour l’équipe de recrutement
- Gestion des rôles utilisateurs (admin, responsable, salarié)

## 🧱 Stack Technique

- Frontend : ReactJS
- Backend : PHP natif (POO, PDO)
- Auth : JWT
- Notifications : PHPMailer

## 🗂️ Structure du projet

jpo-access/
├── backend/
│ ├── api/
│ │ ├── jpo.php
│ │ ├── user.php
│ │ ├── registration.php
│ │ ├── comment.php
│ │ └── content.php
│ ├── classes/
│ │ ├── Database.php
│ │ ├── JPO.php
│ │ ├── User.php
│ │ ├── Comment.php
│ │ └── Registration.php
│ ├── config/
│ │ └── database.php
│ └── vendor/
├── frontend/
│ ├── src/
│ │ ├── components/
│ │ ├── pages/
│ │ └── App.js
├── README.md
├── .gitignore

## Démarrer le frontend

````bash
cd frontend
npm install
npm run dev

## Flux de données

Endpoint : POST /api/admin

## Requete

{
  "action": "login",
  "email": "admin@example.com",
  "password": "motdepasse"
}
## Réponse (succès)

{
  "id": 1,
  "email": "admin@example.com",
  "role": "Directeur"
}

#Réponse (erreur)

{
  "error": "E-mail ou mot de passe incorrect"
}

## Instructions

Utilisez fetch pour appeler l’endpoint :

fetch('/backend/api/admin', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ action: 'login', email: 'admin@example.com', password: 'motdepasse' })
})
  .then(response => response.json())
  .then(data => {
    if (data.error) {
      alert(data.error);
    } else {
      sessionStorage.setItem('admin', JSON.stringify(data));
      alert('Connexion réussie !');
    }
  })
  .catch(error => alert('Erreur : ' + error.message));

  Stockez les données dans sessionStorage pour vérifier le rôle dans vos composants React.

  # JPO Connect Backend

## Endpoints

### GET /backend/api/jpo

- **Description**: List all JPOs (admin only).
- **Response (success)** :
  ```json
  [
    {
      "id_jpo": 1,
      "title": "JPO Lycée 2025",
      "date_jpo": "2025-06-15",
      "description": null,
      "created_at": "2025-06-08 10:13:09",
      "city": "Marseille",
      "address": "123 Rue Exemple",
      "cp": 13001,
      "phone": 123456789,
      "first_name": "Jean",
      "last_name": "Dupont",
      "admin_email": "admin@example.com"
    }
  ]

  Response (error):

  {
  "error": "Unauthorized"
 }

 Exemple(fecth):

 fetch('/backend/api/jpo.php', {
  method: 'GET',
  credentials: 'include'
})
  .then(response => response.json())
  .then(data => console.log(data))
  .catch(error => console.error('Error:', error));

  ##  POST /backend/api/jpo.php
Description : Create a new JPO (Directors only or Pape only).
Request:
{
  "action": "create",
  "title": "JPO Collège 2025",
  "date_jpo": "2025-08-01",
  "site_id": 1,
  "description": "Visite du collège."
}

Response(success):

{
  "id_jpo": 2,
  "message": "JPO created successfully"
}

response(error):

{
  "error": "Only Directors can create JPOs"
}

Exemple(fetch):

fetch('/backend/api/jpo.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json; charset=utf-8' },
  body: JSON.stringify({
    action: 'create',
    title: 'JPO Collège 2025',
    date_jpo: '2025-08-01',
    site_id: 1,
    description: 'Visite du collège.'
  }),
  credentials: 'include'
})
  .then(response => response.json())
  .then(data => console.log(data))
  .catch(error => console.error('Error:', error));

  ### Notes
- The `id_jpo` column in the `jpo` table is now `AUTO_INCREMENT`, removing the need for manual ID generation.
````
### POST /backend/api/visitor.php

- **Description**: Login as a visitor.
- **Request**:
  ```json
  {
    "action": "login",
    "email": "marie@example.com",
    "password": "visitorpass"
  }

  Response(success):
  {
  "id": 1,
  "email": "marie@example.com",
  "message": "Login successful"
}

  Response(error):

  {
  "error": "Invalid email"
}

## POST /backend/api/inscription.php
Description: Register or unregister a visitor to/from a JPO (visitor only).
Request (create)

{
  "action": "create",
  "jpo_id": 1,
  "visitor_id": 1
}

Request (delete)

{
  "action": "delete",
  "jpo_id": 1,
  "visitor_id": 1
}

Response (success):

{
  "id_inscription": 1,
  "message": "Inscription successful"
}

{
  "message": "Unregistration successful"
}

Response (error):

{
  "error": "Unauthorized"
}

## GET /backend/api/inscription.php
Description: List all inscriptions (admin only).