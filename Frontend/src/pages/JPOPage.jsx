import React, { useEffect, useState } from "react";

export default function JPOPage() {
  const [jpo, setJpo] = useState(null);
  const [form, setForm] = useState({ first_name: "", last_name: "", email: "" });
  const [message, setMessage] = useState("");

  useEffect(() => {
    fetch("http://localhost/jpo-access/backend/api/jpo.php")
      .then((response) => {
        if (!response.ok) {
          throw new Error("Erreur réseau");
        }
        return response.json();
      })
      .then((data) => setJpo(data))
      .catch((error) => {
        console.error("Erreur fetch:", error);
      });
  }, []);

  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

 const handleSubmit = (e) => {
  e.preventDefault();

  const inscriptionData = { ...form, jpo_id: jpo.id_jpo };

  fetch("http://localhost/jpo-access/backend/api/registration.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(inscriptionData),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Erreur lors de l'inscription");
      }
      return response.json();
    })
    .then((data) => {
      setMessage(data.message);

      // Si l'inscription a réussi, on envoie l'email
      const mailData = {
        email: form.email,
        prenom: form.first_name,
        nom: form.last_name,
        jpo: `${jpo.date_jpo} à ${jpo.address}, ${jpo.cp} ${jpo.city}`,
      };

      return fetch("http://localhost/jpo-access/backend/api/mail.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(mailData),
      });
    })
    .then((res) => res.json())
    .then((emailResponse) => {
      if (emailResponse.success) {
        console.log("Email envoyé");
      } else {
        console.error("Erreur lors de l'envoi de l'email :", emailResponse.message);
      }
    })
    .catch((error) => {
      console.error("Erreur fetch POST:", error);
    });
};


  if (!jpo) return <p>Chargement...</p>;

  return (
    <div className="p-4">
      <h2 className="text-xl font-bold mb-2">Prochaine Journée Portes Ouvertes</h2>
      <p><strong>Date :</strong> {jpo.date_jpo}</p>
      <p><strong>Adresse :</strong> {jpo.address}, {jpo.cp} {jpo.city}</p>
      <p><strong>Téléphone :</strong> {jpo.phone}</p>

      <h3 className="mt-6 text-lg font-semibold">Inscription</h3>
      <form onSubmit={handleSubmit} className="space-y-2">
        <input name="first_name" onChange={handleChange} placeholder="Prénom" required className="border p-2 block w-full" />
        <input name="last_name" onChange={handleChange} placeholder="Nom" required className="border p-2 block w-full" />
        <input type="email" name="email" onChange={handleChange} placeholder="Email" required className="border p-2 block w-full" />
        <button className="bg-blue-600 text-white p-2 mt-2">S'inscrire</button>
      </form>
      {message && <p className="mt-2 text-green-600">{message}</p>}
    </div>
  );
}
