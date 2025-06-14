import { createContext } from 'react';

export const AuthContext = createContext(null);

export function AuthProvider({ children, value }) {
  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
}