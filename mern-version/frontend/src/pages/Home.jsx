import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { productAPI } from '../services/api';
import ProductCard from '../components/ProductCard';
import toast from 'react-hot-toast';

const Home = () => {
  const [featuredProducts, setFeaturedProducts] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchFeaturedProducts();
  }, []);

  const fetchFeaturedProducts = async () => {
    try {
      const response = await productAPI.getFeatured();
      setFeaturedProducts(response.data.data);
    } catch (error) {
      toast.error('Failed to load featured products');
    } finally {
      setLoading(false);
    }
  };

  const categories = [
    { name: 'Keyboards', icon: '⌨️', link: '/products?category=Keyboards' },
    { name: 'Switches', icon: '🔘', link: '/products?category=Switches' },
    { name: 'Keycaps', icon: '🎨', link: '/products?category=Keycaps' }
  ];

  return (
    <div className="container-custom py-8">
      <section className="bg-gradient-to-r from-primary-600 to-blue-500 rounded-2xl p-16 text-center mb-12">
        <h1 className="text-5xl md:text-6xl font-bold mb-4">
          Build Your Dream Keyboard
        </h1>
        <p className="text-xl mb-8 opacity-90">
          Premium mechanical keyboards, switches, and keycaps for enthusiasts
        </p>
        <Link to="/products" className="btn btn-secondary inline-block">
          Shop Now
        </Link>
      </section>

      <section className="mb-12">
        <h2 className="text-3xl font-bold mb-6">Shop by Category</h2>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {categories.map((category) => (
            <Link
              key={category.name}
              to={category.link}
              className="card card-hover p-8 text-center group"
            >
              <div className="text-6xl mb-4">{category.icon}</div>
              <h3 className="text-2xl font-bold text-primary-500 group-hover:text-primary-400 transition-colors">
                {category.name}
              </h3>
            </Link>
          ))}
        </div>
      </section>

      <section className="mb-12">
        <h2 className="text-3xl font-bold mb-6">Featured Products</h2>
        {loading ? (
          <div className="flex justify-center items-center h-64">
            <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary-600"></div>
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {featuredProducts.map((product) => (
              <ProductCard key={product._id} product={product} />
            ))}
          </div>
        )}
      </section>

      <section className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div className="card p-8 text-center">
          <div className="text-4xl mb-4">🚚</div>
          <h3 className="text-xl font-bold text-primary-500 mb-2">
            Free Shipping
          </h3>
          <p className="text-gray-400">Free shipping on orders over $100</p>
        </div>
        <div className="card p-8 text-center">
          <div className="text-4xl mb-4">✓</div>
          <h3 className="text-xl font-bold text-primary-500 mb-2">
            Quality Guaranteed
          </h3>
          <p className="text-gray-400">All products tested and verified</p>
        </div>
        <div className="card p-8 text-center">
          <div className="text-4xl mb-4">💬</div>
          <h3 className="text-xl font-bold text-primary-500 mb-2">
            Expert Support
          </h3>
          <p className="text-gray-400">Our team is here to help you</p>
        </div>
      </section>
    </div>
  );
};

export default Home;