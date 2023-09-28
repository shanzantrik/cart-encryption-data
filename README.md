# Cart Encryption Data Plugin
## Plugin Name: Cart Encryption Data Plugin
**Description: A WordPress plugin for saving cart information, generate encrypted links, and allow for checkout.**
**Version: 1.0**
**Author: Shantanu Goswami**

## Instructions:

After downloading or cloning the plugin you need to do install the plugin in wordpress.

## Installation

To install the Cart Encryption Data Plugin, follow these steps:

1. **Download the plugin files.**
   - Click on the "Code" button in the GitHub repository.
   - Select "Download ZIP" to download the plugin as a ZIP archive.

2. **Upload the plugin to your WordPress site.**
   - Extract the downloaded ZIP archive.
   - Upload the entire `cart-encryption-plugin` directory to your WordPress plugins directory, typically located at `wp-content/plugins/`.

3. **Activate the plugin.**
   - Go to the WordPress admin panel.
   - Navigate to "Plugins" and find "Cart Encryption Data Plugin."
   - Click "Activate" to enable the plugin.

## Usage

Once the Cart Encryption Data Plugin is installed and activated, it provides the following functionality:

### Saving Cart Information

- When a user adds items to their cart, the plugin automatically saves the cart contents for logged-in users.

### Generating Encrypted Links

- The plugin generates encrypted links that allow users to access their saved carts securely.

### Sending Encrypted Cart Links via Email

- During order processing, the plugin sends emails to customers containing encrypted cart links.
- These emails include a link to the customer's saved cart, enabling them to easily complete their purchase.

### Handling Checkout from Encrypted Links

- When a customer clicks on an encrypted cart link, the plugin decrypts the cart data and adds the items to the WooCommerce cart.
- Customers are then redirected to the checkout page for a seamless checkout experience.


