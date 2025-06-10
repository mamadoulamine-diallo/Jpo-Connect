import React, { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import Header from '/src/components/Header';
import Footer from '/src/components/Footer';
import  PinMap from "../assets/img/PinMap";
import DateIcon from '../assets/img/DateIcon'; 


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
            <div className="bg-[#0161FF] h-32 flex items-center justify-center ">
                <h1 className="text-white text-2xl md:text-3xl font-bold text-center">
                    LaPlateforme {jpo.city} vous ouvre ses portes
                </h1>
            </div>

            {/* Section principale alignée */}
            <div className="flex flex-col md:flex-row justify-center gap-10 px-6 py-10 mt-20">

                    {/* Image */}
                    <div className="w-full md:w-1/3 flex justify-center">
                        <img
                            src={jpo.image_url || '../../src/assets/img/image_details.png'}
                            alt={jpo.title}
                            className="w-[400px] h-[250px] object-cover border-2 border-blue-500"
                        />
                    </div>

                    {/* Infos Date & Lieu */}
                    <div className="w-full md:w-1/3 text-black">
                        <h2 className="text-lg font-bold mb-4">{jpo.title}</h2>

                        <p className="flex items-center mb-2">
                            <span className="mr-2"></span>
                            <span className='flex gap-2 items-center'>
                                <DateIcon className="h-[17px] w-[17px]"/> Date :
                                <strong> {new Date(jpo.date_jpo).toLocaleDateString()}</strong>
                            </span>
                        </p>

                        <p className="flex items-center">
                            <span className="mr-2"></span>
                            <span className='flex gap-2 items-center'>
                                <PinMap className="h-[17px] w-[17px]"/>Lieu : 
                                <strong>{jpo.address},{jpo.cp} {jpo.city}</strong>
                            </span>
                        </p>
                    </div>

                    {/* Formulaire d'inscription - Style original */}
                    <div className=" md:w-1/3 max-w-[400px] ">
                        <div className="border border-gray-300 p-4 rounded-md shadow-sm bg-white">
                            <h3 className="font-semibold mb-4 text-center text-black">
                                Inscription:
                            </h3>

                            <form onSubmit={handleSubmit} className="flex flex-col gap-3 h-[450px] overflow-y-auto justify-center items-center">
                                <div>
                                    <label className="block text-sm text-black">
                                        Nom:
                                    </label>
                                    <input 
                                        type="text" 
                                        name="nom" 
                                        value={formData.nom}
                                        onChange={handleChange}
                                        required
                                        className="border-b border-gray-400 w-[200px] p-1 text-black bg-transparent focus:outline-none focus:border-gray-600" 
                                    />
                                </div>

                                <div>
                                    <label className="block text-sm text-black">
                                        Prénom:
                                    </label>
                                    <input 
                                        type="text" 
                                        name="prenom" 
                                        value={formData.prenom}
                                        onChange={handleChange}
                                        required
                                        className="border-b border-gray-400 w-[200px] p-1 text-black bg-transparent focus:outline-none focus:border-gray-600" 
                                    />
                                </div>

                                <div>
                                    <label className="block text-sm text-black">
                                        Email:
                                    </label>
                                    <input 
                                        type="email" 
                                        name="email" 
                                        value={formData.email}
                                        onChange={handleChange}
                                        required
                                        className="border-b border-gray-400 w-[200px] p-1 text-black bg-transparent focus:outline-none focus:border-gray-600" 
                                    />
                                </div>

                                <button 
                                    type="submit" 
                                    className=" bg-[#E74A34] w-[100px] text-white justify-center items-center p-2 rounded-md hover:bg-[#FF6B5A] transition-colors duration-300 mt-4 self-center" 
                                >
                                    S'inscrire
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                        <div className='max-w-4xl mx-auto px-4 py-6'>
                                <h2 className="text-black text-2xl font-bold mt-10 mb-4">Description de l'événement</h2>
                                <p 
                                    className=" text-gray-700 px-4 leading-relaxed"
                                    dangerouslySetInnerHTML={{ 
                                        __html: jpo.description.replace(/\n/g, '<br>') 
                                    }}
                                />
                        </div>

                <div>
                    
                </div>
            </main>
            <Footer/>
        </>
    );
};

export default JPODetails;