import { useState } from 'react';
import { registerJPO, unregisterJPO } from '../api/api.js';
import './RegisterForm.css';

function RegisterForm({ jpoId }) {
  const [formData, setFormData] = useState({ user_name: '', user_email: '' });
  const [message, setMessage] = useState('');

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  const handleRegister = async () => {
    try {
      const response = await registerJPO(jpoId, formData.user_name, formData.user_email);
      setMessage(response.success || response.error);
    } catch (error) {
      setMessage(error.message);
    }
  };

  const handleUnregister = async () => {
    try {
      const response = await unregisterJPO(jpoId, formData.user_email);
      setMessage(response.success || response.error);
    } catch (error) {
      setMessage(error.message);
    }
  };

  return (
    <div className="register-form">
      <h3>Inscription</h3>
      <input
        type="text"
        name="user_name"
        placeholder="Votre nom"
        value={formData.user_name}
        onChange={handleInputChange}
      />
      <input
        type="email"
        name="user_email"
        placeholder="Votre email"
        value={formData.user_email}
        onChange={handleInputChange}
      />
      <div className="button-group">
        <button onClick={handleRegister}>S'inscrire</button>
        <button className="unregister" onClick={handleUnregister}>Se désinscrire</button>
      </div>
      {message && <p className={message.includes('Erreur') ? 'error' : 'success'}>{message}</p>}
    </div>
  );
}

export default RegisterForm;