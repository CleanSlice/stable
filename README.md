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

The `product` group covers the manufacturer tools too — `getManufacturer` and
`getManufacturers` are catalog lookups and are gated with the categories and products.

`getCountries` and `getZonesByCountryId` are ungated on both sides. A disabled group makes its tools return `You do not have permission to use this tool!`.

## Filtering, sorting and pagination

Every plural tool takes filters plus `sort`, `order` and `page`, and applies all three
server-side. The agent is not expected to fetch broadly and re-order the result itself.

- **Page size is 20** for every search tool. Keep it modest: a generic HTTP tool on the
  agent side typically truncates large bodies, and a truncated response still arrives with
  status 200, so the agent cannot tell it was cut.
- `order` accepts `ASC` / `DESC`. Default is `ASC` everywhere except `getOrders`, which
  defaults to `DESC` so the newest order is the first element of page 1.
- `sort` accepts a fixed key set per tool, mapped in the model's `$sort_data`. An
  unrecognised key silently falls back to the default — the handler does not reject it.

| Tool | `sort` keys |
|---|---|
| `getCategories` | `name`, `sort_order` (backend also `status`) |
| `getManufacturers` | `name`, `sort_order` |
| `getProducts` | `name`, `model`, `price`, `quantity`, `sort_order`, `date_added`, `manufacturer`, `rating` (backend also `status`) |
| `getCustomers` (backend) | `name`, `email`, `customer_group`, `status`, `date_added` |
| `getOrders` (backend) | `order_id`, `customer_name`, `order_status`, `total`, `date_added` |

`manufacturer` and `rating` sort on SELECT-list aliases, which is legal in `ORDER BY` but
**not** in `WHERE` — the same reason the price filter cannot reuse the `special` / `discount`
aliases (see below).

### Effective price — frontend only

`getProducts` on the frontend filters and sorts by the price **the customer actually sees**,
not the stored base price. `getPriceExpression()` and `getDisplayPriceExpression()` in the
frontend model build one SQL expression applying, in order:

1. active special, else quantity-1 discount, else `p.price` — via `COALESCE`, with the
   subqueries repeated inline because SELECT aliases are invisible to `WHERE`;
2. tax for the product's `tax_class_id`, as `value * k + c`. Both coefficients are read off
   the live `Cart\Tax` object with two probes (`calculate(0)` and `calculate(100)`) rather
   than re-implementing the rate rules — `Tax::calculate()` is linear in value, so this is
   exact and can never drift from what the display code produces;
3. the session currency rate.

The same expression backs `getProducts`, `getTotalProducts` **and** the price sort, so the
returned rows, `productCount` and the ordering can never disagree. Because of that, the
controller passes `price_min` / `price_max` through **verbatim** — converting them again
would apply the currency rate twice.

The backend deliberately filters on the raw `p.price`: no tax, no conversion. Admin-side
answers are about stored data, not shopfront presentation.

### Order status

- `getCurrentCustomerOrder` / `getCurrentCustomerOrders` (frontend) only return orders with
  `order_status_id > 0`, scoped to the current customer and store, ordered newest first.
  This tool takes no `sort` / `order`.
- `getOrders` (backend) defaults to `order_status_id > 0` as well, but **`order_status_id: 0`
  is a valid filter value** and returns the unconfirmed ("missing", abandoned) orders. Both
  the handler and the model guard with `!== ''` rather than `!empty()`, precisely so that a
  zero survives. `getOrderStatuses` does not list `0` — status 0 is the absence of a status,
  and it has no row in the `order_status` table, so such orders come back with
  `order_status` set to `null`.

## Backend functionality

The backend controller is intended for admin-side operations and exposes read-only tools for catalog, customer, and order data. No backend tool creates or modifies a record.

### Product and category tools

- `getCategory`
  - Returns detailed information about a single category.
  - Requires: `chat_id`, `category_id`

- `getCategories`
  - Returns one level of the category tree.
  - Requires: `chat_id`
  - Optional: `name`, `parent_category_id`, `status`, `sort`, `order`, `page`

- `getManufacturer`
  - Returns a single manufacturer: `manufacturer_id`, `name`, `image`, `sort_order`.
  - Requires: `chat_id`, `manufacturer_id`

- `getManufacturers`
  - Searches manufacturers, so a brand name can be resolved to an id for `getProducts`.
  - Requires: `chat_id`
  - Optional: `name`, `sort`, `order`, `page`

- `getProduct`
  - Returns detailed information about a single product.
  - Requires: `chat_id`, `product_id`

- `getProducts`
  - Searches products with filters, sorting and paging.
  - Requires: `chat_id`
  - Optional: `name`, `model`, `category_id`, `manufacturer_id`, `price_min`, `price_max`, `quantity_min`, `quantity_max`, `status`, `date_added_from`, `date_added_to`, `sort`, `order`, `page`
  - `status` has **no default**: omit it and both enabled and disabled products are returned.
  - `tag` and `description` are no longer searchable — the filters were removed from the models.

### Customer tools

- `getCustomer`
  - Returns details for a specific customer.
  - Requires: `chat_id`, `customer_id`

- `getCustomers`
  - Searches customers using name, email, customer group, or pagination.
  - Requires: `chat_id`
  - Optional: `name`, `email`, `customer_group_id`, `status`, `date_added_from`, `date_added_to`, `sort`, `order`, `page`

- `getCustomerGroups`
  - Returns the list of available customer groups.
  - Requires: `chat_id`

### Order tools

- `getOrder`
  - Returns details of a single order.
  - Requires: `chat_id`, `order_id`

- `getOrders`
  - Searches orders by customer name, order status, date range, or pagination.
  - Defaults to newest first; pass `sort` / `order` to change that, and `order_status_id: 0` for unconfirmed orders.
  - Requires: `chat_id`
  - Optional: `customer_name`, `order_status_id`, `total_min`, `total_max`, `date_added_from`, `date_added_to`, `sort`, `order`, `page`

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
  - Returns one level of the storefront category tree. Only active categories in this store.
  - Requires: `chat_id`
  - Optional: `name`, `parent_category_id`, `sort`, `order`, `page`

- `getManufacturer`
  - Returns a single manufacturer: `manufacturer_id`, `name`, `image`, `sort_order`.
  - Requires: `chat_id`, `manufacturer_id`

- `getManufacturers`
  - Searches manufacturers, so a brand the customer names can be resolved to an id.
  - Requires: `chat_id`
  - Optional: `name`, `sort`, `order`, `page`

- `getProduct`
  - Returns information about a product from the storefront catalog.
  - Requires: `chat_id`, `product_id`

- `getProducts`
  - Searches products with filters, sorting and paging. Only active, in-store products.
  - Requires: `chat_id`
  - Optional: `name`, `model`, `category_id`, `manufacturer_id`, `price_min`, `price_max`, `quantity_min`, `quantity_max`, `date_added_from`, `date_added_to`, `sort`, `order`, `page`
  - `price_min` / `price_max` are interpreted as the **customer-visible** price — tax applied, session currency — see [Effective price](#effective-price--frontend-only). Pass them through unconverted.
  - No `status` filter here: the storefront query is hard-wired to `p.status = '1'`.
  - `tag` and `description` are no longer searchable — the filters were removed from the models.

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
  - **Payment-specific fields** are declared per method in the config registry (see [Payment methods](#payment-methods)), not hard-coded. `index()` generates their `properties` and one `allOf` / `if` / `then` branch per method from that registry, and the handler validates against the same data. All such fields are declared as **strings**: as numbers, `"01"` becomes `1` and `"045"` becomes `45`, and long card numbers lose precision.
  - Method codes must be passed through verbatim from `getShippingMethods` / `getPaymentMethods`, never constructed.
  - Pre-flight checks: the cart must not be empty, items must be in stock, and quantities must meet each product's `minimum`.
  - The handler copies the decoded body into `$this->request->post` so the payment extension (which reads from there) receives the payment fields.

- `getShippingMethods`
  - Returns shipping methods available for a given country and zone.
  - Requires: `chat_id`, `country_id`, `zone_id`

- `getPaymentMethods`
  - Returns payment methods available for a given country and zone, each annotated with what the module can do with it.
  - Requires: `chat_id`, `country_id`, `zone_id`
  - Each entry carries `code`, `title`, `flow`, and either `required_fields` + `optional_fields` or, for `flow: unsupported`, a `reason`.

### Geography tools

- `getCountries`
  - Returns available countries for checkout and address entry.
  - Requires: `chat_id`

- `getZonesByCountryId`
  - Returns zones for a specific country during checkout.
  - Requires: `chat_id`, `country_id`

## Payment methods

OpenCart ships 54 payment extensions and they are not interchangeable. What a method needs
is visible in its controller's public methods:

| Class | Signature | Can the module complete it? |
|---|---|---|
| Offline | `index`, `confirm` | **Yes** — `confirm()` sets the order status server-side |
| Direct API | `index`, `send` | **Yes**, once the extra fields are collected |
| API + 3-D Secure | `send` plus `acsReturn` / `threeDSecureCallback` / `tds` | No — the challenge needs the customer's browser |
| Redirect / IPN | `index`, `callback` / `notify` / `ipn` | No — the customer must be sent to the gateway |
| JS SDK | `express`, `nonce`, `createOrder` … | No — needs a script running in the browser |

Only the first two classes can be driven headlessly, so the practical ceiling is roughly a
dozen methods, not all 54. That is a property of the payment flows, not a limitation to be
engineered away.

### The registry

Supported methods are declared in `system/config/stable.php` under `payment_method`. Nothing
about a specific gateway lives in the controller.

```php
'cod' => array(
    'code'  => 'cod',
    'flow'  => 'confirm',
    'field' => array()
),
'authorizenet_aim' => array(
    'code'  => 'authorizenet_aim',
    'flow'  => 'send',
    'field' => array(
        'cc_number' => array(
            'code' => 'cc_number', 'type' => 'string', 'required' => true,
            'name' => 'Card Number', 'description' => '...'
        ),
        // ...
    )
),
```

- **`flow`** is the controller method to call — `confirm` or `send`. The dispatcher is
  generic: `load->controller('extension/payment/' . $code . '/' . $flow)`.
- **`field`** declares every field the method understands. `required => true` fields are
  validated by `createOrder` and land in that method's `allOf` / `then` branch;
  `required => false` fields are declared in `properties` and advertised as
  `optional_fields`, but never enforced.
- A method **absent from the registry** is reported as `flow: unsupported` by
  `getPaymentMethods`, and `createOrder` refuses it with HTTP 400 **before** creating
  anything.

Currently registered: `cod`, `bank_transfer`, `cheque`, `free_checkout` (offline);
`authorizenet_aim`, `sagepay_us`, `web_payment_software`, `perpetual_payments`,
`firstdata_remote` (`send`).

Field sets genuinely differ between these — `web_payment_software` never reads a cardholder
name, `firstdata_remote` calls it `cc_name` and reads no expiry date at all,
`perpetual_payments` wants an optional card start date. Nothing may assume a fixed "card
field set".

### One rule when editing the registry

Field descriptions are written into `createOrder`'s `inputSchema` keyed by **field code**, so
a field shared between methods keeps only the **last** description declared. Descriptions for
the same code must therefore stay identical and must never name one method — which method
requires what is expressed by the generated `allOf` branches, not by the prose.

### Confirming the outcome

Response shapes are inconsistent across extensions: `cod` / `bank_transfer` / `cheque` set
`json['redirect']`, `authorizenet_aim` sets `redirect` or `error`, some gateways use
`json['success']`, and **`free_checkout` outputs nothing at all on success**. So "empty
output" means success for one method and failure for the rest — the response cannot decide it.

`createOrder` therefore ignores the response shape and checks the database:

1. `json['error']` present → surface the gateway's own message. It is the only place the real
   reason (a decline, a bad card) exists.
2. Otherwise re-read the order: `order_status_id > 0` means confirmed, because every
   extension calls `addOrderHistory()` on success and that is what lifts the order off
   status 0.
3. Neither → the order was created but never confirmed. Report failure and **leave the cart
   intact**.

The cart is cleared only inside the verified-success branch. A row left at status 0 is a
missing order — the same thing the backend's `order_status_id: 0` filter finds.

## Notes

- The tool catalogue is declared in the `index()` action; the actual logic lives in dedicated methods such as `getProduct`, `getProducts`, `createOrder`, and the cart actions.
- Descriptors carry `endpoint` and `requestMethod` alongside the argument schema. These are extensions for the module's own client and are not part of the MCP contract.
- All requests need a valid `chat_id` and the appropriate permission group.
- Every request and response is written to the module log, which is the primary tool for diagnosing agent behaviour: it shows the arguments that actually arrived and the exact error returned.
