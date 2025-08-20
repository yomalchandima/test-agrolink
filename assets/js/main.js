// AgroLink - Main JavaScript File
// Interactive components and functionality

// Global state
let cart = JSON.parse(localStorage.getItem('agrolink_cart')) || [];
let currentUser = JSON.parse(localStorage.getItem('agrolink_user')) || null;

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

// Initialize Application
function initializeApp() {
    // Initialize navigation
    initNavigation();
    
    // Initialize forms
    initForms();
    
    // Initialize modals
    initModals();
    
    // Initialize cart
    updateCartUI();
    
    // Initialize user state
    updateUserState();
    
    // Initialize page-specific functionality
    const page = getPageName();
    switch(page) {
        case 'index':
            initLandingPage();
            break;
        case 'products':
            initProductsPage();
            break;
        case 'cart':
            initCartPage();
            break;
        case 'dashboard_buyer':
        case 'dashboard_farmer':
        case 'dashboard_transporter':
        case 'dashboard_admin':
            initDashboard();
            break;
    }
}

// Get current page name
function getPageName() {
    const path = window.location.pathname;
    const page = path.split('/').pop().replace('.html', '') || 'index';
    return page;
}

// Navigation functionality
function initNavigation() {
    // Mobile menu toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navLinks = document.querySelector('.nav-links');
    
    if (mobileMenuBtn && navLinks) {
        mobileMenuBtn.addEventListener('click', function() {
            navLinks.classList.toggle('active');
        });
    }
    
    // Active navigation links
    const currentPage = getPageName();
    const navItems = document.querySelectorAll('.nav-links a, .sidebar-menu a');
    
    navItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href && href.includes(currentPage)) {
            item.classList.add('active');
        }
    });
}

// Form handling
function initForms() {
    // Login form
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
    
    // Registration forms
    const registerForms = document.querySelectorAll('[id^="register"]');
    registerForms.forEach(form => {
        form.addEventListener('submit', handleRegistration);
    });
    
    // Product form
    const productForm = document.getElementById('productForm');
    if (productForm) {
        productForm.addEventListener('submit', handleProductSubmission);
    }
    
    // Checkout form
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', handleCheckout);
    }
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', validateField);
            input.addEventListener('input', clearError);
        });
    });
}

// Modal functionality
function initModals() {
    // Modal triggers
    const modalTriggers = document.querySelectorAll('[data-modal]');
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.getAttribute('data-modal');
            openModal(modalId);
        });
    });
    
    // Modal close buttons
    const closeButtons = document.querySelectorAll('.modal-close, [data-modal-close]');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                closeModal(modal.id);
            }
        });
    });
    
    // Close modal on backdrop click
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });
}

// Landing page specific functionality
function initLandingPage() {
    // Smooth scrolling for anchor links
    const anchors = document.querySelectorAll('a[href^="#"]');
    anchors.forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // FAQ accordion
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        if (question) {
            question.addEventListener('click', function() {
                const answer = item.querySelector('.faq-answer');
                const isOpen = item.classList.contains('open');
                
                // Close all FAQ items
                faqItems.forEach(faq => {
                    faq.classList.remove('open');
                    const faqAnswer = faq.querySelector('.faq-answer');
                    if (faqAnswer) faqAnswer.style.display = 'none';
                });
                
                // Open clicked item if it wasn't already open
                if (!isOpen) {
                    item.classList.add('open');
                    if (answer) answer.style.display = 'block';
                }
            });
        }
    });
}

// Products page functionality
function initProductsPage() {
    // Filter functionality
    const filterInputs = document.querySelectorAll('.filters input, .filters select');
    filterInputs.forEach(input => {
        input.addEventListener('change', applyFilters);
        input.addEventListener('input', debounce(applyFilters, 300));
    });
    
    // Sort functionality
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', sortProducts);
    }
    
    // Add to cart buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            addToCart(productId);
        });
    });
}

// Cart functionality
function initCartPage() {
    renderCartItems();
    
    // Quantity controls
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('quantity-increase')) {
            const productId = e.target.getAttribute('data-product-id');
            updateCartQuantity(productId, 1);
        } else if (e.target.classList.contains('quantity-decrease')) {
            const productId = e.target.getAttribute('data-product-id');
            updateCartQuantity(productId, -1);
        } else if (e.target.classList.contains('remove-item')) {
            const productId = e.target.getAttribute('data-product-id');
            removeFromCart(productId);
        }
    });
}

// Dashboard functionality
function initDashboard() {
    // Statistics cards animation
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Table sorting
    const sortableHeaders = document.querySelectorAll('.table th[data-sort]');
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const column = this.getAttribute('data-sort');
            sortTable(this.closest('table'), column);
        });
    });
    
    // Delete confirmation
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const message = this.getAttribute('data-confirm') || 'Are you sure you want to delete this item?';
            if (confirm(message)) {
                // Handle delete action
                console.log('Item deleted');
                showNotification('Item deleted successfully', 'success');
            }
        });
    });
}

// Authentication functions
function handleLogin(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const email = formData.get('email');
    const password = formData.get('password');
    const role = formData.get('role');
    
    // Simulate login (replace with actual API call)
    if (email && password && role) {
        const user = {
            id: generateId(),
            email: email,
            role: role,
            name: getNameFromEmail(email),
            loginTime: new Date().toISOString()
        };
        
        localStorage.setItem('agrolink_user', JSON.stringify(user));
        showNotification('Login successful! Redirecting...', 'success');
        
        setTimeout(() => {
            redirectToDashboard(role);
        }, 1500);
    } else {
        showNotification('Please fill in all fields', 'error');
    }
}

function handleRegistration(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    
    // Basic validation
    if (!data.email || !data.password || !data.name) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }
    
    if (data.password !== data.confirmPassword) {
        showNotification('Passwords do not match', 'error');
        return;
    }
    
    // Simulate registration (replace with actual API call)
    const user = {
        id: generateId(),
        ...data,
        registrationTime: new Date().toISOString()
    };
    
    localStorage.setItem('agrolink_user', JSON.stringify(user));
    showNotification('Registration successful! Redirecting to login...', 'success');
    
    setTimeout(() => {
        window.location.href = 'login.html';
    }, 1500);
}

function logout() {
    localStorage.removeItem('agrolink_user');
    localStorage.removeItem('agrolink_cart');
    showNotification('Logged out successfully', 'success');
    setTimeout(() => {
        window.location.href = 'index.html';
    }, 1000);
}

// Cart functions
function addToCart(productId) {
    const product = getProductById(productId);
    if (!product) return;
    
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: productId,
            name: product.name,
            price: product.price,
            image: product.image,
            farmer: product.farmer,
            quantity: 1
        });
    }
    
    localStorage.setItem('agrolink_cart', JSON.stringify(cart));
    updateCartUI();
    showNotification('Product added to cart', 'success');
}

function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    localStorage.setItem('agrolink_cart', JSON.stringify(cart));
    updateCartUI();
    renderCartItems();
    showNotification('Product removed from cart', 'success');
}

function updateCartQuantity(productId, change) {
    const item = cart.find(item => item.id === productId);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(productId);
            return;
        }
        localStorage.setItem('agrolink_cart', JSON.stringify(cart));
        updateCartUI();
        renderCartItems();
    }
}

function updateCartUI() {
    const cartCount = document.querySelector('.cart-count');
    const cartTotal = document.querySelector('.cart-total');
    
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    if (cartCount) {
        cartCount.textContent = totalItems;
        cartCount.style.display = totalItems > 0 ? 'inline' : 'none';
    }
    
    if (cartTotal) {
        cartTotal.textContent = `Rs. ${totalPrice.toFixed(2)}`;
    }
}

function renderCartItems() {
    const cartContainer = document.getElementById('cartItems');
    if (!cartContainer) return;
    
    if (cart.length === 0) {
        cartContainer.innerHTML = `
            <div class="text-center p-xl">
                <h3>Your cart is empty</h3>
                <p class="text-muted">Add some products to get started</p>
                <a href="products.html" class="btn btn-primary">Browse Products</a>
            </div>
        `;
        return;
    }
    
    cartContainer.innerHTML = cart.map(item => `
        <div class="cart-item">
            <div class="cart-item-image">
                ${item.image ? `<img src="${item.image}" alt="${item.name}">` : '🥬'}
            </div>
            <div class="cart-item-info">
                <div class="cart-item-title">${item.name}</div>
                <div class="cart-item-farmer">by ${item.farmer}</div>
                <div class="cart-item-price">Rs. ${item.price}/kg</div>
            </div>
            <div class="quantity-controls">
                <button class="quantity-btn quantity-decrease" data-product-id="${item.id}">-</button>
                <span class="quantity">${item.quantity}</span>
                <button class="quantity-btn quantity-increase" data-product-id="${item.id}">+</button>
            </div>
            <div class="cart-item-total">
                Rs. ${(item.price * item.quantity).toFixed(2)}
            </div>
            <button class="btn btn-sm remove-item" data-product-id="${item.id}">Remove</button>
        </div>
    `).join('');
}

// Product functions
function applyFilters() {
    const category = document.getElementById('categoryFilter')?.value || '';
    const location = document.getElementById('locationFilter')?.value || '';
    const priceMin = document.getElementById('priceMin')?.value || '';
    const priceMax = document.getElementById('priceMax')?.value || '';
    const search = document.getElementById('searchInput')?.value || '';
    
    const products = document.querySelectorAll('.product-card');
    
    products.forEach(product => {
        const productCategory = product.getAttribute('data-category') || '';
        const productLocation = product.getAttribute('data-location') || '';
        const productPrice = parseFloat(product.getAttribute('data-price')) || 0;
        const productName = product.querySelector('.product-title')?.textContent.toLowerCase() || '';
        
        let shouldShow = true;
        
        if (category && productCategory !== category) shouldShow = false;
        if (location && productLocation !== location) shouldShow = false;
        if (priceMin && productPrice < parseFloat(priceMin)) shouldShow = false;
        if (priceMax && productPrice > parseFloat(priceMax)) shouldShow = false;
        if (search && !productName.includes(search.toLowerCase())) shouldShow = false;
        
        product.style.display = shouldShow ? 'block' : 'none';
    });
}

function sortProducts() {
    const sortValue = document.getElementById('sortSelect')?.value;
    if (!sortValue) return;
    
    const container = document.querySelector('.product-grid');
    if (!container) return;
    
    const products = Array.from(container.querySelectorAll('.product-card'));
    
    products.sort((a, b) => {
        switch (sortValue) {
            case 'price-low':
                return parseFloat(a.getAttribute('data-price')) - parseFloat(b.getAttribute('data-price'));
            case 'price-high':
                return parseFloat(b.getAttribute('data-price')) - parseFloat(a.getAttribute('data-price'));
            case 'name':
                return a.querySelector('.product-title').textContent.localeCompare(b.querySelector('.product-title').textContent);
            default:
                return 0;
        }
    });
    
    // Re-append sorted products
    products.forEach(product => container.appendChild(product));
}

// Checkout functions
function handleCheckout(e) {
    e.preventDefault();
    
    if (cart.length === 0) {
        showNotification('Your cart is empty', 'error');
        return;
    }
    
    const formData = new FormData(e.target);
    const orderData = {
        id: generateId(),
        items: cart,
        customer: Object.fromEntries(formData.entries()),
        total: cart.reduce((sum, item) => sum + (item.price * item.quantity), 0),
        orderDate: new Date().toISOString(),
        status: 'pending'
    };
    
    // Simulate order processing
    showNotification('Processing your order...', 'info');
    
    setTimeout(() => {
        // Clear cart
        cart = [];
        localStorage.setItem('agrolink_cart', JSON.stringify(cart));
        
        // Store order (in a real app, this would be sent to backend)
        const orders = JSON.parse(localStorage.getItem('agrolink_orders')) || [];
        orders.push(orderData);
        localStorage.setItem('agrolink_orders', JSON.stringify(orders));
        
        // Redirect to success page
        window.location.href = `order_success.html?order=${orderData.id}`;
    }, 2000);
}

// Modal functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
}

// Form validation
function validateField(e) {
    const field = e.target;
    const value = field.value.trim();
    
    clearError(field);
    
    if (field.hasAttribute('required') && !value) {
        showFieldError(field, 'This field is required');
        return false;
    }
    
    if (field.type === 'email' && value && !isValidEmail(value)) {
        showFieldError(field, 'Please enter a valid email address');
        return false;
    }
    
    if (field.type === 'password' && value && value.length < 6) {
        showFieldError(field, 'Password must be at least 6 characters');
        return false;
    }
    
    if (field.name === 'confirmPassword') {
        const passwordField = field.form.querySelector('[name="password"]');
        if (passwordField && value !== passwordField.value) {
            showFieldError(field, 'Passwords do not match');
            return false;
        }
    }
    
    return true;
}

function showFieldError(field, message) {
    field.classList.add('error');
    
    let errorElement = field.parentNode.querySelector('.form-text.error');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'form-text error';
        field.parentNode.appendChild(errorElement);
    }
    
    errorElement.textContent = message;
}

function clearError(field) {
    if (typeof field === 'object' && field.target) {
        field = field.target;
    }
    
    field.classList.remove('error');
    const errorElement = field.parentNode.querySelector('.form-text.error');
    if (errorElement) {
        errorElement.remove();
    }
}

// Utility functions
function generateId() {
    return 'id_' + Math.random().toString(36).substr(2, 9);
}

function getNameFromEmail(email) {
    return email.split('@')[0].replace(/[._]/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    // Add to top of body
    document.body.insertBefore(notification, document.body.firstChild);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);
}

function redirectToDashboard(role) {
    const dashboardUrls = {
        farmer: 'dashboard_farmer.html',
        buyer: 'dashboard_buyer.html',
        transporter: 'dashboard_transporter.html',
        admin: 'dashboard_admin.html'
    };
    
    const url = dashboardUrls[role] || 'index.html';
    window.location.href = url;
}

function updateUserState() {
    const user = getCurrentUser();
    const userInfo = document.querySelector('.user-info');
    const loginLinks = document.querySelectorAll('.login-link');
    const logoutLinks = document.querySelectorAll('.logout-link');
    
    if (user) {
        if (userInfo) {
            userInfo.innerHTML = `
                <span>Welcome, ${user.name}</span>
                <button onclick="logout()" class="btn btn-sm">Logout</button>
            `;
        }
        
        loginLinks.forEach(link => link.style.display = 'none');
        logoutLinks.forEach(link => link.style.display = 'inline-block');
    } else {
        if (userInfo) {
            userInfo.innerHTML = '';
        }
        
        loginLinks.forEach(link => link.style.display = 'inline-block');
        logoutLinks.forEach(link => link.style.display = 'none');
    }
}

function getCurrentUser() {
    return JSON.parse(localStorage.getItem('agrolink_user'));
}

function requireAuth(allowedRoles = []) {
    const user = getCurrentUser();
    
    if (!user) {
        showNotification('Please login to access this page', 'error');
        setTimeout(() => {
            window.location.href = 'login.html';
        }, 1500);
        return false;
    }
    
    if (allowedRoles.length > 0 && !allowedRoles.includes(user.role)) {
        showNotification('Access denied', 'error');
        setTimeout(() => {
            redirectToDashboard(user.role);
        }, 1500);
        return false;
    }
    
    return true;
}

// Mock data functions (replace with actual API calls)
function getProductById(id) {
    const products = getMockProducts();
    return products.find(p => p.id === id);
}

function getMockProducts() {
    return [
        {
            id: '1',
            name: 'Fresh Tomatoes',
            price: 120,
            image: null,
            farmer: 'Ranjith Fernando',
            location: 'Matale',
            category: 'vegetables',
            description: 'Fresh organic tomatoes from Matale region'
        },
        {
            id: '2',
            name: 'Green Beans',
            price: 180,
            image: null,
            farmer: 'Kumari Silva',
            location: 'Kandy',
            category: 'vegetables',
            description: 'Premium quality green beans'
        },
        {
            id: '3',
            name: 'Red Rice',
            price: 95,
            image: null,
            farmer: 'Sunil Perera',
            location: 'Anuradhapura',
            category: 'grains',
            description: 'Traditional red rice variety'
        }
    ];
}

// Table sorting function
function sortTable(table, column) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const columnIndex = Array.from(table.querySelectorAll('th')).findIndex(th => th.getAttribute('data-sort') === column);
    
    if (columnIndex === -1) return;
    
    // Determine sort direction
    const currentDirection = table.getAttribute('data-sort-direction') || 'asc';
    const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
    table.setAttribute('data-sort-direction', newDirection);
    
    // Sort rows
    rows.sort((a, b) => {
        const aValue = a.cells[columnIndex].textContent.trim();
        const bValue = b.cells[columnIndex].textContent.trim();
        
        // Try to parse as numbers
        const aNum = parseFloat(aValue);
        const bNum = parseFloat(bValue);
        
        if (!isNaN(aNum) && !isNaN(bNum)) {
            return newDirection === 'asc' ? aNum - bNum : bNum - aNum;
        } else {
            return newDirection === 'asc' ? 
                aValue.localeCompare(bValue) : 
                bValue.localeCompare(aValue);
        }
    });
    
    // Re-append sorted rows
    rows.forEach(row => tbody.appendChild(row));
    
    // Update header indicators
    table.querySelectorAll('th').forEach(th => th.classList.remove('sorted-asc', 'sorted-desc'));
    table.querySelector(`th[data-sort="${column}"]`).classList.add(`sorted-${newDirection}`);
}

// Export functions for global access
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.updateCartQuantity = updateCartQuantity;
window.openModal = openModal;
window.closeModal = closeModal;
window.logout = logout;
window.showNotification = showNotification;
