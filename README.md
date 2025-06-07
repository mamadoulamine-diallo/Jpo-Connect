# 🎓 JPO Connect - La Plateforme_

Une application web permettant aux futurs étudiants de s'inscrire aux Journées Portes Ouvertes (JPO) dans tous les campus de La Plateforme_.

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

```bash
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