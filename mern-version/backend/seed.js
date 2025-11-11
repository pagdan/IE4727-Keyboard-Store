const mongoose = require('mongoose');
require('dotenv').config();

const Product = require('./models/Product');
const Admin = require('./models/Admin');

// Sample products data
const products = [
  // Keyboards
  {
    name: 'GMMK Pro 75% Keyboard',
    description: 'Premium gasket-mounted mechanical keyboard with hot-swappable switches and aluminum frame',
    price: 199.99,
    category: 'Keyboards',
    stockQuantity: 15,
    featured: true,
    specifications: new Map([
      ['Layout', '75%'],
      ['Switch', 'Hot-swap'],
      ['Material', 'Aluminum'],
      ['Connectivity', 'USB-C'],
      ['RGB', 'Yes']
    ])
  },
  {
    name: 'Keychron Q1 Pro',
    description: 'Wireless mechanical keyboard with QMK/VIA support and premium build quality',
    price: 189.99,
    category: 'Keyboards',
    stockQuantity: 20,
    featured: true,
    specifications: new Map([
      ['Layout', '75%'],
      ['Switch', 'Hot-swap'],
      ['Material', 'Aluminum'],
      ['Connectivity', 'Wireless/USB-C'],
      ['RGB', 'Yes']
    ])
  },
  {
    name: 'Ducky One 3 TKL',
    description: 'Tenkeyless mechanical keyboard with excellent build quality and Cherry MX switches',
    price: 139.99,
    category: 'Keyboards',
    stockQuantity: 25,
    specifications: new Map([
      ['Layout', 'TKL'],
      ['Switch', 'Cherry MX'],
      ['Material', 'Plastic'],
      ['Connectivity', 'USB-C'],
      ['RGB', 'Yes']
    ])
  },
  {
    name: 'Tofu65 Custom Kit',
    description: 'DIY custom keyboard kit for enthusiasts',
    price: 149.99,
    category: 'Keyboards',
    stockQuantity: 10,
    specifications: new Map([
      ['Layout', '65%'],
      ['Switch', 'Hot-swap'],
      ['Material', 'Aluminum'],
      ['Connectivity', 'USB-C'],
      ['RGB', 'Optional']
    ])
  },
  
  // Switches
  {
    name: 'Cherry MX Red (Pack of 70)',
    description: 'Linear switches with smooth actuation, perfect for gaming and typing',
    price: 49.99,
    category: 'Switches',
    stockQuantity: 50,
    featured: true,
    specifications: new Map([
      ['Type', 'Linear'],
      ['Actuation', '45g'],
      ['Travel', '4mm'],
      ['Sound', 'Quiet']
    ])
  },
  {
    name: 'Gateron Yellow (Pack of 90)',
    description: 'Budget-friendly smooth linear switches popular in custom builds',
    price: 24.99,
    category: 'Switches',
    stockQuantity: 80,
    specifications: new Map([
      ['Type', 'Linear'],
      ['Actuation', '50g'],
      ['Travel', '4mm'],
      ['Sound', 'Quiet']
    ])
  },
  {
    name: 'Glorious Panda (Pack of 36)',
    description: 'Premium tactile switches with satisfying bump and smooth return',
    price: 34.99,
    category: 'Switches',
    stockQuantity: 40,
    specifications: new Map([
      ['Type', 'Tactile'],
      ['Actuation', '67g'],
      ['Travel', '4mm'],
      ['Sound', 'Medium']
    ])
  },
  {
    name: 'Kailh Box White (Pack of 90)',
    description: 'Clicky switches with crisp feedback and dust-resistant design',
    price: 29.99,
    category: 'Switches',
    stockQuantity: 60,
    specifications: new Map([
      ['Type', 'Clicky'],
      ['Actuation', '50g'],
      ['Travel', '3.6mm'],
      ['Sound', 'Loud']
    ])
  },
  
  // Keycaps
  {
    name: 'GMK Striker Keycap Set',
    description: 'Premium double-shot ABS keycaps with bold blue and white colorway',
    price: 129.99,
    category: 'Keycaps',
    stockQuantity: 12,
    featured: true,
    specifications: new Map([
      ['Profile', 'Cherry'],
      ['Material', 'ABS'],
      ['Compatibility', 'MX'],
      ['Keys', '139']
    ])
  },
  {
    name: 'PBT Islander Keycaps',
    description: 'Dye-sublimated PBT keycaps with tropical island theme',
    price: 89.99,
    category: 'Keycaps',
    stockQuantity: 18,
    specifications: new Map([
      ['Profile', 'Cherry'],
      ['Material', 'PBT'],
      ['Compatibility', 'MX'],
      ['Keys', '150']
    ])
  },
  {
    name: 'Drop MT3 Susuwatari',
    description: 'High-profile MT3 keycaps with elegant black and grey design',
    price: 99.99,
    category: 'Keycaps',
    stockQuantity: 15,
    specifications: new Map([
      ['Profile', 'MT3'],
      ['Material', 'PBT'],
      ['Compatibility', 'MX'],
      ['Keys', '125']
    ])
  },
  {
    name: 'XDA Canvas Keycaps',
    description: 'Uniform profile keycaps with vintage computing aesthetic',
    price: 79.99,
    category: 'Keycaps',
    stockQuantity: 20,
    specifications: new Map([
      ['Profile', 'XDA'],
      ['Material', 'PBT'],
      ['Compatibility', 'MX'],
      ['Keys', '140']
    ])
  }
];

// Seed database
const seedDatabase = async () => {
  try {
    // Connect to MongoDB
    await mongoose.connect(process.env.MONGODB_URI);
    console.log('✅ Connected to MongoDB');
    
    // Clear existing data
    await Product.deleteMany({});
    await Admin.deleteMany({});
    console.log('🗑️  Cleared existing data');
    
    // Insert products
    await Product.insertMany(products);
    console.log('✅ Products seeded');
    
    // Create default admin
    await Admin.create({
      username: 'admin',
      email: 'admin@keyboardhub.com',
      password: 'admin123'
    });
    console.log('✅ Admin user created (username: admin, password: admin123)');
    
    console.log('🎉 Database seeding completed!');
    process.exit(0);
  } catch (error) {
    console.error('❌ Error seeding database:', error);
    process.exit(1);
  }
};

seedDatabase();