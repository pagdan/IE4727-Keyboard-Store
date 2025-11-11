import { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { adminAPI } from '../../services/api';
import { useAuth } from '../../context/AuthContext';
import toast from 'react-hot-toast';

const AdminDashboard = () => {
  const navigate = useNavigate();
  const { logout } = useAuth();
  const [stats, setStats] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchStats();
  }, []);

  const fetchStats = async () => {
    try {
      const response = await adminAPI.getStats();
      setStats(response.data.data);
    } catch (error) {
      toast.error('Failed to load dashboard stats');
    } finally {
      setLoading(false);
    }
  };

  const handleLogout = () => {
    logout();
    toast.success('Logged out successfully');
    navigate('/admin/login');
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center h-screen">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary-600"></div>
      </div>
    );
  }

  return (
    <div className="container-custom py-8">
      <div className="flex justify-between items-center mb-8">
        <h1 className="text-4xl font-bold">Admin Dashboard</h1>
        <div className="flex gap-4">
          <Link to="/admin/orders" className="btn btn-secondary">
            Orders
          </Link>
          <Link to="/admin/products" className="btn btn-secondary">
            Products
          </Link>
          <button onClick={handleLogout} className="btn btn-secondary">
            Logout
          </button>
        </div>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div className="card p-6">
          <p className="text-gray-400 text-sm mb-2">Total Orders</p>
          <p className="text-3xl font-bold text-primary-500">
            {stats?.totalOrders || 0}
          </p>
        </div>
        <div className="card p-6">
          <p className="text-gray-400 text-sm mb-2">Pending Orders</p>
          <p className="text-3xl font-bold text-yellow-400">
            {stats?.pendingOrders || 0}
          </p>
        </div>
        <div className="card p-6">
          <p className="text-gray-400 text-sm mb-2">Total Revenue</p>
          <p className="text-3xl font-bold text-green-400">
            ${stats?.totalRevenue?.toFixed(2) || '0.00'}
          </p>
        </div>
        <div className="card p-6">
          <p className="text-gray-400 text-sm mb-2">Avg Order Value</p>
          <p className="text-3xl font-bold text-blue-400">
            ${stats?.avgOrderValue?.toFixed(2) || '0.00'}
          </p>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Recent Orders Table */}
        <div className="lg:col-span-2">
          <h2 className="text-2xl font-bold mb-4">Recent Orders</h2>
          <div className="card overflow-hidden">
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead className="bg-dark-800">
                  <tr>
                    <th className="text-left p-4 text-primary-500 font-semibold">Order #</th>
                    <th className="text-left p-4 text-primary-500 font-semibold">Customer</th>
                    <th className="text-left p-4 text-primary-500 font-semibold">Date</th>
                    <th className="text-left p-4 text-primary-500 font-semibold">Total</th>
                    <th className="text-left p-4 text-primary-500 font-semibold">Status</th>
                  </tr>
                </thead>
                <tbody>
                  {stats?.recentOrders?.map((order) => (
                    <tr key={order._id} className="border-t border-dark-600 hover:bg-dark-800">
                      <td className="p-4">
                        <Link
                          to={`/admin/orders?view=${order._id}`}
                          className="text-primary-500 hover:text-primary-400 font-semibold"
                        >
                          {order.orderNumber}
                        </Link>
                      </td>
                      <td className="p-4 text-gray-300">{order.customer.name}</td>
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
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
          <div className="text-center mt-4">
            <Link to="/admin/orders" className="btn btn-primary">
              View All Orders
            </Link>
          </div>
        </div>

        {/* Low Stock Alert */}
        <div>
          <h2 className="text-2xl font-bold mb-4">Low Stock Alert</h2>
          <div className="card p-6">
            {stats?.lowStockProducts?.length > 0 ? (
              <div className="space-y-4">
                {stats.lowStockProducts.map((product) => (
                  <div
                    key={product._id}
                    className="flex justify-between items-center pb-4 border-b border-dark-600 last:border-0"
                  >
                    <div>
                      <p className="font-semibold">{product.name}</p>
                      <p className="text-red-400 text-sm">
                        Only {product.stockQuantity} left!
                      </p>
                    </div>
                    <span className="bg-red-500/20 text-red-400 px-3 py-1 rounded-full text-sm font-bold">
                      {product.stockQuantity}
                    </span>
                  </div>
                ))}
                <Link
                  to="/admin/products"
                  className="btn btn-secondary w-full block text-center mt-4"
                >
                  Manage Inventory
                </Link>
              </div>
            ) : (
              <div className="text-center py-8">
                <p className="text-green-400 text-2xl mb-2">✓</p>
                <p className="text-gray-400">All products are well stocked!</p>
              </div>
            )}
          </div>

          {/* Quick Actions */}
          <div className="mt-6">
            <h3 className="text-xl font-bold mb-4">Quick Actions</h3>
            <div className="space-y-3">
              <Link
                to="/admin/products"
                className="btn btn-primary w-full block text-center"
              >
                Manage Products
              </Link>
              <Link
                to="/admin/orders"
                className="btn btn-secondary w-full block text-center"
              >
                Manage Orders
              </Link>
              
                href="/"
                className="btn btn-secondary w-full block text-center"
              <a>
                View Store
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default AdminDashboard;