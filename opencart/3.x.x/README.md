# Stable MCP Capabilities Documentation

This file describes all capabilities that the Stable module exposes to the MCP server.

## Where the functionality is defined

The `index()` function is located in:

- `catalog/controller/extension/module/stable.php`

It builds a JSON-RPC response describing the available tools for a selected working side (`frontend` or `backend`).

## What the `index()` function does

The function performs the following tasks:

- loads configuration from `system/config/stable.php`;
- merges module settings with the current store settings;
- determines the base URL of the MCP endpoint;
- collects the list of available tools depending on the enabled capabilities;
- returns a JSON response with tool metadata and server information.

## Response structure

The response contains:

- `jsonrpc`: protocol version, value `2.0`;
- `result.tools`: list of available MCP tools;
- `result.serverInfo`: server information (`php-mcp-server`, version `1.0.0`).

## Tool availability

The availability of tools depends on the `side` parameter and the module configuration:

- `frontend` — tools for the storefront;
- `backend` — tools for the admin side.

Each tool group can be enabled or disabled through the module settings.

## Available MCP tools

### 1. Product tools

A group of tools for working with products and categories.

- `getCategory`
  - Purpose: retrieve information about a product category.
  - Method: `GET`
  - Required parameters: `chat_id`, `category_id`

- `getCategories`
  - Purpose: retrieve a list of categories.
  - Method: `GET`
  - Required parameters: `chat_id`
  - Additional parameter: `parent_id`

- `getProduct`
  - Purpose: retrieve information about a product.
  - Method: `GET`
  - Required parameters: `chat_id`, `product_id`

- `getProducts`
  - Purpose: search for and retrieve a list of products.
  - Method: `GET`
  - Required parameters: `chat_id`
  - Additional parameters: `name`, `model`, `tag`, `description`, `category_id`, `page`

### 2. Customer tools

A group of tools for working with customers.

- `getCustomer`
  - Purpose: retrieve information about a customer.
  - Method: `GET`
  - Required parameters: `chat_id`, `customer_id`

- `getCustomers`
  - Purpose: search for and retrieve a list of customers.
  - Method: `GET`
  - Required parameters: `chat_id`
  - Additional parameters: `name`, `email`, `customer_group_id`, `page`

- `getCustomerGroups`
  - Purpose: retrieve a list of customer groups.
  - Method: `GET`
  - Required parameters: `chat_id`

### 3. Order tools

A group of tools for working with orders.

- `getOrder`
  - Purpose: retrieve information about an order.
  - Method: `GET`
  - Required parameters: `chat_id`, `order_id`

- `getOrders`
  - Purpose: search for and retrieve a list of orders.
  - Method: `GET`
  - Required parameters: `chat_id`
  - Additional parameters: `customer_name`, `order_status_id`, `date_added_from`, `date_added_to`, `page`

- `getOrderStatuses`
  - Purpose: retrieve a list of order statuses.
  - Method: `GET`
  - Required parameters: `chat_id`

### 4. Cart tools

A group of tools for working with the cart.

- `addProductInCart`
  - Purpose: add a product to the cart.
  - Method: `POST`
  - Required parameters: `chat_id`, `product_id`
  - Additional parameters: `quantity`, `option`, `recurring_id`

- `updateProductInCart`
  - Purpose: update the quantity of a product in the cart.
  - Method: `PATCH`
  - Required parameters: `chat_id`, `product_id`, `quantity`

- `deleteProductInCart`
  - Purpose: remove a product from the cart.
  - Method: `DELETE`
  - Required parameters: `chat_id`, `cart_id`

- `getProductsInCart`
  - Purpose: retrieve the list of products in the cart.
  - Method: `GET`
  - Required parameters: `chat_id`, `cart_id`

### 5. Checkout tools

A group of tools for placing orders.

- `createOrder`
  - Purpose: create an order.
  - Method: `POST`
  - Required parameters: `chat_id`, `shipping_method_code`, `payment_method_code`
  - Additional parameters: first name, last name, email, phone, address, country, region, payment data, and more.

- `getCountries`
  - Purpose: retrieve a list of countries.
  - Method: `GET`
  - Required parameters: `chat_id`

- `getZonesByCountryId`
  - Purpose: retrieve a list of zones for a country ID.
  - Method: `GET`
  - Required parameters: `chat_id`, `country_id`

- `getShippingMethods`
  - Purpose: retrieve available shipping methods.
  - Method: `GET`
  - Required parameters: `chat_id`, `country_id`, `zone_id`

- `getPaymentMethods`
  - Purpose: retrieve available payment methods.
  - Method: `GET`
  - Required parameters: `chat_id`, `country_id`, `zone_id`

## General note

The `index()` function does not perform the business operations itself; it only describes the available MCP tools and their parameters. The actual request handling is done in other controller methods such as `getProduct`, `createOrder`, `addProductInCart`, and others.
