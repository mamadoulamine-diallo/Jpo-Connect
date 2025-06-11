import React, { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
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
    
    // État pour le formulaire
    const [formData, setFormData] = useState({
        nom: '',
        prenom: '',
        email: ''
    });

    useEffect(() => {
        fetch('http://localhost/Jpo-Connect/Backend/api/jpo.php')
            .then(res => {
                if (!res.ok) {
                    throw new Error('Erreur lors du chargement des JPO');
                }
                return res.json();
            })
            .then(data => {
                const selectedJpo = data.find(jpo => jpo.id_jpo === parseInt(id));
                if (!selectedJpo) {
                    throw new Error('JPO non trouvée');
                }
                setJpo(selectedJpo);
                setLoading(false);
            })
            .catch(err => {
                setError(err.message);
                setLoading(false);
            });
    }, [id]);

    // Gestion du formulaire
    const handleChange = (e) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        
        try {
            const visitorResponse = await fetch('http://localhost/Jpo-Connect/Backend/api/visitor.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    action: 'create',
                    nom: formData.nom,
                    prenom: formData.prenom,
                    email: formData.email,
                    jpo_id: jpo.id_jpo
                })
            });

            if (!visitorResponse.ok) {
                const errorText = await visitorResponse.text();
                console.error('Server response:', errorText);
                throw new Error('Erreur lors de l\'inscription');
            }

            const responseData = await visitorResponse.json();

            if (responseData.success) {
                // Reset form
                setFormData({
                    nom: '',
                    prenom: '',
                    email: ''
                });
                alert('Inscription réussie !');
            } else {
                throw new Error(responseData.error || 'Erreur lors de l\'inscription');
            }

        } catch (error) {
            console.error('Erreur détaillée:', error);
            alert(error.message);
        }
    };

    if (loading) return <div>Chargement...</div>;
    if (error) return <div>Erreur: {error}</div>;
    if (!jpo) return <div>JPO non trouvée</div>;

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
                                        />
                                    </div>

                                    <button type="submit" className="submit-btn">
                                        S'inscrire
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