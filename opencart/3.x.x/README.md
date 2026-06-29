# Stable Module Documentation

The Stable module exposes a set of MCP-compatible tools for interacting with an OpenCart store through two controller groups:

- Frontend endpoints for storefront operations
- Backend endpoints for admin-side catalog, customer, and order data

## Overview

The module is designed to let an external MCP-compatible client query and manipulate store data using JSON-RPC 2.0 responses. Each tool is exposed through a dedicated controller action and returns either a structured result or an error message.

The module supports the following high-level areas:

- Product and category browsing
- Customer lookup and profile access
- Order and order status retrieval
- Shopping cart management
- Checkout and payment/shipping information
- Country and zone lookup

## Common behavior

All tool endpoints share the same general pattern:

1. A request arrives with a `chat_id` and relevant parameters.
2. The controller validates the request method and required input.
3. The chat is checked for existence and the caller is validated against the configured tool permissions.
4. The relevant model layer is called to retrieve or modify data.
5. The result is returned in JSON-RPC format.

The module also records chat actions and request logs for traceability.

## Backend functionality

The backend controller is intended for admin-side operations and exposes tools for catalog, customer, and order management.

### Product and category tools

- `getCategory`
  - Returns detailed information about a single category.
  - Requires: `chat_id`, `category_id`

- `getCategories`
  - Returns a list of categories.
  - Requires: `chat_id`
  - Optional: `parent_id`

- `getProduct`
  - Returns detailed information about a single product.
  - Requires: `chat_id`, `product_id`

- `getProducts`
  - Searches and returns products by name, model, tag, description, category, or page.
  - Requires: `chat_id`
  - Optional: `name`, `model`, `tag`, `description`, `category_id`, `page`

### Customer tools

- `getCustomer`
  - Returns details for a specific customer.
  - Requires: `chat_id`, `customer_id`

- `getCustomers`
  - Searches customers using name, email, customer group, or pagination.
  - Requires: `chat_id`
  - Optional: `name`, `email`, `customer_group_id`, `page`

- `getCustomerGroups`
  - Returns the list of available customer groups.
  - Requires: `chat_id`

### Order tools

- `getOrder`
  - Returns details of a single order.
  - Requires: `chat_id`, `order_id`

- `getOrders`
  - Searches orders by customer name, order status, date range, or pagination.
  - Requires: `chat_id`
  - Optional: `customer_name`, `order_status_id`, `date_added_from`, `date_added_to`, `page`

- `getOrderStatuses`
  - Returns the list of order statuses.
  - Requires: `chat_id`

### Geography tools

- `getCountries`
  - Returns available countries.
  - Requires: `chat_id`

- `getZonesByCountryId`
  - Returns zones for a specific country.
  - Requires: `chat_id`, `country_id`

## Frontend functionality

The frontend controller is intended for storefront interactions and exposes tools that support customer-facing shopping scenarios.

### Product and category tools

- `getCategory`
  - Returns information about a category in the storefront catalog.
  - Requires: `chat_id`, `category_id`

- `getCategories`
  - Returns category lists for the storefront.
  - Requires: `chat_id`
  - Optional: `parent_id`

- `getProduct`
  - Returns information about a product from the storefront catalog.
  - Requires: `chat_id`, `product_id`

- `getProducts`
  - Searches products in the storefront catalog.
  - Requires: `chat_id`
  - Optional: `name`, `model`, `tag`, `description`, `category_id`, `page`

### Customer profile tools

- `getCurrentCustomer`
  - Returns information about the current customer session.
  - Requires: `chat_id`

- `getCurrentCustomerOrder`
  - Returns a specific order belonging to the current customer.
  - Requires: `chat_id`, `order_id`

- `getCurrentCustomerOrders`
  - Returns the current customer’s order history.
  - Requires: `chat_id`
  - Optional: `page`

### Cart tools

- `addCartProduct`
  - Adds a product to the customer cart.
  - Requires: `chat_id`, `product_id`
  - Optional: `quantity`, `option`, `recurring_id`

- `editCartProduct`
  - Updates the quantity of a product already in the cart.
  - Requires: `chat_id`, `cart_id`, `quantity`

- `deleteCartProduct`
  - Removes a product from the cart.
  - Requires: `chat_id`, `cart_id`

- `getCartProducts`
  - Returns the contents of the cart.
  - Requires: `chat_id`, `cart_id`

### Checkout and delivery tools

- `createOrder`
  - Creates a new order from the current cart and customer data.
  - Requires: `chat_id` and checkout-related details such as shipping and payment method codes.
  - Supports customer address, payment data, and shipping configuration fields.

- `getShippingMethods`
  - Returns shipping methods available for a given country and zone.
  - Requires: `chat_id`, `country_id`, `zone_id`

- `getPaymentMethods`
  - Returns payment methods available for a given country and zone.
  - Requires: `chat_id`, `country_id`, `zone_id`
  
### Geography tools

- `getCountries`
  - Returns available countries for checkout and address entry.
  - Requires: `chat_id`

- `getZonesByCountryId`
  - Returns zones for a specific country during checkout.
  - Requires: `chat_id`, `country_id`

## Notes

- The module exposes functionality through tool metadata in the `index()` action and executes the actual logic in dedicated methods such as `getProduct`, `getProducts`, `createOrder`, and cart-related actions.
- All requests are expected to be made with a valid `chat_id` and the appropriate permissions.
- The module is suitable for integration with MCP clients that need read and write access to store data in a controlled, structured way.

