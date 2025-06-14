import RegisterForm from './RegisterForm.jsx';
import CommentForm from './CommentForm.jsx';
import './JPOCard.css';

function JPOCard({ jpo }) {
  return (
    <div className="jpo-card">
      <h2>{jpo.title}</h2>
      <p>Date: {new Date(jpo.date).toLocaleDateString()}</p>
      <p>Lieu: {jpo.location}</p>
      <p>Capacité: {jpo.capacity}</p>
      <p>{jpo.description}</p>
      <RegisterForm jpoId={jpo.id} />
      <CommentForm jpoId={jpo.id} />
    </div>
  );
}

export default JPOCard;