---
name: stable-opencart4-backend
description: The only way to read this OpenCart store's admin data — look up categories, manufacturers, products, customers, customer groups, orders, order statuses, countries and zones. Every search tool filters, sorts and pages server-side — by name, model, brand, category, price, stock, status, email, customer group, order status, order total or date range. Read-only — no tool here creates or changes a record. Every tool is POST with a JSON body to the Stable API URL from the integrator context; admin pages, route= URLs and SQL are not alternatives and do not work. Load before answering anything about the catalog, customers, or orders.
metadata:
  always: true
---

# Stable OpenCart Backend

Comprehensive skill for looking up categories, products, customers, and orders in OpenCart using the Stable module's backend HTTP API.

⚠️ **IMPORTANT — this is not an MCP server.** These are plain HTTP endpoints, called with the built-in **`http`** tool (see [How to call these tools](#how-to-call-these-tools--use-the-http-tool)). No MCP server in your tool list can reach this store: a tool named `search`, `query_knowledge` or anything similar belongs to something unrelated and knows nothing about this store's data. Take the **Chat ID** and **Stable API URL** from the integrator context (the embed's `data-prompt` attribute) — they are the only valid coordinates for this store. Never fetch OpenCart admin pages directly.

---

## API Endpoints

**Base:** `<STABLE_API_URL>/<TOOL>`

### Transport — the same for every tool

**Every tool is `POST` with a JSON body.** There are no `GET` tools, no query-string
parameters, and no other HTTP methods. If you find yourself building a URL with `?` and
`&`, you are calling it wrong.

```
POST <STABLE_API_URL>/<TOOL>
Content-Type: application/json
{"chat_id": "<CHAT_ID>", ...tool arguments...}
```

All arguments — including `chat_id` — go in the body as one flat JSON object.

### How to call these tools — use the `http` tool

The blocks above are HTTP sketches, not tool calls. Each one is executed with the built-in
**`http`** tool. Nothing else reaches the store — not `browser`, not `exec`, and no MCP
server other than this API.

`http` takes exactly four parameters:

| Param | What to pass |
|---|---|
| `url` | `<STABLE_API_URL>` + `/` + the tool name. Must be absolute — starts with `https://`. |
| `method` | `"POST"` — always, for every tool here. |
| `headers` | `{"Content-Type": "application/json"}` |
| `body` | The arguments object **serialized to a JSON string** — not an object. |

Worked example. To run the `getOrders` sketch, call `http` with:

```json
{
  "url": "<STABLE_API_URL>/getOrders",
  "method": "POST",
  "headers": {"Content-Type": "application/json"},
  "body": "{\"chat_id\":\"<CHAT_ID>\",\"customer_name\":\"Jane Doe\",\"page\":1}"
}
```

Three mistakes that kill the call before it ever leaves the runtime:

- **`body` passed as an object.** It is a string parameter. Serialize it. A nested object
  is rejected outright, and the rejection is not a store error — the request never went.
- **A relative `url`.** `/index.php?route=...` is not accepted. Use the full `<STABLE_API_URL>`
  from the integrator context.
- **Reaching for a different tool** because this one feels indirect. A tool named `search`,
  `query_knowledge` or anything similar belongs to an unrelated server and knows nothing
  about this store's data. There is no shortcut and no second route.

**Keep responses small.** `http` truncates the response body at 50,000 characters and times
out after 30 seconds. A broad `getProducts`, `getCustomers` or `getOrders` call easily
exceeds that and comes back as JSON cut off mid-structure — with `status` still `200`.
Always narrow the query with the available filters, then use the singular tool
(`getProduct`, `getCustomer`, `getOrder`) for full detail on one record.

### Response format

`http` returns a wrapper, not the store's reply directly:

```json
{"status": 200, "body": "<the response text>"}
```

Read `status` first — that is how you tell success from failure. Then parse `body`, which
is a **string** containing the JSON envelope below.

**Success — HTTP 200:**
```json
{ "jsonrpc": "2.0", "result": { ... } }
```

**Failure — HTTP 400:**
```json
{
  "jsonrpc": "2.0",
  "error": "Chat ID required! Order ID required!",
  "errors": ["Chat ID required!", "Order ID required!"]
}
```

- `result` and `error` are mutually exclusive. A response has one or the other, never both.
- `error` is a single string: every problem found, joined into one sentence.
- `errors` is the same problems as an array, one per element.
- **All problems are reported at once.** If two fields are missing you get both in one
  response — fix them together rather than discovering them one call at a time.
- **HTTP 400 means the lookup returned nothing.** Never present a 400 response as data,
  and never fill the gap with a guess.

### Authentication

- Every call requires `chat_id` — a session identifier automatically issued once the user is logged in the admin panel.
- The `chat_id` is resolved to a chat record; if it doesn't exist, the call fails with `Chat not found!`.

### Tool Permission Groups

Every tool is gated by one of the permission groups, configured per store under `setting.side.backend.tool.<group>.status`:

| Group | Tools |
|---|---|
| `product` | `getCategory`, `getCategories`, `getManufacturer`, `getManufacturers`, `getProduct`, `getProducts` |
| `customer` | `getCustomer`, `getCustomers`, `getCustomerGroups` |
| `order` | `getOrder`, `getOrders`, `getOrderStatuses` |

Tools `getCountries`/`getZonesByCountryId` are ungated. 

If a group is disabled for the store, its tools return `You do not have permission to use this tool!`.

---

## Tool Reference

### `getCategory`
```
POST <STABLE_API_URL>/getCategory
Content-Type: application/json
{"chat_id": "<CHAT_ID>", "category_id": <CATEGORY_ID>}
```
**Required:** `chat_id`, `category_id`
**Result:** a single category object (see [Category fields](#category-fields))
**Errors:** `Chat ID required!`, `Category ID required!`, `Chat not found!`, `Category not found!`

### `getCategories`
```
POST <STABLE_API_URL>/getCategories
Content-Type: application/json
{"chat_id": "<CHAT_ID>", "name": "", "parent_category_id": 0, "status": 1, "sort": "sort_order", "order": "ASC", "page": 1}
```
**Required:** `chat_id`
**Optional:** `name`, `parent_category_id` (`0` = top level), `status` (`1` enabled / `0` disabled — omit for both), `sort`, `order`, `page`
**Sorting:** `sort` accepts `name`, `status`, `sort_order` (default `sort_order`); `order` accepts `ASC` or `DESC` (default `ASC`)
**Result:** `{ "categories": { "<category_id>": {...}, ... } }` — one level of the category tree, keyed by ID

### `getManufacturer`
```
POST <STABLE_API_URL>/getManufacturer
Content-Type: application/json
{"chat_id": "<CHAT_ID>", "manufacturer_id": <MANUFACTURER_ID>}
```
**Required:** `chat_id`, `manufacturer_id`
**Result:** a single manufacturer object (see [Manufacturer fields](#manufacturer-fields))
**Errors:** `Manufacturer ID required!`, `Manufacturer not found!`

### `getManufacturers`
```
POST <STABLE_API_URL>/getManufacturers
Content-Type: application/json
{"chat_id": "<CHAT_ID>", "name": "", "sort": "name", "order": "ASC", "page": 1}
```
**Required:** `chat_id`
**Optional:** `name` (matches any word, `LIKE %word%`), `sort`, `order`, `page`
**Sorting:** `sort` accepts `name` or `sort_order` (default `name`); `order` accepts `ASC` or `DESC` (default `ASC`)
**Result:** `{ "manufacturers": [...], "manufacturerCount": N, "page": N, "pageCount": N }` (page size 20)

Use this to turn a brand name into a `manufacturer_id`, then pass that id to `getProducts`.

### `getProduct`
```
POST <STABLE_API_URL>/getProduct
Content-Type: application/json
{"chat_id": "<CHAT_ID>", "product_id": <PRODUCT_ID>}
```
**Required:** `chat_id`, `product_id`
**Result:** a single product object (see [Product fields](#product-fields))
**Errors:** `Product ID required!`, `Product not found!`

### `getProducts`
```
POST <STABLE_API_URL>/getProducts
Content-Type: application/json
{"chat_id": "<CHAT_ID>", "name": "chair", "category_id": 0, "quantity_max": 5, "status": 1, "sort": "quantity", "order": "ASC", "page": 1}
```
**Required:** `chat_id`

**Optional filters:**

| Field | Type | Meaning |
|---|---|---|
| `name` | string | matches any word in the product name (`LIKE %word%`) |
| `model` | string | exact, case-insensitive |
| `category_id` | number | filters by category path, so subcategories are included |
| `manufacturer_id` | number | one brand. Resolve a brand name to its id with `getManufacturers` first |
| `price_min` / `price_max` | number | the stored base price, **before tax and currency conversion** — not what a shopper sees on the storefront |
| `quantity_min` / `quantity_max` | number | stock on hand |
| `status` | number | `1` enabled / `0` disabled. **Omit to get both** — there is no active-only default here |
| `date_added_from` / `date_added_to` | string | `YYYY-MM-DD`, inclusive |

**Sorting:** `sort` accepts `name`, `model`, `price`, `quantity`, `status`, `sort_order`, `date_added`, `manufacturer`, `rating` (default `sort_order`); `order` accepts `ASC` or `DESC` (default `ASC`)

**Result:** `{ "products": {...}, "productCount": N, "page": N, "pageCount": N }` — page size is fixed at 20

Omit a filter to leave it out — there is no "any" value to pass. `tag` and `description` are **not** searchable.

### `getCustomer`
```
POST <STABLE_API_URL>/getCustomer
Content-Type: application/json
{"chat_id": "<CHAT_ID>", "customer_id": <CUSTOMER_ID>}
```
**Required:** `chat_id`, `customer_id`
**Result:** a single customer object (see [Customer fields](#customer-fields))
**Errors:** `Customer ID required!`, `Customer not found!`

### `getCustomers`
```
POST <STABLE_API_URL>/getCustomers
Content-Type: application/json
{"chat_id": "<CHAT_ID>", "name": "Jane", "email": "", "customer_group_id": 0, "status": 1, "date_added_from": "", "date_added_to": "", "sort": "date_added", "order": "DESC", "page": 1}
```
**Required:** `chat_id`
**Optional:** `name`, `email`, `customer_group_id`, `status` (`1`/`0` — omit for both), `date_added_from` / `date_added_to` (`YYYY-MM-DD`, inclusive), `sort`, `order`, `page`
**Sorting:** `sort` accepts `name`, `email`, `customer_group`, `status`, `date_added` (default `name`); `order` accepts `ASC` or `DESC` (default `ASC`)
**Result:** `{ "customers": [...], "customerCount": N, "page": N, "pageCount": N }` (page size 20)

### `getCustomerGroups`
```
POST <STABLE_API_URL>/getCustomerGroups
Content-Type: application/json
{"chat_id": "<CHAT_ID>"}
```
**Required:** `chat_id`
**Result:** `{ "customer_groups": [ { "customer_group_id": ..., "name": ... }, ... ] }`

### `getOrder`
```
POST <STABLE_API_URL>/getOrder
Content-Type: application/json
{"chat_id": "<CHAT_ID>", "order_id": <ORDER_ID>}
```
**Required:** `chat_id`, `order_id`
**Result:** a single order object (see [Order fields](#order-fields))
**Errors:** `Order ID required!`, `Order not found!`

### `getOrders`
```
POST <STABLE_API_URL>/getOrders
Content-Type: application/json
{"chat_id": "<CHAT_ID>", "customer_name": "Jane Doe", "order_status_id": 0, "total_min": 0, "total_max": 0, "date_added_from": "", "date_added_to": "", "sort": "order_id", "order": "DESC", "page": 1}
```
**Required:** `chat_id`
**Optional:** `customer_name`, `order_status_id`, `total_min` / `total_max`, `date_added_from` / `date_added_to` (`YYYY-MM-DD`, inclusive), `sort`, `order`, `page`

**Sorting:** `sort` accepts `order_id`, `customer_name`, `order_status`, `total`, `date_added` (default `order_id`); `order` accepts `ASC` or `DESC` — **default `DESC` here**, unlike the other search tools.

So with no `sort`/`order` given, the list is newest first and **the latest order is `orders[0]` on page 1** — take it from there rather than paging to the end. But that only holds for the default: if you pass your own `sort` or `order`, position 0 means whatever you sorted by, not "newest". Sort deliberately, then read the first row accordingly.

**Result:** `{ "orders": [...], "orderCount": N, "page": N, "pageCount": N }` (page size 20)
**Default filter:** if `order_status_id` is omitted, only orders with `order_status_id > 0` are returned.

#### Missing orders — `order_status_id: 0`

In OpenCart, **`order_status_id = 0` means the order was started but never confirmed.** The
row exists in the `order` table but checkout was abandoned or failed part-way. These are
what a store owner means by any of:

> missing orders · abandoned orders · incomplete orders · failed orders · unconfirmed orders ·
> брошенные заказы · незавершённые заказы · ошибочные заказы

**You already know what those mean — don't ask.** Call `getOrders` with `"order_status_id": 0`
and report what comes back. The only thing worth clarifying is the time range, and only if
the user didn't give one.

Two details about these rows:

- `order_status` comes back **`null`** — status `0` has no name, because it has no row in the
  `order_status` table. Call them "missing" or "unconfirmed", never `null`.
- `getOrderStatuses` does **not** list `0`. It reads the `order_status` table, and `0` is the
  absence of a status rather than one of them. So don't look for it there and don't conclude
  it isn't queryable — `0` is a valid value for the `order_status_id` filter.

### `getOrderStatuses`
```
POST <STABLE_API_URL>/getOrderStatuses
Content-Type: application/json
{"chat_id": "<CHAT_ID>"}
```
**Required:** `chat_id`
**Result:** `{ "order_statuses": [ { "order_status_id": ..., "name": ... }, ... ] }`

### `getCountries`
```
POST <STABLE_API_URL>/getCountries
Content-Type: application/json
{"chat_id": "<CHAT_ID>"}
```
**Required:** `chat_id` (no permission group — always available)
**Result:** `{ "countries": [ { "country_id": ..., "name": ..., "iso_code_2": ..., ... }, ... ] }`

### `getZonesByCountryId`
```
POST <STABLE_API_URL>/getZonesByCountryId
Content-Type: application/json
{"chat_id": "<CHAT_ID>", "country_id": <COUNTRY_ID>}
```
**Required:** `chat_id`, `country_id` (no permission group — always available)
**Result:** `{ "zones": [ { "zone_id": ..., "name": ..., "code": ..., ... }, ... ] }`

---

## Response Field Reference

### Category fields
`category_id`, `name`, `description`, `meta_title`, `meta_description`, `meta_keyword`, `image`, `parent_category_id`, `sort_order`, `status`, `date_added`, `date_modified`

### Product fields
`product_id`, `name`, `description`, `meta_title`, `meta_description`, `meta_keyword`, `tag`, `model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`, `quantity`, `image`, `manufacturer_id`, `manufacturer`, `categories_id` (comma-separated), `options` (array of option groups with values/prices), `subscriptions` (subscription plans; each carries `subscription_plan_id` and `name`), `price` (formatted, discount-aware), `special` (formatted special price), `discount` (formatted quantity-1 discount), `reward`, `points`, `tax_class_id`, `tax_class` (the tax class title), `date_available`, `weight`/`weight_class_id`/`weight_class` (unit), `length`/`width`/`height`/`length_class_id`/`length_class` (unit), `subtract`, `rating` (rounded average), `reviews` (count), `minimum`, `sort_order`, `status`, `date_added`, `date_modified`, `href` (storefront link)

### Manufacturer fields
`manufacturer_id`, `name`, `image`, `sort_order`

### Customer fields
`customer_id`, `customer_group_id`, `firstname`, `lastname`, `email`, `telephone`, `custom_field`, `address` (nested [Address fields](#address-fields) for the customer's default address), `newsletter`, `status`, `safe`, `date_added`

### Address fields
`address_id`, `firstname`, `lastname`, `company`, `address_1`, `address_2`, `postcode`, `city`, `zone_id`, `zone`, `zone_code`, `country_id`, `country`, `iso_code_2`, `iso_code_3`, `address_format`, `custom_field`

### Order fields
`order_id`, `invoice_no`, `invoice_prefix`, `store_id`, `store_name`, `store_url`, `customer_id`, `customer`, `customer_group_id`, `firstname`, `lastname`, `email`, `telephone`, `custom_field`,
`payment_firstname`…`payment_country`, `payment_iso_code_2/3`, `payment_address_format`, `payment_custom_field`, `payment_method`, `payment_code`,
`shipping_firstname`…`shipping_country`, `shipping_iso_code_2/3`, `shipping_address_format`, `shipping_custom_field`, `shipping_method`, `shipping_code`,
`products` (array of order line items), `totals` (array of order total lines — sub-total, shipping, tax, total, etc.), `comment`, `total`,
`order_status_id`, `order_status` (name), `affiliate_id`, `commission`, `language_id`, `language_code`, `currency_id`, `currency_code`, `currency_value`, `ip`, `forwarded_ip`, `user_agent`, `accept_language`, `date_added`, `date_modified`

### Order list item fields
`order_id`, `firstname`, `lastname`, `order_status_id`, `order_status`, `shipping_code`, `total`, `currency_code`, `currency_value`, `date_added`, `date_modified`

---

## Common Recipes

Every line below is `POST` with the shown JSON body.

### 1. Find a product by name and check stock
```
POST <STABLE_API_URL>/getProducts {"chat_id": "<CHAT_ID>", "name": "hoodie"}
```
Read `quantity` and `status` on each returned product.

### 2. List a customer's recent orders
```
POST <STABLE_API_URL>/getOrders {"chat_id": "<CHAT_ID>", "customer_name": "Jane Doe", "page": 1}
POST <STABLE_API_URL>/getOrder  {"chat_id": "<CHAT_ID>", "order_id": <ORDER_ID>}
```
The second call gives full line items and totals.

### 3. Find all Pending orders from the last 7 days
```
POST <STABLE_API_URL>/getOrderStatuses {"chat_id": "<CHAT_ID>"}
```
Look up the `order_status_id` for "Pending", then:
```
POST <STABLE_API_URL>/getOrders {"chat_id": "<CHAT_ID>", "order_status_id": <PENDING_ID>, "date_added_from": "2026-07-10", "date_added_to": "2026-07-17"}
```

### 4. Resolve a shipping zone for an order/address
```
POST <STABLE_API_URL>/getCountries        {"chat_id": "<CHAT_ID>"}
POST <STABLE_API_URL>/getZonesByCountryId {"chat_id": "<CHAT_ID>", "country_id": <COUNTRY_ID>}
```

### 5. See which customer group a customer belongs to
```
POST <STABLE_API_URL>/getCustomerGroups {"chat_id": "<CHAT_ID>"}
POST <STABLE_API_URL>/getCustomers      {"chat_id": "<CHAT_ID>", "customer_group_id": <CUSTOMER_GROUP_ID>}
```

### 6. Browse a category tree
```
POST <STABLE_API_URL>/getCategories {"chat_id": "<CHAT_ID>", "parent_id": 0}
```
Recurse into a child by calling again with `"parent_id": <CHILD_CATEGORY_ID>`.

---

## Error Handling

An error response is **HTTP 400** and carries both `error` (all problems as one string)
and `errors` (the same problems as an array). Read `errors` when several things went wrong
at once, and fix them together.

```json
{
  "jsonrpc": "2.0",
  "error": "Chat ID required! Order ID required!",
  "errors": ["Chat ID required!", "Order ID required!"]
}
```

**A 400 means you got no data.** Say the lookup failed — never present a guess, a
remembered value, or a plausible-looking record as if it came from the store.

| Error | Cause | Solution |
|---|---|---|
| `Chat ID required!` | `chat_id` missing from the request body | Ask the user to refresh the OpenCart admin page |
| `Chat not found!` | `chat_id` doesn't match a known session | Ask the user to refresh the OpenCart admin page |
| `You do not have permission to use this tool!` | This tool's permission group is disabled for this store | Explain the tool is out of scope here; a store admin can enable it under the module's settings |
| `<Field> required!` | A required field was missing from the JSON body | Supply the field and retry. If *every* field is reported missing, the body didn't arrive — resend as a JSON object in the POST body |
| `<Thing> not found!` | The ID didn't match any record | Double-check the ID or broaden the search with the corresponding `get<Things>` tool |

Always explain errors in plain language — never raw JSON-RPC codes.

---

## Best Practices

1. **One transport for everything** — every tool is `POST` with all arguments in a JSON body. No `GET`, no query strings. Building a `?chat_id=...` URL means you are calling it wrong.
2. **Report what you got, not what you expected** — if a call returned 400 or an empty set, say so. Never fill a gap with a plausible-sounding record.
3. **Read-only boundary** — never suggest or imply that a call here changes a record. Point to Catalog/Sales/Customers screens in admin for edits.
4. **Resolve IDs before drilling in** — use the plural search tool (`getProducts`, `getCustomers`, `getOrders`) to find an ID, then the singular tool for full detail.
5. **Filter and sort on the server, don't post-process** — every search tool takes `sort`, `order` and a full set of filters. "The five biggest orders this month" is one call with a date range, `sort: "total"`, `order: "DESC"` — not a broad fetch you then reorder yourself. Page 1 already holds the answer.
6. **Respect pagination** — all search tools cap at 20 results per page; use `page` to page through large result sets rather than assuming everything fits on page 1. If you only need the top or bottom few, sort instead of paging.
7. **`status` has no default** — `getCategories`, `getProducts` and `getCustomers` return both enabled and disabled records unless you pass `status` explicitly. Don't describe a count as "active" unless you filtered for it.
8. **Date filters are inclusive and string-based** — always pass `YYYY-MM-DD`.
9. **`getOrders` hides missing orders by default** — "missing", "abandoned", "incomplete", "failed" and "unconfirmed" all mean `order_status_id = 0` in OpenCart. Pass `order_status_id: 0` to get them; never ask the user to define the term.
10. **Category IDs from `getProducts`** — `category_id` filtering includes the full category path, so it also returns products in subcategories.
11. **Never fabricate IDs or field values** — every number/name shown to the user must come from a tool response.

---
