import { useState, useEffect } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { orderAPI } from '../../services/api';
import toast from 'react-hot-toast';

const AdminOrders = () => {
  const [searchParams, setSearchParams] = useSearchParams();
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  const [filters, setFilters] = useState({
    status: searchParams.get('status') || 'all',
    search: searchParams.get('search') || ''
  });
  const [selectedOrder, setSelectedOrder] = useState(null);

  useEffect(() => {
    fetchOrders();
  }, [filters]);

  const fetchOrders = async () => {
    setLoading(true);
    try {
      const response = await orderAPI.getAll(filters);
      setOrders(response.data.data);
    } catch (error) {
      toast.error('Failed to load orders');
    } finally {
      setLoading(false);
    }
  };

  const handleFilterChange = (key, value) => {
    const newFilters = { ...filters, [key]: value };
    setFilters(newFilters);
    
    const params = {};
    if (newFilters.status !== 'all') params.status = newFilters.status;
    if (newFilters.search) params.search = newFilters.search;
    setSearchParams(params);
  };

  const handleStatusUpdate = async (orderId, newStatus) => {
    try {
      await orderAPI.updateStatus(orderId, newStatus);
      toast.success('Order status updated successfully!');
      fetchOrders();
      setSelectedOrder(null);
    } catch (error) {
      toast.error('Failed to update order status');
    }
  };

  const viewOrderId = searchParams.get('view');
  const viewOrder = viewOrderId ? orders.find(o => o._id === viewOrderId) : null;

  return (
    <div className="container-custom py-8">
      <div className="flex justify-between items-center mb-8">
        <h1 className="text-4xl font-bold">Manage Orders</h1>
        <Link to="/admin/dashboard" className="btn btn-secondary">
          ← Back to Dashboard
        </Link>
      </div>

      {viewOrder ? (
        // Order Detail View
        <div>
          <button
            onClick={() => setSearchParams({})}
            className="btn btn-secondary mb-6"
          >
            ← Back to Orders
          </button>

          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div className="lg:col-span-2">
              <div className="card p-8 mb-6">
                <h2 className="text-2xl font-bold mb-6">
                  Order {viewOrder.orderNumber}
                </h2>
                
                <div className="grid grid-cols-2 gap-4 mb-6">
                  <div>
                    <p className="text-gray-400 text-sm">Order Date</p>
                    <p className="font-semibold">
                      {new Date(viewOrder.orderDate).toLocaleString()}
                    </p>
                  </div>
                  <div>
                    <p className="text-gray-400 text-sm">Last Updated</p>
                    <p className="font-semibold">
                      {new Date(viewOrder.updatedAt).toLocaleString()}
                    </p>
                  </div>
                </div>

                <h3 className="text-xl font-bold mb-4">Customer Information</h3>
                <div className="card bg-dark-800 p-4 mb-6">
                  <p className="mb-2"><strong>Name:</strong> {viewOrder.customer.name}</p>
                  <p className="mb-2"><strong>Email:</strong> {viewOrder.customer.email}</p>
                  <p className="mb-2"><strong>Phone:</strong> {viewOrder.customer.phone}</p>
                  <p><strong>Address:</strong><br/>
                    {viewOrder.shippingAddress.street}<br/>
                    {viewOrder.shippingAddress.city}, {viewOrder.shippingAddress.postalCode}
                  </p>
                </div>

                <h3 className="text-xl font-bold mb-4">Order Items</h3>
                {viewOrder.items.map((item) => (
                  <div key={item._id} className="flex justify-between p-4 bg-dark-800 rounded-lg mb-2">
                    <div>
                      <p className="font-semibold">{item.productName}</p>
                      <p className="text-sm text-gray-400">
                        Qty: {item.quantity} × ${item.priceAtPurchase.toFixed(2)}
                      </p>
                    </div>
                    <div className="text-primary-500 font-bold">
                      ${(item.quantity * item.priceAtPurchase).toFixed(2)}
                    </div>
                  </div>
                ))}

                <div className="text-right mt-6 pt-6 border-t-2 border-dark-600">
                  <p className="text-2xl font-bold">
                    Total: <span className="text-primary-500">${viewOrder.totalAmount.toFixed(2)}</span>
                  </p>
                </div>
              </div>
            </div>

            <div>
              <div className="card p-6">
                <h3 className="text-xl font-bold mb-4">Update Order Status</h3>
                <select
                  value={selectedOrder?.status || viewOrder.status}
                  onChange={(e) => setSelectedOrder({ ...viewOrder, status: e.target.value })}
                  className="input mb-4"
                >
                  <option value="Pending">Pending</option>
                  <option value="Processing">Processing</option>
                  <option value="Shipped">Shipped</option>
                  <option value="Delivered">Delivered</option>
                  <option value="Cancelled">Cancelled</option>
                </select>
                <button
                  onClick={() => handleStatusUpdate(viewOrder._id, selectedOrder?.status || viewOrder.status)}
                  className="btn btn-primary w-full"
                >
                  Update Status
                </button>
                <p className="text-sm text-gray-400 text-center mt-4">
                  Customer will be notified via email
                </p>
              </div>
            </div>
          </div>
        </div>
      ) : (
        // Orders List
        <>
          <div className="card p-6 mb-6">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label className="block text-sm text-gray-400 mb-2">Search</label>
                <input
                  type="text"
                  value={filters.search}
                  onChange={(e) => handleFilterChange('search', e.target.value)}
                  placeholder="Order #, customer name, email..."
                  className="input"
                />
              </div>

              <div>
                <label className="block text-sm text-gray-400 mb-2">Filter by Status</label>
                <select
                  value={filters.status}
                  onChange={(e) => handleFilterChange('status', e.target.value)}
                  className="input"
                >
                  <option value="all">All Orders</option>
                  <option value="Pending">Pending</option>
                  <option value="Processing">Processing</option>
                  <option value="Shipped">Shipped</option>
                  <option value="Delivered">Delivered</option>
                  <option value="Cancelled">Cancelled</option>
                </select>
              </div>

              <div className="flex items-end">
                <button
                  onClick={() => setFilters({ status: 'all', search: '' })}
                  className="btn btn-secondary w-full"
                >
                  Clear Filters
                </button>
              </div>
            </div>
          </div>

          {loading ? (
            <div className="flex justify-center items-center h-64">
              <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary-600"></div>
            </div>
          ) : (
            <div className="card overflow-hidden">
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-dark-800">
                    <tr>
                      <th className="text-left p-4 text-primary-500 font-semibold">Order #</th>
                      <th className="text-left p-4 text-primary-500 font-semibold">Customer</th>
                      <th className="text-left p-4 text-primary-500 font-semibold">Email</th>
                      <th className="text-left p-4 text-primary-500 font-semibold">Date</th>
                      <th className="text-left p-4 text-primary-500 font-semibold">Total</th>
                      <th className="text-left p-4 text-primary-500 font-semibold">Status</th>
                      <th className="text-left p-4 text-primary-500 font-semibold">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    {orders.map((order) => (
                      <tr key={order._id} className="border-t border-dark-600 hover:bg-dark-800">
                        <td className="p-4 font-semibold text-primary-500">
                          {order.orderNumber}
                        </td>
                        <td className="p-4">{order.customer.name}</td>
                        <td className="p-4 text-gray-400">{order.customer.email}</td>
                        <td className="p-4 text-gray-400">
                          {new Date(order.orderDate).toLocaleDateString()}
                        </td>
                        <td className="p-4 font-semibold">${order.totalAmount.toFixed(2)}</td>
                        <td className="p-4">
                          <span className={`px-3 py-1 rounded-full text-sm font-semibold ${
                            order.status === 'Pending' ? 'bg-yellow-500/20 text-yellow-400' :
                            order.status === 'Processing' ? 'bg-blue-500/20 text-blue-400' :
                            order.status === 'Shipped' ? 'bg-purple-500/20 text-purple-400' :
                            order.status === 'Delivered' ? 'bg-green-500/20 text-green-400' :
                            'bg-gray-500/20 text-gray-400'
                          }`}>
                            {order.status}
                          </span>
                        </td>
                        <td className="p-4">
                          <button
                            onClick={() => setSearchParams({ view: order._id })}
                            className="btn btn-primary text-sm px-4 py-2"
                          >
                            View / Edit
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
              {orders.length === 0 && (
                <div className="text-center py-12 text-gray-400">
                  No orders found
                </div>
              )}
            </div>
          )}
        </>
      )}
    </div>
  );
};

export default AdminOrders;