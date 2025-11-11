import { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { productAPI } from '../services/api';
import { useCart } from '../context/CartContext';
import toast from 'react-hot-toast';

const ProductDetail = () => {
  const { id } = useParams();
  const { addToCart } = useCart();
  const [product, setProduct] = useState(null);
  const [quantity, setQuantity] = useState(1);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchProduct();
  }, [id]);

  const fetchProduct = async () => {
    try {
      const response = await productAPI.getById(id);
      setProduct(response.data.data);
    } catch (error) {
      toast.error('Failed to load product');
    } finally {
      setLoading(false);
    }
  };

  const handleAddToCart = () => {
    addToCart(product, quantity);
    toast.success(`${quantity} x ${product.name} added to cart!`);
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center h-screen">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary-600"></div>
      </div>
    );
  }

  if (!product) {
    return (
      <div className="container-custom py-16 text-center">
        <h2 className="text-3xl font-bold mb-4">Product Not Found</h2>
        <Link to="/products" className="btn btn-primary">
          Back to Products
        </Link>
      </div>
    );
  }

  return (
    <div className="container-custom py-8">
      <div className="text-gray-400 mb-6">
        <Link to="/">Home</Link> / <Link to="/products">Products</Link> / {product.name}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <div className="card bg-dark-800 overflow-hidden">
          <img 
          src={product.imageUrl}
          alt={product.name}
          className="w-full h-full object-cover"
        />
        </div>

        <div>
          <h1 className="text-4xl font-bold mb-4">{product.name}</h1>
          
          <p className="text-primary-500 text-xl mb-4">{product.category}</p>

          <div className="flex items-center gap-4 mb-6">
            <span className="text-3xl font-bold text-primary-500">
              ${product.price.toFixed(2)}
            </span>
            {product.stockQuantity > 0 ? (
              <span className="text-green-400 font-semibold">
                ✓ In Stock ({product.stockQuantity} available)
              </span>
            ) : (
              <span className="text-red-400 font-semibold">✗ Out of Stock</span>
            )}
          </div>

          <div className="mb-6">
            <h3 className="text-xl font-bold mb-2">Description</h3>
            <p className="text-gray-400 leading-relaxed">{product.description}</p>
          </div>

          {product.specifications && Object.keys(product.specifications).length > 0 && (
            <div className="mb-6">
              <h3 className="text-xl font-bold mb-2">Specifications</h3>
              <div className="card p-4">
                {Object.entries(product.specifications).map(([key, value]) => (
                  <div
                    key={key}
                    className="flex justify-between py-2 border-b border-dark-600 last:border-0"
                  >
                    <span className="text-gray-400">{key}</span>
                    <span className="font-semibold">{value}</span>
                  </div>
                ))}
              </div>
            </div>
          )}

          {product.stockQuantity > 0 ? (
            <div className="card p-6">
              <label className="block text-sm text-gray-400 mb-2">Quantity</label>
              <select
                value={quantity}
                onChange={(e) => setQuantity(Number(e.target.value))}
                className="input mb-4"
              >
                {[...Array(Math.min(10, product.stockQuantity))].map((_, i) => (
                  <option key={i + 1} value={i + 1}>
                    {i + 1}
                  </option>
                ))}
              </select>
              <button onClick={handleAddToCart} className="btn btn-primary w-full">
                Add to Cart
              </button>
            </div>
          ) : (
            <div className="card p-6 text-center">
              <p className="text-red-400 mb-4">This product is currently out of stock</p>
              <button className="btn btn-secondary" disabled>
                Notify When Available
              </button>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default ProductDetail;