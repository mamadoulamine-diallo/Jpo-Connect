import LogoLP from '../assets/img/La_Plateforme-logo.png';
import LogoJPO from '../assets/img/JPOaccess.png';
import UserIcon from '../assets/img/UserIcon';

function Header() {
    return (
        <header className="flex h-[88px] items-center justify-between px-4 shadow-sm">
            <nav className='flex h-full items-center gap-4'>
                <a href="https://laplateforme.io"><img className='h-8/10²' src={LogoLP} alt="La Plateforme Logo" /></a>
                <a href="#"><img className='h-10' src={LogoJPO} alt="Logo JPOaccess" /></a>
            </nav>
            <UserIcon id="btn-user" className="h-5/10 text-(--blue-color) cursor-pointer" />
        </header>
    );
}

export default Header;