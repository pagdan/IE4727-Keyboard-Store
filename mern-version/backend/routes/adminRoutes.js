const express = require('express');
const router = express.Router();
const Admin = require('../models/Admin');
const Order = require('../models/Order');
const Product = require('../models/Product');
const { protect, generateToken } = require('../middleware/auth');

// @route   POST /api/admin/login
// @desc    Admin login
// @access  Public
router.post('/login', async (req, res) => {
  try {
    const { username, password } = req.body;
    
    if (!username || !password) {
      return res.status(400).json({
        success: false,
        message: 'Please provide username and password'
      });
    }
    
    // Find admin and include password
    const admin = await Admin.findOne({ username }).select('+password');
    
    if (!admin) {
      return res.status(401).json({
        success: false,
        message: 'Invalid credentials'
      });
    }
    
    // Check password
    const isMatch = await admin.comparePassword(password);
    
    if (!isMatch) {
      return res.status(401).json({
        success: false,
        message: 'Invalid credentials'
      });
    }
    
    // Generate token
    const token = generateToken(admin._id);
    
    res.json({
      success: true,
      token,
      admin: {
        id: admin._id,
        username: admin.username,
        email: admin.email,
        role: admin.role
      }
    });
  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
});

// @route   POST /api/admin/register
// @desc    Register new admin
// @access  Public (should be protected in production)
router.post('/register', async (req, res) => {
  try {
    const { username, email, password } = req.body;
    
    // Check if admin already exists
    const existingAdmin = await Admin.findOne({ 
      $or: [{ username }, { email }] 
    });
    
    if (existingAdmin) {
      return res.status(400).json({
        success: false,
        message: 'Admin with this username or email already exists'
      });
    }
    
    // Create admin
    const admin = await Admin.create({
      username,
      email,
      password
    });
    
    // Generate token
    const token = generateToken(admin._id);
    
    res.status(201).json({
      success: true,
      token,
      admin: {
        id: admin._id,
        username: admin.username,
        email: admin.email,
        role: admin.role
      }
    });
  } catch (error) {
    res.status(400).json({
      success: false,
      message: error.message
    });
  }
});

// @route   GET /api/admin/stats
// @desc    Get dashboard statistics
// @access  Private
router.get('/stats', protect, async (req, res) => {
  try {
    // Get total orders
    const totalOrders = await Order.countDocuments();
    
    // Get pending orders
    const pendingOrders = await Order.countDocuments({ status: 'Pending' });
    
    // Get total revenue
    const revenueResult = await Order.aggregate([
      { $group: { _id: null, total: { $sum: '$totalAmount' } } }
    ]);
    const totalRevenue = revenueResult.length > 0 ? revenueResult[0].total : 0;
    
    // Get average order value
    const avgOrderValue = totalOrders > 0 ? totalRevenue / totalOrders : 0;
    
    // Get low stock products
    const lowStockProducts = await Product.find({ stockQuantity: { $lt: 10 } })
      .select('name stockQuantity')
      .limit(5);
    
    // Get recent orders
    const recentOrders = await Order.find()
      .sort({ orderDate: -1 })
      .limit(10)
      .populate('items.product');
    
    res.json({
      success: true,
      data: {
        totalOrders,
        pendingOrders,
        totalRevenue,
        avgOrderValue,
        lowStockProducts,
        recentOrders
      }
    });
  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
});

// @route   GET /api/admin/me
// @desc    Get current admin info
// @access  Private
router.get('/me', protect, async (req, res) => {
  try {
    const admin = await Admin.findById(req.admin._id);
    
    res.json({
      success: true,
      data: admin
    });
  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
});

module.exports = router;