import { useState, useEffect, useContext } from 'react';
import { AuthContext } from '../contexts/AuthContext.jsx';
import {
  getJPOs, addJPO, updateJPO, deleteJPO,
  getInscriptions, getAllComments, approveComment, deleteComment,
  getStats, getContent, updateContent, sendReminder
} from '../api/api.js';
import './Dashboard.css';

function Dashboard() {
  const { user } = useContext(AuthContext);
  const [tab, setTab] = useState('jpos');
  const [jpos, setJpos] = useState([]);
  const [inscriptions, setInscriptions] = useState([]);
  const [comments, setComments] = useState([]);
  const [stats, setStats] = useState(null);
  const [content, setContent] = useState({ upcoming_sessions: '', practical_info: '' });
  const [jpoForm, setJpoForm] = useState({ id: '', title: '', date: '', location: '', capacity: '', description: '' });
  const [contentForm, setContentForm] = useState({ upcoming_sessions: '', practical_info: '' });
  const [emailForm, setEmailForm] = useState({ user_email: '', jpo_id: '', jpo_title: '', jpo_date: '', jpo_location: '' });
  const [message, setMessage] = useState('');

  useEffect(() => {
    getJPOs()
      .then(data => {
        if (data.success) {
          setJpos(Array.isArray(data.data) ? data.data : []);
        } else {
          setJpos([]);
          console.error('Réponse inattendue de getJPOs:', data);
        }
      })
      .catch(error => console.error('Erreur lors du chargement des JPOs:', error));

    Promise.all([
      getContent('upcoming_sessions'),
      getContent('practical_info')
    ])
      .then(([upcoming, practical]) => {
        setContent({ upcoming_sessions: upcoming, practical_info: practical });
        setContentForm({ upcoming_sessions: upcoming, practical_info: practical });
      })
      .catch(error => console.error('Erreur lors du chargement du contenu:', error));
  }, []);

  const loadJPOData = async (jpoId) => {
    try {
      const [inscriptionsData, commentsData, statsData] = await Promise.all([
        getInscriptions(jpoId),
        getAllComments(),
        getStats(jpoId)
      ]);
      setInscriptions(inscriptionsData);
      setComments(commentsData);
      setStats(statsData);
    } catch (error) {
      console.error('Erreur:', error);
    }
  };

  const handleJpoInputChange = (e) => {
    const { name, value } = e.target;
    setJpoForm({ ...jpoForm, [name]: value });
  };

  const handleJpoSubmit = async () => {
    try {
      const response = jpoForm.id
        ? await updateJPO(jpoForm)
        : await addJPO(jpoForm);
      if (response && typeof response === 'object') {
        if (response.success) {
          setMessage(response.success); // Utilise la chaîne success comme message
        } else if (response.error) {
          setMessage(response.error);
        } else {
          setMessage('Réponse inattendue du serveur');
        }
      } else {
        setMessage('Réponse invalide du serveur');
      }
      setJpoForm({ id: '', title: '', date: '', location: '', capacity: '', description: '' });
      const data = await getJPOs();
      if (data.success) {
        setJpos(Array.isArray(data.data) ? data.data : []);
      } else {
        setJpos([]);
        console.error('Réponse inattendue après ajout/modif:', data);
      }
    } catch (error) {
      setMessage('Erreur: ' + error.message);
    }
  };

  const handleJpoDelete = async (id) => {
    try {
      const response = await deleteJPO(id);
      if (response && typeof response === 'object') {
        if (response.success) {
          setMessage(response.success); // Utilise la chaîne success comme message
        } else if (response.error) {
          setMessage(response.error);
        } else {
          setMessage('Réponse inattendue du serveur');
        }
      } else {
        setMessage('Réponse invalide du serveur');
      }
      const data = await getJPOs();
      if (data.success) {
        setJpos(Array.isArray(data.data) ? data.data : []);
      } else {
        setJpos([]);
        console.error('Réponse inattendue après suppression:', data);
      }
    } catch (error) {
      setMessage('Erreur: ' + error.message);
    }
  };

  const handleJpoEdit = (jpo) => {
    setJpoForm(jpo);
  };

  const handleContentInputChange = (e) => {
    const { name, value } = e.target;
    setContentForm({ ...contentForm, [name]: value });
  };

  const handleContentSubmit = async (key) => {
    try {
      const response = await updateContent(key, contentForm[key]);
      setMessage(response.success || response.error);
      setContent({ ...content, [key]: contentForm[key] });
    } catch (error) {
      setMessage(error.message);
    }
  };

  const handleCommentApprove = async (commentId) => {
    try {
      const response = await approveComment(commentId);
      setMessage(response.success || response.error);
      const data = await getAllComments();
      if (data.success && Array.isArray(data.data)) {
        setComments(data.data);
      } else {
        setComments([]);
        console.error('Réponse inattendue des commentaires:', data);
      }
    } catch (error) {
      setMessage(error.message);
    }
  };

  const handleCommentDelete = async (commentId) => {
    try {
      const response = await deleteComment(commentId);
      setMessage(response.success || response.error);
      const data = await getAllComments();
      if (data.success && Array.isArray(data.data)) {
        setComments(data.data);
      } else {
        setComments([]);
        console.error('Réponse inattendue des commentaires:', data);
      }
    } catch (error) {
      setMessage(error.message);
    }
  };

  const handleEmailInputChange = (e) => {
    const { name, value } = e.target;
    setEmailForm({ ...emailForm, [name]: value });
  };

  const handleEmailSubmit = async () => {
    try {
      const response = await sendReminder(emailForm);
      setMessage(response.success || response.error);
    } catch (error) {
      setMessage(error.message);
    }
  };

  if (!user) {
    return <p className="error">Veuillez vous connecter.</p>;
  }

  return (
    <div className="dashboard">
      <h1>Tableau de bord</h1>
      <div className="tabs">
        <button
          className={tab === 'jpos' ? 'active' : ''}
          onClick={() => setTab('jpos')}
        >
          JPO
        </button>
        <button
          className={tab === 'inscriptions' ? 'active' : ''}
          onClick={() => setTab('inscriptions')}
        >
          Inscriptions
        </button>
        <button
          className={tab === 'comments' ? 'active' : ''}
          onClick={() => setTab('comments')}
        >
          Commentaires
        </button>
        <button
          className={tab === 'content' ? 'active' : ''}
          onClick={() => setTab('content')}
        >
          Contenu
        </button>
        <button
          className={tab === 'emails' ? 'active' : ''}
          onClick={() => setTab('emails')}
        >
          Emails
        </button>
      </div>
      {message && <p className={message.includes('Erreur') ? 'error' : 'success'}>{message}</p>}

      {tab === 'jpos' && (
        <div className="tab-content">
          <h2>Gérer les JPO</h2>
          <input
            type="text"
            name="title"
            placeholder="Titre"
            value={jpoForm.title}
            onChange={handleJpoInputChange}
          />
          <input
            type="datetime-local"
            name="date"
            value={jpoForm.date}
            onChange={handleJpoInputChange}
          />
          <input
            type="text"
            name="location"
            placeholder="Lieu"
            value={jpoForm.location}
            onChange={handleJpoInputChange}
          />
          <input
            type="number"
            name="capacity"
            placeholder="Capacité"
            value={jpoForm.capacity}
            onChange={handleJpoInputChange}
          />
          <textarea
            name="description"
            placeholder="Description"
            value={jpoForm.description}
            onChange={handleJpoInputChange}
          />
          <button onClick={handleJpoSubmit}>{jpoForm.id ? 'Modifier' : 'Ajouter'}</button>
          <div className="jpo-list">
            {Array.isArray(jpos) ? (
              jpos.map(jpo => (
                <div key={jpo.id} className="jpo-item">
                  <span>{jpo.title} - {jpo.location}</span>
                  <div>
                    <button className="edit" onClick={() => handleJpoEdit(jpo)}>Modifier</button>
                    <button className="delete" onClick={() => handleJpoDelete(jpo.id)}>Supprimer</button>
                  </div>
                </div>
              ))
            ) : (
              <p>Erreur: Données des JPOs invalides</p>
            )}
          </div>
        </div>
      )}

      {tab === 'inscriptions' && (
        <div className="tab-content">
          <h2>Inscriptions</h2>
          <select onChange={e => loadJPOData(e.target.value)}>
            <option value="">Sélectionner une JPO</option>
            {Array.isArray(jpos) ? (
              jpos.map(jpo => (
                <option key={jpo.id} value={jpo.id}>{jpo.title}</option>
              ))
            ) : (
              <option disabled>Erreur: Données des JPOs invalides</option>
            )}
          </select>
          {inscriptions.length > 0 ? (
            inscriptions.map(inscription => (
              <div key={inscription.id} className="inscription-item">
                <p>{inscription.user_name} - {inscription.user_email}</p>
              </div>
            ))
          ) : (
            <p>Aucune inscription pour cette JPO.</p>
          )}
          {stats && (
            <div className="stats">
              <p>Inscriptions: {stats.inscriptions}</p>
              <p>Commentaires: {stats.comments}</p>
              <p>Capacité: {stats.capacity}</p>
            </div>
          )}
        </div>
      )}

      {tab === 'comments' && (
        <div className="tab-content">
          <h2>Commentaires</h2>
          {Array.isArray(comments) ? (
            comments.map(comment => (
              <div key={comment.id} className="comment-item">
                <div>
                  <p>{comment.user_name}: {comment.comment}</p>
                  <p className="comment-status">{comment.is_approved ? 'Approuvé' : 'En attente'}</p>
                </div>
                <div>
                  {!comment.is_approved && (
                    <button className="approve" onClick={() => handleCommentApprove(comment.id)}>
                      Approuver
                    </button>
                  )}
                  <button className="delete" onClick={() => handleCommentDelete(comment.id)}>
                    Supprimer
                  </button>
                </div>
              </div>
            ))
          ) : (
            <p>Erreur: Données des commentaires invalides</p>
          )}
        </div>
      )}

      {tab === 'content' && (
        <div className="tab-content">
          <h2>Contenu personnalisé</h2>
          <div className="content-section">
            <h3>Sessions à venir</h3>
            <textarea
              name="upcoming_sessions"
              value={contentForm.upcoming_sessions}
              onChange={handleContentInputChange}
            />
            <button onClick={() => handleContentSubmit('upcoming_sessions')}>
              Mettre à jour
            </button>
          </div>
          <div className="content-section">
            <h3>Infos pratiques</h3>
            <textarea
              name="practical_info"
              value={contentForm.practical_info}
              onChange={handleContentInputChange}
            />
            <button onClick={() => handleContentSubmit('practical_info')}>
              Mettre à jour
            </button>
          </div>
        </div>
      )}

      {tab === 'emails' && (
        <div className="tab-content">
          <h2>Envoyer un rappel</h2>
          <input
            type="email"
            name="user_email"
            placeholder="Email du destinataire"
            value={emailForm.user_email}
            onChange={handleEmailInputChange}
          />
          <input
            type="text"
            name="jpo_id"
            placeholder="ID de la JPO"
            value={emailForm.jpo_id}
            onChange={handleEmailInputChange}
          />
          <input
            type="text"
            name="jpo_title"
            placeholder="Titre de la JPO"
            value={emailForm.jpo_title}
            onChange={handleEmailInputChange}
          />
          <input
            type="text"
            name="jpo_date"
            placeholder="Date de la JPO"
            value={emailForm.jpo_date}
            onChange={handleEmailInputChange}
          />
          <input
            type="text"
            name="jpo_location"
            placeholder="Lieu de la JPO"
            value={emailForm.jpo_location}
            onChange={handleEmailInputChange}
          />
          <button onClick={handleEmailSubmit}>Envoyer</button>
        </div>
      )}
    </div>
  );
}

export default Dashboard;