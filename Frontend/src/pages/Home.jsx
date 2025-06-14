import { useState, useEffect } from 'react';
import JPOCard from '../components/JPOCard.jsx';
import { getJPOs, getContent } from '../api/api.js';
import './Home.css';

function Home() {
  const [jpos, setJpos] = useState([]);
  const [searchTerm, setSearchTerm] = useState('');
  const [content, setContent] = useState({ upcoming_sessions: '', practical_info: '' });

  useEffect(() => {
    getJPOs()
      .then(data => {
        if (data.success && Array.isArray(data.data)) {
          setJpos(data.data); // Extrait le tableau data
        } else {
          setJpos([]); // Défaut si la structure est inattendue
          console.error('Réponse inattendue de getJPOs:', data);
        }
      })
      .catch(error => console.error('Erreur getJPOs:', error));

    Promise.all([
      getContent('upcoming_sessions'),
      getContent('practical_info')
    ])
      .then(([upcoming, practical]) => setContent({
        upcoming_sessions: upcoming.value || '', // Extrait la valeur
        practical_info: practical.value || ''
      }))
      .catch(error => console.error('Erreur getContent:', error));
  }, []);

  const filteredJpos = jpos.filter(jpo =>
    jpo.location.toLowerCase().includes(searchTerm.toLowerCase()) ||
    new Date(jpo.date).toLocaleDateString().includes(searchTerm)
  );

  return (
    <div className="home">
      <h1>Journées Portes Ouvertes</h1>
      <div className="content-section">
        <h2>Sessions à venir</h2>
        <p>{content.upcoming_sessions || 'Aucune session pour le moment.'}</p>
      </div>
      <div className="content-section">
        <h2>Infos pratiques</h2>
        <p>{content.practical_info || 'Aucune info disponible.'}</p>
      </div>
      <input
        type="text"
        placeholder="Rechercher par lieu ou date..."
        className="search-input"
        value={searchTerm}
        onChange={e => setSearchTerm(e.target.value)}
      />
      <div className="jpo-grid">
        {filteredJpos.map(jpo => (
          <JPOCard key={jpo.id} jpo={jpo} />
        ))}
      </div>
    </div>
  );
}

export default Home;