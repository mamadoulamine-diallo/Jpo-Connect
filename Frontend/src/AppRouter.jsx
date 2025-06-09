import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import Home from "./pages/Home";
import App from "./App";
// import JpoDetails from './pages/JpoDetails';

function AppRouter() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/app" element={<App />} />
        {/* <Route path="/jpo/:id" element={<JpoDetails />} /> */}
      </Routes>
    </Router>
  );
}

export default AppRouter;