import { Link } from 'react-router-dom';
import { useCart } from '../context/CartContext';
import toast from 'react-hot-toast';

const ProductCard = ({ product }) => {
  const { addToCart } = useCart();

  const handleAddToCart = (e) => {
    e.preventDefault();
    addToCart(product);
    toast.success(`${product.name} added to cart!`);
  };

  return (
    <Link
      to={`/products/${product._id}`}
      className="card card-hover group"
    >
      <div className="bg-dark-800 h-56 flex items-center justify-center text-6xl">
        🎹
      </div>

      <div className="p-6">
        <h3 className="text-lg font-semibold mb-2 group-hover:text-primary-500 transition-colors">
          {product.name}
        </h3>
        
        <p className="text-gray-400 text-sm mb-4 line-clamp-2">
          {product.description}
        </p>

        <div className="flex items-center justify-between mb-4">
          <span className="text-2xl font-bold text-primary-500">
            ${product.price.toFixed(2)}
          </span>
          <span className="text-sm text-gray-400">
            Stock: {product.stockQuantity}
          </span>
        </div>

        <button
          onClick={handleAddToCart}
          className="btn btn-primary w-full"
          disabled={product.stockQuantity === 0}
        >
          {product.stockQuantity === 0 ? 'Out of Stock' : 'Add to Cart'}
        </button>
      </div>
    </Link>
  );
};

export default ProductCard;