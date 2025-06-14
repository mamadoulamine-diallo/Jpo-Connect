import { Outlet, useNavigate } from 'react-router-dom';
import { useState } from 'react';
import NavBar from './components/NavBar.jsx';
import { AuthProvider } from './contexts/AuthContext.jsx';

function App() {
  const [user, setUser] = useState(null); // { token, role } ou null
  const navigate = useNavigate();

  const logout = () => {
    setUser(null);
    navigate('/');
  };

  return (
    <AuthProvider value={{ user, setUser, logout }}>
      <div>
        <NavBar />
        <div className="container">
          <Outlet />
        </div>
      </div>
    </AuthProvider>
  );
}

export default App;