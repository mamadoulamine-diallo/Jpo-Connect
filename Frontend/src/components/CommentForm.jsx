import { useState, useEffect } from 'react';
import { addComment, getComments } from '../api/api.js';
import './CommentForm.css';

function CommentForm({ jpoId }) {
  const [formData, setFormData] = useState({ user_name: '', comment: '' });
  const [comments, setComments] = useState([]);
  const [message, setMessage] = useState('');

  useEffect(() => {
    getComments(jpoId)
      .then(data => {
        if (data.success && Array.isArray(data.data)) {
          setComments(data.data);
        } else {
          setComments([]);
          console.error('Réponse inattendue:', data);
        }
      })
      .catch(error => console.error('Erreur:', error));
  }, [jpoId]);

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  const handleSubmit = async () => {
    try {
      const response = await addComment(jpoId, formData.user_name, formData.comment);
      if (response && typeof response === 'object') {
        if (response.success) {
          setMessage(response.success); // Affiche le message de succès
          setFormData({ user_name: '', comment: '' });
          // Rafraîchir les commentaires
          getComments(jpoId).then(data => {
            if (data.success && Array.isArray(data.data)) setComments(data.data);
          }).catch(err => console.error('Erreur refresh:', err));
        } else if (response.error) {
          setMessage(response.error); // Affiche l'erreur
        } else {
          setMessage('Réponse inattendue du serveur');
        }
      } else {
        setMessage('Erreur de communication avec le serveur');
      }
    } catch (error) {
      setMessage('Erreur: ' + error.message);
    }
  };

  return (
    <div className="comment-form">
      <h3>Commentaires</h3>
      <input
        type="text"
        name="user_name"
        placeholder="Votre nom"
        value={formData.user_name}
        onChange={handleInputChange}
      />
      <textarea
        name="comment"
        placeholder="Votre commentaire"
        value={formData.comment}
        onChange={handleInputChange}
      />
      <button onClick={handleSubmit}>Envoyer</button>
      {message && <p className={message.includes('Erreur') || message.includes('introuvable') ? 'error' : 'success'}>{message}</p>}
      <div className="comment-list">
        {comments.length > 0 ? (
          comments.map(comment => (
            <div key={comment.id} className="comment-item">
              <p className="comment-author">{comment.user_name}</p>
              <p>{comment.comment}</p>
              <p className="comment-date">
                {new Date(comment.created_at).toLocaleDateString()}
              </p>
            </div>
          ))
        ) : (
          <p>Aucun commentaire pour le moment.</p>
        )}
      </div>
    </div>
  );
}

export default CommentForm;