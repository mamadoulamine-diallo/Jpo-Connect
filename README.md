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
