const express = require('express');
const router = express.Router();
const Order = require('../models/Order');
const Product = require('../models/Product');
const { protect } = require('../middleware/auth');

// @route   POST /api/orders
// @desc    Create new order
// @access  Public
router.post('/', async (req, res) => {
  try {
    const { customer, shippingAddress, items } = req.body;
    
    // Validate items and check stock
    const orderItems = [];
    let totalAmount = 0;
    
    for (const item of items) {
      const product = await Product.findById(item.productId);
      
      if (!product) {
        return res.status(404).json({
          success: false,
          message: `Product not found: ${item.productId}`
        });
      }
      
      if (product.stockQuantity < item.quantity) {
        return res.status(400).json({
          success: false,
          message: `Insufficient stock for ${product.name}. Available: ${product.stockQuantity}`
        });
      }
      
      // Add to order items
      orderItems.push({
        product: product._id,
        productName: product.name,
        quantity: item.quantity,
        priceAtPurchase: product.price
      });
      
      totalAmount += product.price * item.quantity;
      
      // Update stock
      product.stockQuantity -= item.quantity;
      await product.save();
    }
    
    // Create order
    const order = await Order.create({
      customer,
      shippingAddress,
      items: orderItems,
      totalAmount
    });
    
    // Populate product details
    await order.populate('items.product');
    
    res.status(201).json({
      success: true,
      data: order,
      message: 'Order created successfully'
    });
  } catch (error) {
    res.status(400).json({
      success: false,
      message: error.message
    });
  }
});

// @route   GET /api/orders/:orderNumber
// @desc    Get order by order number
// @access  Public
router.get('/:orderNumber', async (req, res) => {
  try {
    const order = await Order.findOne({ 
      orderNumber: req.params.orderNumber 
    }).populate('items.product');
    
    if (!order) {
      return res.status(404).json({
        success: false,
        message: 'Order not found'
      });
    }
    
    res.json({
      success: true,
      data: order
    });
  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
});

// @route   GET /api/orders
// @desc    Get all orders (Admin only)
// @access  Private
router.get('/', protect, async (req, res) => {
  try {
    const { status, search, page = 1, limit = 20 } = req.query;
    
    // Build query
    let query = {};
    
    if (status && status !== 'all') {
      query.status = status;
    }
    
    if (search) {
      query.$or = [
        { orderNumber: { $regex: search, $options: 'i' } },
        { 'customer.name': { $regex: search, $options: 'i' } },
        { 'customer.email': { $regex: search, $options: 'i' } }
      ];
    }
    
    // Execute query with pagination
    const skip = (page - 1) * limit;
    const orders = await Order.find(query)
      .sort({ orderDate: -1 })
      .limit(parseInt(limit))
      .skip(skip)
      .populate('items.product');
    
    const total = await Order.countDocuments(query);
    
    res.json({
      success: true,
      data: orders,
      pagination: {
        page: parseInt(page),
        limit: parseInt(limit),
        total,
        pages: Math.ceil(total / limit)
      }
    });
  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
});

// @route   PUT /api/orders/:id/status
// @desc    Update order status (Admin only)
// @access  Private
router.put('/:id/status', protect, async (req, res) => {
  try {
    const { status } = req.body;
    
    const order = await Order.findByIdAndUpdate(
      req.params.id,
      { status },
      { new: true, runValidators: true }
    ).populate('items.product');
    
    if (!order) {
      return res.status(404).json({
        success: false,
        message: 'Order not found'
      });
    }
    
    res.json({
      success: true,
      data: order,
      message: 'Order status updated successfully'
    });
  } catch (error) {
    res.status(400).json({
      success: false,
      message: error.message
    });
  }
});

module.exports = router;