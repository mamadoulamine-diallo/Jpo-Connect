import { Link } from 'react-router-dom';
import { useContext } from 'react';
import { AuthContext } from '../contexts/AuthContext.jsx';
import './NavBar.css';

function NavBar() {
  const { user, logout } = useContext(AuthContext);

  return (
    <nav className="navbar">
      <div className="navbar-container">
        <Link to="/" className="navbar-logo">JPO La Plateforme</Link>
        <div className="navbar-links">
          <Link to="/">Accueil</Link>
          {user ? (
            <>
              <Link to="/dashboard">Tableau de bord</Link>
              <button onClick={logout} className="navbar-logout">Déconnexion</button>
            </>
          ) : (
            <Link to="/login">Connexion</Link>
          )}
        </div>
      </div>
    </nav>
  );
}

export default NavBar;