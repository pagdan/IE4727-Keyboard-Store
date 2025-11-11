import { Link, useLocation } from 'react-router-dom';
import { ShoppingCartIcon, UserIcon } from '@heroicons/react/24/outline';
import { useCart } from '../context/CartContext';
import { useAuth } from '../context/AuthContext';

const Header = () => {
  const location = useLocation();
  const { getCartCount } = useCart();
  const { isAuthenticated } = useAuth();
  const cartCount = getCartCount();

  const isActive = (path) => location.pathname === path;
  const isAdmin = location.pathname.startsWith('/admin');

  return (
    <header className="bg-dark-900 border-b-2 border-primary-600 sticky top-0 z-50">
      <nav className="container-custom py-4">
        <div className="flex items-center justify-between">
          <Link to="/" className="flex items-center">
            <img 
            src="/RBKLogo.png" 
            alt="RobbingKeebs" 
            className="h-12 w-auto transition-transform hover:scale-110"
          />
          </Link>

          {!isAdmin && (
            <ul className="hidden md:flex items-center space-x-8">
              <li>
                <Link
                  to="/"
                  className={`font-medium transition-colors ${
                    isActive('/')
                      ? 'text-white'
                      : 'text-gray-400 hover:text-white'
                  }`}
                >
                  Home
                </Link>
              </li>
              <li>
                <Link
                  to="/products"
                  className={`font-medium transition-colors ${
                    isActive('/products')
                      ? 'text-white'
                      : 'text-gray-400 hover:text-white'
                  }`}
                >
                  Products
                </Link>
              </li>
            </ul>
          )}

          <div className="flex items-center space-x-6">
            {!isAdmin && (
              <Link
                to="/cart"
                className="relative text-gray-400 hover:text-white transition-colors"
              >
                <ShoppingCartIcon className="h-6 w-6" />
                {cartCount > 0 && (
                  <span className="absolute -top-2 -right-2 bg-primary-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                    {cartCount}
                  </span>
                )}
              </Link>
            )}
            
            <Link
              to={isAuthenticated ? '/admin/dashboard' : '/admin/login'}
              className="text-gray-400 hover:text-white transition-colors"
            >
              <UserIcon className="h-6 w-6" />
            </Link>
          </div>
        </div>
      </nav>
    </header>
  );
};

export default Header;