import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import Home from "./pages/Home";
import JPODetails from "./pages/JPODetails"; 

function AppRouter() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/jpo/:id" element={<JPODetails />} />
        
      </Routes>
    </Router>
  );
}

export default AppRouter;
