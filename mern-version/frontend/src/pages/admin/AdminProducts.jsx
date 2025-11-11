import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { productAPI } from '../../services/api';
import toast from 'react-hot-toast';

const AdminProducts = () => {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [editingId, setEditingId] = useState(null);
  const [editData, setEditData] = useState({});

  useEffect(() => {
    fetchProducts();
  }, []);

  const fetchProducts = async () => {
    setLoading(true);
    try {
      const response = await productAPI.getAll({});
      setProducts(response.data.data);
    } catch (error) {
      toast.error('Failed to load products');
    } finally {
      setLoading(false);
    }
  };

  const handleEdit = (product) => {
    setEditingId(product._id);
    setEditData({
      price: product.price,
      stockQuantity: product.stockQuantity
    });
  };

  const handleSave = async (productId) => {
    try {
      await productAPI.update(productId, editData);
      toast.success('Product updated successfully!');
      setEditingId(null);
      fetchProducts();
    } catch (error) {
      toast.error('Failed to update product');
    }
  };

  const handleCancel = () => {
    setEditingId(null);
    setEditData({});
  };

  return (
    <div className="container-custom py-8">
      <div className="flex justify-between items-center mb-8">
        <h1 className="text-4xl font-bold">Manage Products</h1>
        <Link to="/admin/dashboard" className="btn btn-secondary">
          ← Back to Dashboard
        </Link>
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
                  <th className="text-left p-4 text-primary-500 font-semibold">ID</th>
                  <th className="text-left p-4 text-primary-500 font-semibold">Product Name</th>
                  <th className="text-left p-4 text-primary-500 font-semibold">Category</th>
                  <th className="text-left p-4 text-primary-500 font-semibold">Price</th>
                  <th className="text-left p-4 text-primary-500 font-semibold">Stock</th>
                  <th className="text-left p-4 text-primary-500 font-semibold">Actions</th>
                </tr>
              </thead>
              <tbody>
                {products.map((product) => (
                  <tr key={product._id} className="border-t border-dark-600 hover:bg-dark-800">
                    <td className="p-4 text-primary-500 font-semibold">
                      {product._id.slice(-6)}
                    </td>
                    <td className="p-4">{product.name}</td>
                    <td className="p-4 text-gray-400">{product.category}</td>
                    <td className="p-4">
                      {editingId === product._id ? (
                        <input
                          type="number"
                          step="0.01"
                          value={editData.price}
                          onChange={(e) => setEditData({ ...editData, price: parseFloat(e.target.value) })}
                          className="input w-24"
                        />
                      ) : (
                        <span className="font-semibold">${product.price.toFixed(2)}</span>
                      )}
                    </td>
                    <td className="p-4">
                      {editingId === product._id ? (
                        <input
                          type="number"
                          value={editData.stockQuantity}
                          onChange={(e) => setEditData({ ...editData, stockQuantity: parseInt(e.target.value) })}
                          className="input w-20"
                        />
                      ) : (
                        <span className={`font-semibold ${
                          product.stockQuantity < 10 ? 'text-red-400' : 'text-green-400'
                        }`}>
                          {product.stockQuantity}
                        </span>
                      )}
                    </td>
                    <td className="p-4">
                      {editingId === product._id ? (
                        <div className="flex gap-2">
                          <button
                            onClick={() => handleSave(product._id)}
                            className="btn btn-primary text-sm px-4 py-2"
                          >
                            Save
                          </button>
                          <button
                            onClick={handleCancel}
                            className="btn btn-secondary text-sm px-4 py-2"
                          >
                            Cancel
                          </button>
                        </div>
                      ) : (
                        <button
                          onClick={() => handleEdit(product)}
                          className="btn btn-primary text-sm px-4 py-2"
                        >
                          Edit
                        </button>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}
    </div>
  );
};

export default AdminProducts;