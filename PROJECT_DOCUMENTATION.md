# Project Documentation

## 1. Project Overview
This project is a multi-vendor e-commerce platform that allows customers to browse products from various vendors, add them to a cart, and purchase them. It supports vendor management, order tracking, and dispute resolution. The application is built using PHP (native) with a MySQL database.

## 2. Directory Structure

- **`actions/`**: Contains PHP scripts that handle form submissions and business logic (e.g., login, registration, adding to cart, processing payments).
- **`admin/`**: Admin-specific pages and scripts.
- **`classes/`**: PHP classes representing the data models and core logic (e.g., `user_class.php`, `product_class.php`, `order_class.php`).
- **`controllers/`**: Controller functions that act as intermediaries between the views and the classes.
- **`css/`**: Stylesheets for the application.
- **`js/`**: JavaScript files for frontend interactivity.
- **`settings/`**: Configuration files, including database connection (`db_class.php`, `db_cred.php`) and SQL schema (`preorda.sql`).
- **`view/`**: The user-facing HTML/PHP pages (e.g., `products.php`, `cart.php`, `checkout.php`).
- **`uploads/`**: Directory for storing uploaded files (e.g., product images).

## 3. Database Schema

The database consists of the following key tables:

### Users & Roles
- **`users`**: Stores core user information (name, email, phone, password hash, user type).
- **`customers`**: Extends `users` for customer-specific data.
- **`vendors`**: Extends `users` for vendor-specific data (business name, registration number, verification status).
- **`admins`**: Extends `users` for administrative roles.

### Products & Inventory
- **`products`**: Stores product details (name, price, description, stock status) linked to a vendor.
- **`categories`**: Product categories.
- **`brands`**: Product brands.

### Orders & Payments
- **`carts`** & **`cart_items`**: Manages temporary shopping cart data for customers.
- **`orders`**: Stores order information. **Note**: Orders are split by vendor.
- **`order_details`**: Links products to orders with quantity and price.
- **`payments`**: Records payment transactions linked to orders.
- **`invoices`**: Generated invoices for orders.

### Logistics & Support
- **`logistics_partners`**: Shipping partners.
- **`order_shipments`**: Tracking information for shipped orders.
- **`support_tickets`** & **`chats`**: Customer support system.
- **`audits`**: System audit logs.

## 4. User Roles & Permissions

### Customer
- Can browse products, add to cart, and checkout.
- Can view order history and track shipments.
- Can file disputes and open support tickets.
- Can update personal profile.

### Vendor
- Can manage their own profile and business details.
- Can add, edit, and delete their products.
- Can view orders specific to their products.
- Can update order status (e.g., confirm, ship).

### Admin
- **Super Admin**: Full access to the system.
- **Support Admin**: Focuses on resolving tickets and disputes.
- Can manage users, vendors, and categories.
- Can view system-wide audits and reports.

## 5. Key Workflows

### User Registration & Login
1.  **Registration**: Users sign up via the registration form. The system checks for duplicate emails.
2.  **Login**: Users authenticate with email/phone and password.
3.  **Session**: Upon login, session variables (`user_id`, `user_type`, `role`) are set.

### Vendor Onboarding
1.  **Application**: A registered user can apply to become a vendor by providing business details.
2.  **Verification**: Admins review vendor applications.
3.  **Approval**: Once approved (`verified = 1`), the user gains access to vendor features.

### Product Management
1.  **Creation**: Vendors upload product details and images. Images are stored in the `uploads/` directory.
2.  **Display**: Products are listed on the main shop page (`view/products.php`) and detailed views.
3.  **Inventory**: Stock status is tracked (`available` vs `out_of_stock`).

### Shopping & Checkout
1.  **Cart**: Users add items to their cart. Cart data is stored in the database (`carts` table) for persistence.
2.  **Checkout**:
    - Users provide shipping and billing details.
    - **Order Splitting**: If the cart contains items from multiple vendors, the system automatically splits the order into separate sub-orders, one for each vendor.
    - **Guest Checkout**: Supported. The system creates a temporary user account for guests behind the scenes.
3.  **Payment**: Payments are processed (simulated or via gateway like Paystack) and recorded in the `payments` table.

### Order Fulfillment
1.  **Notification**: Vendors receive notifications of new orders.
2.  **Processing**: Vendors update order status (Pending -> Confirmed -> Shipped).
3.  **Tracking**: Shipping details and tracking numbers are added to `order_shipments`.
4.  **Delivery**: Customers can track the status via `view/track.php`.

### Dispute Resolution
1.  **Filing**: Customers can file a dispute for a specific order if issues arise.
2.  **Mediation**: Admins review the dispute and facilitate communication between customer and vendor via the `chats` system.
3.  **Resolution**: Admins mark the dispute as resolved once a conclusion is reached.
