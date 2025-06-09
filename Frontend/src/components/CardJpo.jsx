import { Link } from 'react-router-dom';

function formatDate(mysqlDate) {
    if (!mysqlDate) return '';
    const [year, month, day] = mysqlDate.split('-');
    return `${day}/${month}/${year}`;
}

function CardJpo({ jpo }) {
    return (
        <div key={jpo.id} id="cardJpo" className="bg-white text-black rounded-md">
            <p id="pCity">{jpo.city}</p>
            <p>{formatDate(jpo.date_jpo)}</p>
            <Link to={`/jpo/${jpo.id}`}>
                <button className="text-white rounded-md cursor-pointer transition-colors duration-300">
                    Voir les détails
                </button>
            </Link>
        </div>
    );
}

export default CardJpo;