import { useState, useContext } from 'react';
import { useNavigate } from 'react-router-dom';
import { AuthContext } from '../contexts/AuthContext.jsx';
import { login } from '../api/api.js';
import './Login.css';

function Login() {
  const { setUser } = useContext(AuthContext);
  const navigate = useNavigate();
  const [formData, setFormData] = useState({ username: '', password: '' });
  const [message, setMessage] = useState('');

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  const handleSubmit = async () => {
    try {
      const response = await login(formData.username, formData.password);
      if (response.success) {
        const user = { token: response.token, role: response.role };
        setUser(user);
        localStorage.setItem('user', JSON.stringify(user));
        navigate('/dashboard');
      } else {
        setMessage(response.error);
      }
    } catch (error) {
      setMessage(error.message);
    }
  };

  return (
    <div className="login">
      <h1>Connexion</h1>
      <input
        type="text"
        name="username"
        placeholder="Nom d'utilisateur"
        value={formData.username}
        onChange={handleInputChange}
      />
      <input
        type="password"
        name="password"
        placeholder="Mot de passe"
        value={formData.password}
        onChange={handleInputChange}
      />
      <button onClick={handleSubmit}>Se connecter</button>
      {message && <p className="error">{message}</p>}
    </div>
  );
}

export default Login;