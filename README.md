# Stable Module Documentation

The Stable module exposes a set of HTTP tool endpoints for interacting with an OpenCart store through two controller groups:

- Frontend endpoints for storefront operations
- Backend endpoints for admin-side catalog, customer, and order data

## Overview

The module lets an external agent query and manipulate store data over plain HTTP, using JSON-RPC 2.0 shaped responses. Each tool is exposed through a dedicated controller action and returns either a structured result or an error.

**This is not an MCP server.** The endpoints are ordinary HTTP handlers, and the tool catalogue in `index()` is a convenience descriptor, not a conformant MCP `tools/list`. An agent calls these endpoints with a generic HTTP tool; nothing registers them as MCP tools. Keep this distinction in mind when writing agent-facing documentation — describing them as "MCP tools" leads an agent to look for them in its tool list, where they do not exist.

The module supports the following high-level areas:

- Product and category browsing
- Customer lookup and profile access
- Order and order status retrieval
- Shopping cart management
- Checkout and payment/shipping information
- Country and zone lookup

## Integration and bootstrap

`catalog/controller/extension/module/stable.php` runs on the `content_top_before` event and only for a logged-in customer. `admin/controller/extension/module/stable.php` does the equivalent for the admin panel.

On each page load it:

1. Looks up (or creates) the chat record for the customer in `stable_chat`.
2. **Refreshes the stored `session_id`** with the current storefront session via `editChat()`.
3. Requests a Ranch embed token.
4. Sets four short-lived cookies and enqueues the widget script.

| Cookie | Contents |
|---|---|
| `stable_api_url` | Base URL of this store's Stable endpoint group (frontend or backend) |
| `stable_chat_id` | Chat identifier for this customer |
| `stable_agent_id` | Ranch agent id from module settings |
| `stable_token` | Ranch embed token |

`view/javascript/stable/stable.js` reads the cookies and passes two values to the agent through the SDK's `data-prompt` attribute:

```
Stable API URL: <base url>
Chat ID: <chat id>
```

These labels are the agent's only source for both values, so they must match the placeholders used in the agent-facing documentation (`<STABLE_API_URL>`, `Chat ID`) exactly.

Note that the cookies are set with a 60-second lifetime while the Ranch token is issued for a day. They are read once on `window load`; a widget re-initialisation later than that reads `null`.

## Transport

**Every endpoint is `POST` with a JSON body.** There are no `GET` endpoints and no other HTTP methods; request-method validation was removed from the handlers.

```
POST <base url>/<tool>
Content-Type: application/json
{"chat_id": "...", ...tool arguments...}
```

Arguments are read by the shared `getRequestData()` helper, which is deliberately tolerant. It merges, in descending priority:

1. The JSON body (`php://input`)
2. Form-encoded POST fields
3. Query-string parameters

OpenCart's own `route` parameter is stripped. A caller that puts arguments in the query string still works, but the JSON body is the documented form.

## Response format

**Success — HTTP 200:**

```json
{ "jsonrpc": "2.0", "result": { ... } }
```

**Failure — HTTP 400:**

```json
{
  "jsonrpc": "2.0",
  "error": "Chat ID required! Product ID required!",
  "errors": ["Chat ID required!", "Product ID required!"]
}
```

- `result` and `error` are mutually exclusive.
- All validation problems are collected and reported in one response, rather than one per call.
- `error` is every problem joined into a single string; `errors` is the same set as an array.
- Failures return HTTP status **400**, not 200. Success stays 200.

## Common behavior

All tool endpoints share the same pattern:

1. A request arrives with a `chat_id` and relevant arguments.
2. Required input is validated; every missing field is collected.
3. The chat record is resolved, and the caller is checked against the configured tool permissions.
4. The storefront (or admin) session is restored from the chat record.
5. The relevant model layer is called to retrieve or modify data.
6. The result is returned in JSON-RPC shape, with HTTP 400 on failure.

The module also records chat actions and request logs for traceability.

## Session handling

Write operations — cart changes and order creation — need the customer's real session, not the one the API request arrives on. `refreshStartup($chat)` rebuilds it:

- starts the PHP session stored in `stable_chat.session_id`
- re-creates `customer`, `currency`, `tax`, `weight`, `length` and `cart` in the registry against that session

This makes two things load-bearing:

- **`stable_chat.session_id` must stay current.** The bootstrap controller refreshes it on every page load. If that update is skipped, the stored id eventually points at an expired session: `Cart\Customer` then finds no logged-in customer, `getCurrentCustomer` fails with `Current customer not found!`, and cart writes silently land in a guest-scoped cart that the customer never sees on the storefront.
- **Cart operations can appear to succeed without a logged-in customer.** `Cart\Cart` is scoped by `customer_id` + `session_id`, so with `customer_id = 0` an add still returns a consistent cart. A successful cart response is therefore not by itself proof that the session is healthy.

## Tool permissions

Every tool is gated by one of five permission groups, configured per store under `setting.side.<side>.tool.<group>.status`:

| Side | Groups |
|---|---|
| Frontend | `product`, `customer`, `cart`, `checkout` |
| Backend | `product`, `customer`, `order` |

`getCountries` and `getZonesByCountryId` are ungated on both sides. A disabled group makes its tools return `You do not have permission to use this tool!`.

## Pagination and ordering

- All search tools return **20 records per page**; use `page` to walk larger result sets.
- Order lists are sorted **newest first** (`order_id` descending) on both the frontend and the backend, so the most recent order is the first element of page 1.
- `getCurrentCustomerOrder` / `getCurrentCustomerOrders` only return orders with `order_status_id > 0`, scoped to the current customer and store.
- Keep responses modest: a generic HTTP tool on the agent side typically truncates large bodies, and a truncated response still arrives with status 200.

## Backend functionality

The backend controller is intended for admin-side operations and exposes read-only tools for catalog, customer, and order data. No backend tool creates or modifies a record.

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
  - Returns newest orders first.
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
  - Fails with `Current customer not found!` when the restored session has no logged-in customer — see [Session handling](#session-handling).

- `getCurrentCustomerOrder`
  - Returns a specific order belonging to the current customer.
  - Requires: `chat_id`, `order_id`

- `getCurrentCustomerOrders`
  - Returns the current customer's order history, newest first.
  - Requires: `chat_id`
  - Optional: `page`

### Cart tools

Each cart tool returns the **full updated cart** (`{"products": [...]}`), so the response itself is the confirmation that the change took effect.

- `addCartProduct`
  - Adds a product to the customer cart.
  - Requires: `chat_id`, `product_id`
  - Optional: `quantity` (default `1`), `option`, `recurring_id`
  - Required product options are validated: a missing one fails with `<Option name> required!`. A product with recurring profiles needs a valid `recurring_id`.

- `editCartProduct`
  - Updates the quantity of a product already in the cart.
  - Requires: `chat_id`, `cart_id`, `quantity`
  - Keys off the cart line (`cart_id`), not `product_id`.

- `deleteCartProduct`
  - Removes a product from the cart.
  - Requires: `chat_id`, `cart_id`

- `getCartProducts`
  - Returns the contents of the cart.
  - Requires: `chat_id`

### Checkout and delivery tools

- `createOrder`
  - Creates a new order from the current cart and customer data, then clears the cart.
  - Unconditionally requires: `chat_id`, `payment_method_code`
  - **Auto-filled when omitted** from the customer's profile and default address: `firstname`, `lastname`, `email`, `address_1`, `city`, `postcode`, `country_id`, `zone_id`. They only fail if still missing afterwards.
  - **`shipping_method_code`** is required only when the cart contains a shippable product (`cart->hasShipping()`); omit it for digital-only carts.
  - **Card fields** — `cc_owner`, `cc_number`, `cc_expire_date_month`, `cc_expire_date_year`, `cc_cvv2` — are required if and only if `payment_method_code` is `authorizenet_aim`. All five are declared as **strings** in the schema and must be sent as strings: as numbers, `"01"` becomes `1` and `"045"` becomes `45`, and long card numbers lose precision. The conditional requirement is expressed in the descriptor with JSON Schema `allOf` / `if` / `then`.
  - Method codes must be passed through verbatim from `getShippingMethods` / `getPaymentMethods`, never constructed.
  - Pre-flight checks: the cart must not be empty, items must be in stock, and quantities must meet each product's `minimum`.
  - The handler copies the decoded body into `$this->request->post` so the payment extension (which reads from there) receives the card fields.

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

- The tool catalogue is declared in the `index()` action; the actual logic lives in dedicated methods such as `getProduct`, `getProducts`, `createOrder`, and the cart actions.
- Descriptors carry `endpoint` and `requestMethod` alongside the argument schema. These are extensions for the module's own client and are not part of the MCP contract.
- All requests need a valid `chat_id` and the appropriate permission group.
- Every request and response is written to the module log, which is the primary tool for diagnosing agent behaviour: it shows the arguments that actually arrived and the exact error returned.
