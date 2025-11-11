import { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { orderAPI } from '../services/api';
import toast from 'react-hot-toast';

const OrderConfirmation = () => {
  const { orderNumber } = useParams();
  const [order, setOrder] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchOrder();
  }, [orderNumber]);

  const fetchOrder = async () => {
    try {
      const response = await orderAPI.getByOrderNumber(orderNumber);
      setOrder(response.data.data);
    } catch (error) {
      toast.error('Failed to load order details');
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center h-screen">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary-600"></div>
      </div>
    );
  }

  if (!order) {
    return (
      <div className="container-custom py-16 text-center">
        <h2 className="text-3xl font-bold mb-4">Order Not Found</h2>
        <Link to="/products" className="btn btn-primary">
          Continue Shopping
        </Link>
      </div>
    );
  }

  return (
    <div className="container-custom py-8">
      <div className="text-center mb-12 bg-gradient-to-r from-green-600 to-green-500 rounded-2xl p-12">
        <div className="text-6xl mb-4">✓</div>
        <h1 className="text-4xl font-bold mb-4">Order Confirmed!</h1>
        <p className="text-xl opacity-95">
          Thank you for your order. We've sent a confirmation email to{' '}
          <strong>{order.customer.email}</strong>
        </p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div className="card p-8">
          <h2 className="text-2xl font-bold mb-6">Order Details</h2>
          
          <div className="space-y-4 mb-6">
            <div className="flex justify-between">
              <span className="text-gray-400">Order Number:</span>
              <span className="font-bold text-primary-500">{order.orderNumber}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-gray-400">Order Date:</span>
              <span className="font-semibold">
                {new Date(order.orderDate).toLocaleDateString()}
              </span>
            </div>
            <div className="flex justify-between">
              <span className="text-gray-400">Status:</span>
              <span className="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-sm font-semibold">
                {order.status}
              </span>
            </div>
            <div className="flex justify-between">
              <span className="text-gray-400">Total Amount:</span>
              <span className="text-2xl font-bold text-primary-500">
                ${order.totalAmount.toFixed(2)}
              </span>
            </div>
          </div>

          <h3 className="text-xl font-bold mb-4">Customer Information</h3>
          <div className="card bg-dark-800 p-4 space-y-2">
            <p><strong>Name:</strong> {order.customer.name}</p>
            <p><strong>Email:</strong> {order.customer.email}</p>
            <p><strong>Phone:</strong> {order.customer.phone}</p>
            <p><strong>Shipping Address:</strong><br/>
              {order.shippingAddress.street}<br/>
              {order.shippingAddress.city}, {order.shippingAddress.postalCode}
            </p>
          </div>
        </div>

        <div className="card p-8">
          <h2 className="text-2xl font-bold mb-6">Order Items</h2>
          
          <div className="space-y-4">
            {order.items.map((item) => (
              <div key={item._id} className="flex justify-between py-4 border-b border-dark-600">
                <div className="flex gap-4">
                  <div className="text-4xl">🎹</div>
                  <div>
                    <p className="font-semibold">{item.productName}</p>
                    <p className="text-sm text-gray-400">
                      Qty: {item.quantity} × ${item.priceAtPurchase.toFixed(2)}
                    </p>
                  </div>
                </div>
                <div className="text-primary-500 font-bold">
                  ${(item.quantity * item.priceAtPurchase).toFixed(2)}
                </div>
              </div>
            ))}
          </div>

          <div className="mt-6 pt-6 border-t-2 border-dark-600">
            <div className="flex justify-between text-2xl font-bold">
              <span>Order Total:</span>
              <span className="text-primary-500">
                ${order.totalAmount.toFixed(2)}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div className="card p-8 mt-8 text-center">
        <h2 className="text-2xl font-bold mb-6">What Happens Next?</h2>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <div className="text-4xl mb-2">📧</div>
            <h3 className="text-xl font-bold text-primary-500 mb-2">1. Email Confirmation</h3>
            <p className="text-gray-400">We've sent order details to your email</p>
          </div>
          <div>
            <div className="text-4xl mb-2">📦</div>
            <h3 className="text-xl font-bold text-primary-500 mb-2">2. Processing</h3>
            <p className="text-gray-400">We'll process and pack your order</p>
          </div>
          <div>
            <div className="text-4xl mb-2">🚚</div>
            <h3 className="text-xl font-bold text-primary-500 mb-2">3. Shipping</h3>
            <p className="text-gray-400">You'll receive tracking information</p>
          </div>
        </div>
      </div>

      <div className="text-center mt-8 space-x-4">
        <Link to="/products" className="btn btn-primary">
          Continue Shopping
        </Link>
        <Link to="/" className="btn btn-secondary">
          Back to Home
        </Link>
      </div>
    </div>
  );
};

export default OrderConfirmation;