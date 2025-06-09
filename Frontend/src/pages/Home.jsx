import React, { useEffect, useState } from 'react';
import Header from '/src/components/Header';
import Footer from '/src/components/Footer';
import CardJpo from '/src/components/CardJpo';
import RightArrow from '../assets/img/RightArrow';
import DropdownArrow from '../assets/img/DropdownArrow';

const Home = () => {
    const [jpos, setJpos] = useState([]);
    const [selectedCity, setSelectedCity] = useState(null);

    useEffect(() => {
        fetch('http://localhost/Jpo-Connect/Backend/api/jpo.php')
            .then(res => res.json())
            .then(data => setJpos(data))
            .catch(err => console.error('Erreur lors du fetch JPO:', err));
    }, []);

    // Trier par date croissante
    const sortedJpos = [...jpos].sort((a, b) => new Date(a.date_jpo) - new Date(b.date_jpo));

    // Grouper par ville et prendre les 6 plus proches pour chaque ville
    const jposByCity = {};
    sortedJpos.forEach(jpo => {
        if (!jposByCity[jpo.city]) jposByCity[jpo.city] = [];
        if (jposByCity[jpo.city].length < 6) {
            jposByCity[jpo.city].push(jpo);
        }
    });

    // Liste des villes pour les onglets
    const cities = Object.keys(jposByCity);

    // Préselectionner la première ville si aucune sélection
    useEffect(() => {
        if (!selectedCity && cities.length > 0) {
            setSelectedCity(cities[0]);
        }
    }, [cities, selectedCity]);

    // JPOs de la ville sélectionnée
    const selectedJpos = selectedCity ? jposByCity[selectedCity] || [] : [];

    return (
        <div>
            <Header />
                <main className="">
                    <section id="nextJpoSection" className="bg-(--blue-color)">
                        <h1 className="text-xl">Journées portes ouvertes à venir</h1>
                        <div id="divCards" className="flex flex-wrap justify-between gap-4">
                            {Object.entries(jposByCity).map(([city, cityJpos]) => (
                            <div key={city}>
                                <div className="flex flex-wrap gap-4">
                                    {cityJpos.map(jpo => (
                                        <CardJpo key={jpo.id} jpo={jpo} />
                                    ))}
                                </div>
                            </div>
                        ))}
                        </div>
                    </section>
                    <section id="byCitySection" className="bg-white">
                        <div id="cityBtns" className="flex gap-4">
                            {cities.map(city => (
                                <button
                                    key={city}
                                    className={`rounded cursor-pointer ${selectedCity === city ? 'bg-(--blue-color) text-white' : 'bg-gray-200 text-black'}`}
                                    onClick={() => setSelectedCity(city)}
                                >
                                    {city}
                                </button>
                            ))}
                        </div>
                        <div id="divCards" className="flex flex-wrap gap-4">
                            {selectedJpos.length > 0 ? (
                                selectedJpos
                                    .slice() // Crée une copie pour ne pas muter l'original
                                    .sort((a, b) => new Date(a.date_jpo) - new Date(b.date_jpo) // Trie par date croissante
                                    )
                                    .map(jpo => (
                                        <CardJpo key={jpo.id} jpo={jpo} />
                                    ))
                            ) : (
                                <p className="text-gray-500">Sélectionnez une ville pour voir ses JPO.</p>
                            )}
                        </div>
                    </section>
                    <section id="newsletterSection" className="h-[200px] flex items-center justify-center gap-8 bg-white">
                        <p className="w-[256px] text-black">Inscrivez vous à la newsletter pour recevoir un mail dès l'annonce d'une nouvelle JPO !</p>
                        <RightArrow className="right-arrow h-[48px] w-[48px] text-black"/>
                        <form method="post" className='flex flex-col justify-center gap-4' action="https://formspree.io/f/xjvowzqk">
                            <div className="dropdown">
                                <select name="city" id="cars" className="bg-gray-200 text-black rounded cursor-pointer">
                                    <option value="" disabled selected>Ville</option>
                                    {cities.map(city => (
                                        <option value={city}>
                                            {city}
                                        </option>
                                ))}
                                </select>
                            </div>
                            <input
                                type="email"
                                name="email"
                                placeholder="Votre email"
                                className="goated-input-settings text-black bg-gray-200 rounded"
                                required
                            />
                            <button name='btn' type="submit" className="bg-(--red-color) w-fit cursor-pointer text-white rounded">
                                S'inscrire
                            </button>
                        </form>
                    </section>
                </main>
            <Footer />
        </div>
    );
};

export default Home;