import React from 'react';
import Header from '/src/components/Header';
import Footer from '/src/components/Footer';

const Home = () => {
    return (
        <div>
            <Header />
            <body>
                <main className="flex flex-col items-center justify-center min-h-screen bg-gray-100">
                    <h1 className="text-4xl font-bold mb-4">Welcome to La Plateforme</h1>
                    <p className="text-lg">Your journey to mastering technology starts here.</p>
                </main>
            </body>
            <Footer />
        </div>
    );
};

export default Home;