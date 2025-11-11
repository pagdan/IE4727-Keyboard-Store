import { Link } from 'react-router-dom';
import { useCart } from '../context/CartContext';

const Cart = () => {
  const { cart, removeFromCart, updateQuantity, getCartTotal } = useCart();

  if (cart.length === 0) {
    return (
      <div className="container-custom py-16">
        <div className="card p-12 text-center">
          <div className="text-6xl mb-4">🛒</div>
          <h2 className="text-3xl font-bold mb-4">Your cart is empty</h2>
          <p className="text-gray-400 mb-6">Add some awesome keyboards and accessories!</p>
          <Link to="/products" className="btn btn-primary">
            Continue Shopping
          </Link>
        </div>
      </div>
    );
  }

  const subtotal = getCartTotal();
  const shipping = 10.00;
  const total = subtotal + shipping;

  return (
    <div className="container-custom py-8">
      <h1 className="text-4xl font-bold mb-8">Shopping Cart</h1>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div className="lg:col-span-2 space-y-4">
          {cart.map((item) => (
            <div key={item._id} className="card p-6 flex gap-6">
              <div className="text-6xl flex-shrink-0">🎹</div>
              
              <div className="flex-grow">
                <Link to={`/products/${item._id}`}>
                  <h3 className="text-xl font-semibold mb-2 hover:text-primary-500">
                    {item.name}
                  </h3>
                </Link>
                <p className="text-gray-400 text-sm mb-2 line-clamp-2">
                  {item.description}
                </p>
                <p className="text-primary-500 font-bold">
                  ${item.price.toFixed(2)} each
                </p>
              </div>

              <div className="text-center">
                <label className="block text-sm text-gray-400 mb-2">Quantity</label>
                <select
                  value={item.quantity}
                  onChange={(e) => updateQuantity(item._id, Number(e.target.value))}
                  className="input w-20"
                >
                  {[...Array(Math.min(10, item.stockQuantity))].map((_, i) => (
                    <option key={i + 1} value={i + 1}>
                      {i + 1}
                    </option>
                  ))}
                </select>
              </div>

              <div className="text-right min-w-[120px]">
                <p className="text-2xl font-bold text-primary-500 mb-4">
                  ${(item.price * item.quantity).toFixed(2)}
                </p>
                <button
                  onClick={() => removeFromCart(item._id)}
                  className="btn btn-secondary text-sm px-4 py-2"
                >
                  Remove
                </button>
              </div>
            </div>
          ))}

          <div className="flex gap-4">
            <Link to="/products" className="btn btn-secondary">
              Continue Shopping
            </Link>
          </div>
        </div>

        <div>
          <div className="card p-6 sticky top-24">
            <h2 className="text-2xl font-bold mb-6">Order Summary</h2>
            
            <div className="space-y-3 mb-6 pb-6 border-b border-dark-600">
              <div className="flex justify-between">
                <span className="text-gray-400">Subtotal:</span>
                <span>${subtotal.toFixed(2)}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-gray-400">Shipping:</span>
                <span>${shipping.toFixed(2)}</span>
              </div>
            </div>
            
            <div className="flex justify-between text-2xl font-bold mb-6">
              <span>Total:</span>
              <span className="text-primary-500">${total.toFixed(2)}</span>
            </div>
            
            <Link to="/checkout" className="btn btn-primary w-full block text-center">
              Proceed to Checkout
            </Link>

            <div className="mt-6 pt-6 border-t border-dark-600 text-center text-sm text-gray-400">
              <p>🔒 Secure Checkout</p>
              <p>💳 Multiple Payment Options</p>
              <p>📦 Fast Shipping</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Cart;