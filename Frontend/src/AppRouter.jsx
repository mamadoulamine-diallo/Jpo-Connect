import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import Home from "./pages/Home";
import JPOPage from "./pages/JPOPage"; 

function AppRouter() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/jpo" element={<JPOPage />} />
      </Routes>
    </Router>
  );
}

export default AppRouter;
