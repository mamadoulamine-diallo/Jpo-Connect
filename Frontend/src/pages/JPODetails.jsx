import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import Header from '/src/components/Header';
import Footer from '/src/components/Footer';
import PinMap from "../assets/img/PinMap";
import DateIcon from '../assets/img/DateIcon'; 
import '../jpodetails.css'; 

const JPODetails = () => {
    const { id } = useParams();
    const [jpo, setJpo] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    
    const [formData, setFormData] = useState({
        nom: '',
        prenom: '',
        email: ''
    });

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
    };

useEffect(() => {
    const fetchJPO = async () => {
        try {
            setLoading(true);
            const response = await fetch('http://localhost/JPO-Connect/Backend/api/jpo.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }

            const data = await response.json();
            const selectedJpo = data.find(jpo => jpo.id_jpo === parseInt(id));
            
            if (!selectedJpo) {
                throw new Error('JPO non trouvée');
            }
            
            setJpo(selectedJpo);
            setError(null);
        } catch (err) {
            console.error('Erreur:', err);
            setError('Erreur lors du chargement des JPO');
        } finally {
            setLoading(false);
        }
    };

    fetchJPO();
}, [id]);

          const handleSubmit = async (e) => {
    e.preventDefault();

    // Validation simple
    if (!formData.nom.trim() || !formData.prenom.trim() || !formData.email.trim()) {
        alert('Veuillez remplir tous les champs');
        return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(formData.email)) {
        alert('Veuillez entrer un email valide');
        return;
    }

    setIsSubmitting(true);

    try {
     // appel a l'API pour l'inscription api visitor.php
        const response = await fetch('http://localhost/JPO-Connect/Backend/api/visitor.php', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                action: 'create',
                nom: formData.nom.trim(),
                prenom: formData.prenom.trim(),
                email: formData.email.trim().toLowerCase(),
                jpo_id: parseInt(id)  
            })
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Réponse du serveur:', errorText);
            throw new Error('Erreur lors de l\'inscription');
        }

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.error || 'Erreur lors de l\'inscription');
        }

        // Succès
        setFormData({ nom: '', prenom: '', email: '' });
        alert('Bien un mail va ette envoyer.');

    } catch (error) {
        console.error('Erreur détaillée:', error);
        alert(error.message || 'Une erreur est survenue lors de l\'inscription');
    } finally {
        setIsSubmitting(false);
    }
};


    if (loading) return (
        <div className="loading-container">
            <div>Chargement...</div>
        </div>
    );

    if (error) return (
        <div className="error-container">
            <div>Erreur: {error}</div>
        </div>
    );

    if (!jpo) return (
        <div className="error-container">
            <div>JPO non trouvée</div>
        </div>
    );

    return (
        <>
            <Header />
            <main>
                <div className="hero-header">
                    <h1>LaPlateforme {jpo.city} vous ouvre ses portes</h1>
                </div>

                <div className="main-container">
                    <div className="content-section">
                        <div className="image-section">
                            <img
                                src={jpo.image_url || '../../src/assets/img/image_details.png'}
                                alt={jpo.title}
                                className="event-image"
                            />
                        </div>

                        <div className="event-info">
                            <h2>{jpo.title}</h2>
                            <div className="info-item">
                                <DateIcon className="icon"/>
                                <span><strong>Date : </strong>{new Date(jpo.date_jpo).toLocaleDateString()}</span>
                            </div>
                            <div className="info-item">
                                <PinMap className="icon"/>
                                <span><strong>Lieu : </strong>{jpo.address}, {jpo.cp} {jpo.city}</span>
                            </div>
                        </div>

                        <div className="form-section">
                            <div className="form-container">
                                <h3 className="form-title">Inscription :</h3>
                                <form onSubmit={handleSubmit} className="registration-form">
                                    <div className="form-group">
                                        <label>Nom :</label>
                                        <input 
                                            type="text" 
                                            name="nom" 
                                            value={formData.nom}
                                            onChange={handleChange}
                                            required
                                            disabled={isSubmitting}
                                        />
                                    </div>

                                    <div className="form-group">
                                        <label>Prénom :</label>
                                        <input 
                                            type="text" 
                                            name="prenom" 
                                            value={formData.prenom}
                                            onChange={handleChange}
                                            required
                                            disabled={isSubmitting}
                                        />
                                    </div>

                                    <div className="form-group">
                                        <label>Email :</label>
                                        <input 
                                            type="email" 
                                            name="email" 
                                            value={formData.email}
                                            onChange={handleChange}
                                            required
                                            disabled={isSubmitting}
                                        />
                                    </div>

                                    <button 
                                        type="submit" 
                                        className="submit-btn"
                                        disabled={isSubmitting}
                                    >
                                        {isSubmitting ? 'Inscription en cours...' : 'S\'inscrire'}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div className="description-section">
                        <h2>Description de l'événement</h2>
                        <div 
                            className="description-content"
                            dangerouslySetInnerHTML={{ 
                                __html: jpo.description.replace(/\n/g, '<br>') 
                            }}
                        />
                    </div>
                </div>
            </main>
            <Footer/>
        </>
    );
};

export default JPODetails;