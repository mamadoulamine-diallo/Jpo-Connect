import LogoFacebook from "../assets/img/LogoFacebook";
import LogoInsta from "../assets/img/LogoInsta";
import LogoLinkedin from "../assets/img/LogoLinkedin";
import LogoTwitter from "../assets/img/LogoTwitter";
import LogoYoutube from "../assets/img/LogoToutube";

function Footer() {
    return (
        <footer className="bg-(--bg-footer)">
			<div className="">
                <div id="new-footer" className="">
                    <div id="outer-left-section" className="flex">
                        <div id="left-section" className="" >
                            <div id="left-section-logo" className="">
                                <span className=""><img decoding="async" src="https://laplateforme.io/wp-content/uploads/2024/02/Fichier-1.svg" alt="" title="Fichier 1" height="auto" width="auto"/></span>
                            </div>
                            <ul className="flex">
                                <li id="li-left"><a href="https://www.facebook.com/LaPlateformeIO" className="h-[32px] w-[32px] bg-white flex items-center justify-center" title="Suivez sur Facebook" target="_blank"><LogoFacebook className="h-[18px] w-[18px] text-(--bg-footer)"/></a></li>
                                <li id="li-left"><a href="https://www.instagram.com/LaPlateformeIO/" className="h-[32px] w-[32px] bg-white flex items-center justify-center" title="Suivez sur Instagram" target="_blank"><LogoInsta className="h-[18px] w-[18px] text-(--bg-footer)"/></a></li>
                                <li id="li-left"><a href="https://www.linkedin.com/school/laplateformeio/" className="h-[32px] w-[32px] bg-white flex items-center justify-center" title="Suivez sur LinkedIn" target="_blank"><LogoLinkedin className="h-[18px] w-[18px] text-(--bg-footer)"/></a></li>
                                <li id="li-left"><a href="https://twitter.com/LaPlateformeIO" className="h-[32px] w-[32px] bg-white flex items-center justify-center" title="Suivez sur Twitter" target="_blank"><LogoTwitter className="h-[18px] w-[18px] text-(--bg-footer)"/></a></li>
                                <li><a href="https://www.youtube.com/c/LaPlateformeIO" className="h-[32px] w-[32px] bg-white flex items-center justify-center" title="Suivez sur Youtube" target="_blank"><LogoYoutube className="h-[18px] w-[18px] text-(--bg-footer)"/></a></li>
                            </ul>
                            <div className="">
                                <a id="a-bot" className="a-top bg-white text-(--bg-footer) inline-block" href="https://recrutement.laplateforme.io/" target="_blank">Rejoignez nos équipes</a>
                            </div>
                            <div className="">
                                <a id="a-bot" className="bg-(--red-color) inline-block" href="/telechargement-brochure/">Télécharger la brochure</a>
                            </div>
                        </div>
                        <div id="right-section" className="">
                            <div id="right-section-inner-div" className="flex">
                                <div id="inner-div-tier" className="">
                                    <div id="tier-inner" className="">
                                        <div className="">
                                            <p className="font-lg"><b>La Plateforme</b></p>
                                            <p><a href="https://laplateforme.io/">Accueil</a></p>
                                            <p><a href="https://laplateforme.io/qui-sommes-nous/">Qui sommes-nous ?</a></p>
                                            <p><a href="https://laplateforme.io/telechargement-brochure/">Notre brochure</a></p>
                                            <p><a href="https://laplateforme.io/lls-parlent-de-nous/">Ils parlent de nous</a></p>
                                            <p><a href="https://laplateforme.io/news/">News</a></p>
                                            <p id="p-last"><a href="https://laplateforme.io/entreprise/taxe-apprentissage/ ">Taxe d'apprentissage</a></p>
                                        </div>
                                    </div>
                                </div>
                                <div id="inner-div-tier" className="">
                                    <div id="tier-inner" className="">
                                        <div className="">
                                            <p className="font-lg"><b>Les Formations</b></p>
                                            <p><a href="https://laplateforme.io/bachelor-it//">Bachelor IT</a></p>
                                            <p><a href="https://laplateforme.io/master-of-science/">Master of Science</a></p>
                                            <p><a href="https://laplateforme.io/innovation-lab/">Innovation Lab</a></p>
                                            <p><a href="https://laplateforme.io/coding-school/">Développement</a></p>
                                            <p><a href="https://laplateforme.io/ai-school/">Intelligence Artificielle</a></p>
                                            <p id="p-last"><a href="https://laplateforme.io/cyber-security-school/">Cybersécurité</a></p>
                                        </div>
                                    </div>
                                </div>
                                <div id="inner-div-tier" className="">
                                    <div id="tier-inner" className="">
                                        <div className="">
                                            <p className="font-lg"><b>Informations</b></p>
                                            <p>8 rue d'hozier, 13002 Marseille</p>
                                            <p id="p-last">Tel : <a href="tel:04.84.89.43.69" target="_blank" rel="noopener">04.84.89.43.69</a></p>
                                        </div>
                                    </div>
                                    <div id="tier-inner-a" className="">
                                        <a className="" href="https://laplateforme.io/informations/">Contact</a>
                                    </div>
                                    <div id="tier-inner-img" className="">
                                        <span className=""><img decoding="async" src="https://laplateforme.io/wp-content/uploads/2022/05/Logo_April_footer2.svg" alt="" title="Logo_April_footer2"/></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="subfooter" className="bg-black">
                    <div id="subfooter-inner" className="">
                        <div className="">
                            <div className="">
                                <div className="">
                                    <p className="p1">© La Plateforme -&nbsp; <a href="https://laplateforme.io/qui-sommes-nous/">Établissement d'enseignement supérieur technique privé</a>&nbsp; - Tous droits réservés. | <a href="/mentions-legales/">Mentions légales</a> | <a href="/cookies/">Cookies</a> |&nbsp;<a href="/cgv/">Conditions générales de vente</a>&nbsp;| <a href="mailto:referent.handicap@laplateforme.io">Référent handicap : Christine Yorillo</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="subfooter-inner-2" className="">
                        <div className="">
                            <div className="">
                                <div className="">
                                    <p className="p1">JPO access réalisé par <a href="https://github.com/mamadoulamine-diallo">Lamine</a>, <a href="https://github.com/sebastien-liveyupeng">Sébastien</a> & <a href="https://github.com/antoine-leca">Antoine</a>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>	
            </div>
        </footer>
    );
}

export default Footer;