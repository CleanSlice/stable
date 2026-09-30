---
name: stable-opencart4-frontend
description: The only way to reach this OpenCart storefront — search and sort the catalog by name, model, brand, category, price, stock or date, look up manufacturers, read product and category detail, check the logged-in customer's profile and past orders, add / change quantity / remove cart lines, list shipping and payment methods, and place a real order. Every tool is POST with a JSON body to the Stable API URL from the integrator context; storefront pages, route= URLs and hand-written forms are not alternatives and do not work. Load before answering anything about products, prices, stock, the cart, checkout or orders.
metadata:
  always: true
---

# Stable OpenCart Frontend

Comprehensive skill for browsing the catalog, managing the cart, and placing orders in OpenCart using the Stable module's frontend HTTP API.

⚠️ **IMPORTANT — this is not an MCP server.** These are plain HTTP endpoints, called with the built-in **`http`** tool (see [How to call these tools](#how-to-call-these-tools--use-the-http-tool)). No MCP server in your tool list can reach this store: a tool named `search`, `query_knowledge` or anything similar belongs to something unrelated and knows nothing about this catalog. Take the **Chat ID** and **Stable API URL** from the integrator context (the embed's `data-prompt` attribute) — they are the only valid coordinates for this store. Never fetch OpenCart pages directly.

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

Worked example. To run the `getProducts` sketch, call `http` with:

```json
{
  "url": "<STABLE_API_URL>/getProducts",
  "method": "POST",
  "headers": {"Content-Type": "application/json"},
  "body": "{\"chat_id\":\"<CHAT_ID>\",\"name\":\"hoodie\"}"
}
```

Three mistakes that kill the call before it ever leaves the runtime:

- **`body` passed as an object.** It is a string parameter. Serialize it. A nested object
  is rejected outright, and the rejection is not a store error — the request never went.
- **A relative `url`.** `/index.php?route=...` is not accepted. Use the full `<STABLE_API_URL>`
  from the integrator context.
- **Reaching for a different tool** because this one feels indirect. A tool named `search`,
  `query_knowledge` or anything similar belongs to an unrelated server and knows nothing
  about this store's catalog. There is no shortcut and no second route.

**Keep responses small.** `http` truncates the response body at 50,000 characters and times
out after 30 seconds. A broad `getProducts` call easily exceeds that and comes back as JSON
cut off mid-structure — with `status` still `200`. Always narrow the search: pass `name`,
`model` or `category_id` instead of fetching everything, and use `getProduct` when you need
full detail on one item.

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
  "error": "Chat ID required! Product ID required!",
  "errors": ["Chat ID required!", "Product ID required!"]
}
```

- `result` and `error` are mutually exclusive. A response has one or the other, never both.
- `error` is a single string: every problem found, joined into one sentence.
- `errors` is the same problems as an array, one per element. Use it when you need to
  address them individually.
- **All problems are reported at once.** If three fields are missing you get all three in
  one response — ask the customer for all of them in one message rather than discovering
  them one call at a time.
- **HTTP 400 means the action did NOT happen.** Nothing was added, changed, or ordered.
  Never describe a 400 response as a success.

### Authentication

- Every call requires `chat_id` — a session identifier automatically issued once the customer is logged in the storefront.
- The `chat_id` is resolved to a chat record; if it doesn't exist, the call fails with `Chat not found!`.
- Every tool scopes data to that one customer's session — `getCurrentCustomer`/`getCurrentCustomerOrder(s)` use the logged-in customer's ID internally; there's no way to query another customer.

### Tool Permission Groups

Every tool is gated by one of the permission groups, configured per store under `setting.side.frontend.tool.<group>.status`:

| Group | Tools |
|---|---|
| `product` | `getCategory`, `getCategories`, `getManufacturer`, `getManufacturers`, `getProduct`, `getProducts` |
| `customer` | `getCurrentCustomer`, `getCurrentCustomerOrder`, `getCurrentCustomerOrders` |
| `cart` | `addCartProduct`, `editCartProduct`, `deleteCartProduct`, `getCartProducts` |
| `checkout` | `createOrder`, `getShippingMethods`, `getPaymentMethods` |

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
{"chat_id": "<CHAT_ID>", "name": "", "parent_category_id": 0, "sort": "sort_order", "order": "ASC", "page": 1}
```
**Required:** `chat_id`
**Optional:** `name`, `parent_category_id` (`0` = top level), `sort`, `order`, `page`
**Sorting:** `sort` accepts `name` or `sort_order` (default `sort_order`); `order` accepts `ASC` or `DESC` (default `ASC`)
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

Use this to turn a brand the customer names into a `manufacturer_id`, then pass that id to `getProducts`.

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
{"chat_id": "<CHAT_ID>", "name": "hoodie", "category_id": 0, "price_min": 25, "price_max": 99.99, "sort": "price", "order": "ASC", "page": 1}
```
**Required:** `chat_id`

**Optional filters:**

| Field | Type | Meaning |
|---|---|---|
| `name` | string | matches any word in the product name (`LIKE %word%`) |
| `model` | string | exact, case-insensitive |
| `category_id` | number | filters by category path, so subcategories are included |
| `manufacturer_id` | number | one brand. Resolve a brand name to its id with `getManufacturers` first |
| `price_min` / `price_max` | number | **as the customer sees the price** — tax included, in the current currency. Plain number, `.` as the decimal separator, no currency symbol |
| `quantity_min` / `quantity_max` | number | stock on hand |
| `date_added_from` / `date_added_to` | string | `YYYY-MM-DD`, inclusive |

**Sorting:** `sort` accepts `name`, `model`, `price`, `quantity`, `sort_order`, `date_added`, `manufacturer`, `rating` (default `sort_order`); `order` accepts `ASC` or `DESC` (default `ASC`). Sorting by `price` uses the same customer-visible price as the price filters.

**Result:** `{ "products": {...}, "productCount": N, "page": N, "pageCount": N }` — page size is fixed at 20

Omit a filter to leave it out — there is no "any" value to pass. `tag` and `description` are **not** searchable.

### `getCurrentCustomer`
```
POST <STABLE_API_URL>/getCurrentCustomer
Content-Type: application/json
{"chat_id": "<CHAT_ID>"}
```
**Required:** `chat_id`
**Result:** a single current customer object (see [Customer fields](#customer-fields))
**Errors:** `Current customer not found!`

### `getCurrentCustomerOrder`
```
POST <STABLE_API_URL>/getCurrentCustomerOrder
Content-Type: application/json
{"chat_id": "<CHAT_ID>", "order_id": <ORDER_ID>}
```
**Required:** `chat_id`, `order_id`
**Result:** a single order object if it belongs to the current customer **and** has `order_status_id > 0` — i.e. confirmed orders, excluding the unconfirmed ones at status `0` (see [Order fields](#order-fields))
**Errors:** `Current customer order not found!`

### `getCurrentCustomerOrders`
```
POST <STABLE_API_URL>/getCurrentCustomerOrders
Content-Type: application/json
{"chat_id": "<CHAT_ID>", "page": 1}
```
**Required:** `chat_id` · **Optional:** `page` (default `1`)
**Result:** `{ "orders": [...], "orderCount": N, "page": N, "pageCount": N }` (page size 20)
**Ordering:** fixed — newest first (`order_id` descending). This tool takes no `sort`/`order`, so **the latest order is always `orders[0]` on page 1**. Never page through the list looking for it, and never assume the newest is at the end.
**Default filter:** orders scoped to the current customer and current store with `order_status_id > 0` are returned — confirmed orders only, never the unconfirmed ones at status `0`

### `addCartProduct`
```
POST <STABLE_API_URL>/addCartProduct
Content-Type: application/json
{"chat_id": "<CHAT_ID>", "product_id": <PRODUCT_ID>, "quantity": 1, "option": {"10": 5}, "subscription_plan_id": 0}
```
**Required:** `chat_id`, `product_id` · **Optional:** `quantity` (default `1`), `option`, `subscription_plan_id`
- `option` keys are `product_option_id` (as a string), values are `product_option_value_id` (or free text for text-type options, or an array for checkboxes). Fetch a product's `options` array first (from `getProduct`) to know which options exist and which are `required`.
- If the product has required options and one is missing, the error names the missing option (e.g. `Size required!`).
- If the product is sold on subscription plans, `subscription_plan_id` must be one of the product's `subscriptions[].subscription_plan_id`, or the call fails with `Please select a subscription plan!`.
**Result:** the full updated cart — `{ "products": [...] }`. Each item includes (typical OpenCart cart fields): `cart_id`, `product_id`, `name`, `model`, `image`, `option`, `download`, `quantity`, `minimum`, `subtract`, `stock`, `price`, `total`, `tax_class_id`, `reward`.

### `editCartProduct`
```
POST <STABLE_API_URL>/editCartProduct
Content-Type: application/json
{"chat_id": "<CHAT_ID>", "cart_id": <CART_ID>, "quantity": 3}
```
**Required:** `chat_id`, `cart_id`, `quantity` 
— `cart_id` identifies the specific cart line (get it from `getCartProducts`), not the `product_id`.
**Result:** the full updated cart — `{ "products": [...] }`. Each item includes (typical OpenCart cart fields): `cart_id`, `product_id`, `name`, `model`, `image`, `option`, `download`, `quantity`, `minimum`, `subtract`, `stock`, `price`, `total`, `tax_class_id`, `reward`.

### `deleteCartProduct`
```
POST <STABLE_API_URL>/deleteCartProduct
Content-Type: application/json
{"chat_id": "<CHAT_ID>", "cart_id": <CART_ID>}
```
**Required:** `chat_id`, `cart_id`
**Result:** the full updated cart — `{ "products": [...] }`. Each item includes (typical OpenCart cart fields): `cart_id`, `product_id`, `name`, `model`, `image`, `option`, `download`, `quantity`, `minimum`, `subtract`, `stock`, `price`, `total`, `tax_class_id`, `reward`.

### `getCartProducts`
```
POST <STABLE_API_URL>/getCartProducts
Content-Type: application/json
{"chat_id": "<CHAT_ID>"}
```
**Required:** `chat_id`. 
**Result:** the full customer's cart — `{ "products": [...] }`. Each item includes (typical OpenCart cart fields): `cart_id`, `product_id`, `name`, `model`, `image`, `option`, `download`, `quantity`, `minimum`, `subtract`, `stock`, `price`, `total`, `tax_class_id`, `reward`.

### `createOrder`

A method with no extra fields — `required_fields` came back empty, so **no** `cc_*` fields:
```
POST <STABLE_API_URL>/createOrder
Content-Type: application/json
{
  "chat_id": "<CHAT_ID>",
  "firstname": "Jane", "lastname": "Doe", "email": "jane@example.com", "telephone": "555-0100",
  "address_1": "123 Main St", "city": "Springfield", "postcode": "12345",
  "country_id": <COUNTRY_ID>, "zone_id": <ZONE_ID>,
  "shipping_method_code": "flat.flat",
  "payment_method_code": "cod"
}
```
A card method — every field `getPaymentMethods` listed in `required_fields`, as **strings**.
The exact set differs per method; this example shows one that asked for five:
```
POST <STABLE_API_URL>/createOrder
Content-Type: application/json
{
  "chat_id": "<CHAT_ID>",
  "country_id": <COUNTRY_ID>, "zone_id": <ZONE_ID>,
  "shipping_method_code": "flat.flat",
  "payment_method_code": "<THE CODE getPaymentMethods RETURNED>",
  "cc_owner": "Jane Doe",
  "cc_number": "4007000000027",
  "cc_expire_date_month": "01",
  "cc_expire_date_year": "2029",
  "cc_cvv2": "123"
}
```
**Required:** `chat_id`, `payment_method_code`.
**Also enforced by the server:** `firstname`, `lastname`, `email`, `address_1`, `city`, `postcode`, `country_id`, `zone_id` — any of these omitted are auto-filled from `getCurrentCustomer`'s profile/default address if available; only error out if still missing after that.
**Method codes must come from the store**, not be guessed: pass through the exact `code` values returned by `getShippingMethods`/`getPaymentMethods` for the chosen country/zone.
**Pre-flight checks:** the cart must not be empty, all items must be in stock, and quantities must meet each product's `minimum`.
**Result on success:** the full created order object (same shape as [Order fields](#order-fields)), and the server-side cart is cleared automatically.
**Errors:** field-specific messages (e.g. `E-Mail address does not appear to be valid!`, `Your shopping cart is empty!`, `Products in cart are not in stock!`, `Shipping method required!`, `Payment method required!`).

#### Conditionally required fields

Resolve these **before** calling, so checkout isn't rejected halfway through.

**`shipping_method_code`** — mandatory only if the cart contains a shippable product
(server checks `cart->hasShipping()`); omit it for digital-only carts.

**Payment-specific fields — read them off `getPaymentMethods`, never from memory.**

The store has several card-taking methods and they do **not** all want the same fields.
One asks for five, another skips the cardholder name, another needs a name field called
`cc_name` instead of `cc_owner`. So there is no fixed card field set to learn — the
per-method answer is in the `getPaymentMethods` response for the method the customer chose:

- `required_fields` — send **all** of these, or the call is rejected.
- `optional_fields` — send only what applies. Ask the customer; omit the field when it
  doesn't apply to their card. Never invent a value to fill one in.

Formats, whichever method asks for them:

| Field | Type | Format |
|---|---|---|
| `cc_owner` / `cc_name` | **string** | Name as printed on the card |
| `cc_number` | **string** | Digits only, no spaces or dashes |
| `cc_expire_date_month` | **string** | Two digits, `"01"`…`"12"` |
| `cc_expire_date_year` | **string** | Four digits, e.g. `"2029"` |
| `cc_cvv2` | **string** | 3–4 digits, leading zeros preserved |
| `cc_start_date_month` / `cc_start_date_year` | **string** | Same shape as the expiry pair. Only a few card types have a start date — omit when the customer's card has none |
| `cc_choice` | **string** | Picks a stored card. Omit it to charge the card details in this call |

Send every one as a JSON **string**, never a number. As numbers the values corrupt
silently: `"01"` → `1` (gateways reject it), `"045"` → `45`, and card numbers past
16 digits exceed double precision.

#### Handling card data

- **Ask the customer for card details each time.** Never invent them, never reuse them
  from earlier in the conversation, from a past order, or from `getCurrentCustomer` —
  cards are not stored in the profile and never appear in any tool response.
- **Never echo card data back.** Don't put the number, CVV, or expiry in a recap,
  a confirmation, or an error explanation. At most refer to "the card ending in <last 4>".
- **Send no payment fields the chosen method did not ask for.** If both its
  `required_fields` and `optional_fields` are empty, send none at all and ask for none.
- On `Card Security Code (CVV2) required for this payment method!` or a sibling error —
  ask the customer for that single field and retry. Never fill in a placeholder or
  test value to get the call through.

### `getShippingMethods`
```
POST <STABLE_API_URL>/getShippingMethods
Content-Type: application/json
{"chat_id": "<CHAT_ID>", "country_id": <COUNTRY_ID>, "zone_id": <ZONE_ID>}
```
**Required:** `chat_id`, `country_id`, `zone_id`.
**Result:** `{"shipping_methods": { "<code>": {"code","title","text"}, ... }}` — pass a returned `code` straight into `createOrder`'s `shipping_method_code`.

### `getPaymentMethods`
```
POST <STABLE_API_URL>/getPaymentMethods
Content-Type: application/json
{"chat_id": "<CHAT_ID>", "country_id": <COUNTRY_ID>, "zone_id": <ZONE_ID>}
```
**Required:** `chat_id`, `country_id`, `zone_id`.

**Result:** `{"payment_methods": { "<code>": {...}, ... }}`. Each entry tells you not just
the name but whether you can complete it and what it needs:

```json
{
  "cod": {
    "code": "cod", "title": "Cash On Delivery",
    "flow": "confirm", "required_fields": [], "optional_fields": []
  },
  "authorizenet_aim": {
    "code": "authorizenet_aim", "title": "Credit Card",
    "flow": "send",
    "required_fields": ["cc_owner", "cc_number", "cc_expire_date_month", "cc_expire_date_year", "cc_cvv2"],
    "optional_fields": []
  },
  "pp_standard": {
    "code": "pp_standard", "title": "PayPal",
    "flow": "unsupported",
    "reason": "This method needs the customer to complete payment on the provider's site!"
  }
}
```

**`flow` is the first thing to read.**

| `flow` | Meaning |
|---|---|
| `confirm` | Offline method. You can place the order right here, no extra fields. |
| `send` | Charged through the gateway from this call. Collect `required_fields` first. |
| `unsupported` | **Cannot be completed in chat.** `createOrder` will refuse it. |

**Do not offer an `unsupported` method as if you could process it.** Name it, say the
customer would have to finish that payment on the provider's own site, and offer the
methods you can actually complete. Calling `createOrder` with it returns HTTP 400 and
creates nothing.

**`required_fields` / `optional_fields` are per method** — never assume one method's field
set applies to another, and never carry a field list over from an earlier conversation.
Read them from this response, for this method, every time.

Pass the returned `code` verbatim into `createOrder`'s `payment_method_code`.

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
`category_id`, `name`, `description`, `meta_title`, `meta_description`, `meta_keyword`, `image`, `parent_category_id`, `sort_order`, `date_added`, `date_modified`

### Product fields
`product_id`, `name`, `description`, `meta_title`, `meta_description`, `meta_keyword`, `tag`, `model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`, `quantity`, `image`, `manufacturer_id`, `manufacturer`, `categories_id` (comma-separated), `options` (array of option groups with values/prices), `subscriptions` (subscription plans; each carries `subscription_plan_id` and `name`), `price` (formatted, discount-aware), `special` (formatted special price), `reward`, `points`, `tax_class_id`, `tax_class` (the tax class title), `weight`/`weight_class_id`/`weight_class` (unit), `length`/`width`/`height`/`length_class_id`/`length_class` (unit), `subtract`, `rating` (rounded average), `reviews` (count), `minimum`, `sort_order`, `date_added`, `date_modified`, `href` (storefront link)

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

### 1. Browse and add a product to the cart
```
POST <STABLE_API_URL>/getProducts     {"chat_id": "<CHAT_ID>", "name": "hoodie"}
POST <STABLE_API_URL>/getProduct      {"chat_id": "<CHAT_ID>", "product_id": <PRODUCT_ID>}   (check options[] for required ones)
POST <STABLE_API_URL>/addCartProduct  {"chat_id": "<CHAT_ID>", "product_id": <PRODUCT_ID>, "quantity": 1, "option": {"10": 5}}
```
Then check the returned cart actually contains `<PRODUCT_ID>` before telling the customer it was added.

### 2. Review and adjust the cart
```
POST <STABLE_API_URL>/getCartProducts   {"chat_id": "<CHAT_ID>"}
POST <STABLE_API_URL>/editCartProduct   {"chat_id": "<CHAT_ID>", "cart_id": <CART_ID>, "quantity": 2}
POST <STABLE_API_URL>/deleteCartProduct {"chat_id": "<CHAT_ID>", "cart_id": <CART_ID>}
```

### 3. Full checkout flow
```
POST <STABLE_API_URL>/getCurrentCustomer    {"chat_id": "<CHAT_ID>"}
POST <STABLE_API_URL>/getCountries          {"chat_id": "<CHAT_ID>"}
POST <STABLE_API_URL>/getZonesByCountryId   {"chat_id": "<CHAT_ID>", "country_id": <COUNTRY_ID>}
POST <STABLE_API_URL>/getShippingMethods    {"chat_id": "<CHAT_ID>", "country_id": <COUNTRY_ID>, "zone_id": <ZONE_ID>}
POST <STABLE_API_URL>/getPaymentMethods     {"chat_id": "<CHAT_ID>", "country_id": <COUNTRY_ID>, "zone_id": <ZONE_ID>}
```
Recap items, address, chosen shipping/payment with the customer, then:
```
POST <STABLE_API_URL>/createOrder
{
  "chat_id": "<CHAT_ID>",
  "country_id": <COUNTRY_ID>, "zone_id": <ZONE_ID>,
  "shipping_method_code": "flat.flat",
  "payment_method_code": "cod"
}
```
(Name/email/address fields can be omitted if the logged-in customer already has them on file.)

### 4. Check past orders
```
POST <STABLE_API_URL>/getCurrentCustomerOrders {"chat_id": "<CHAT_ID>", "page": 1}
POST <STABLE_API_URL>/getCurrentCustomerOrder  {"chat_id": "<CHAT_ID>", "order_id": <ORDER_ID>}
```

---

## Error Handling

An error response is **HTTP 400** and carries both `error` (all problems as one string)
and `errors` (the same problems as an array). Read `errors` when several things went wrong
at once — then ask the customer for everything missing in a single message.

```json
{
  "jsonrpc": "2.0",
  "error": "Card Owner required for this payment method! Card Number required for this payment method!",
  "errors": [
    "Card Owner required for this payment method!",
    "Card Number required for this payment method!"
  ]
}
```

**A 400 means nothing happened.** The product was not added, the quantity was not changed,
the order was not placed. Tell the customer the action failed — never report it as done.

| Error | Cause | Solution |
|---|---|---|
| `Chat ID required!` | `chat_id` missing from the request body | Ask the customer to refresh page or log in again |
| `Chat not found!` | `chat_id` doesn't match a known session | Ask the customer to refresh page or log in again |
| `You do not have permission to use this tool!` | This tool's permission group is disabled | Explain the action isn't available here |
| `<Field> required!` | A required field was missing from the JSON body | Supply the field and retry. If *every* field is reported missing, the body didn't arrive — resend as a JSON object in the POST body |
| `<Thing> not found!` | The ID didn't match any record | Double-check the ID or broaden the search with the corresponding `get<Things>` tool |
| `Your shopping cart is empty!` | `createOrder` called with nothing in the cart | Add items first |
| `Products in cart are not in stock!` | An item's stock ran out since it was added | Re-check with `getCartProducts`/`getProduct`, adjust quantity or remove |
| `Products in cart are not available in the required quantity!` | Not a stock problem — a line's total quantity is **below** that product's `minimum` order quantity | **Raise** the quantity to at least `getProduct`'s `minimum` via `editCartProduct` (quantities of the same `product_id` are summed across lines), or remove the line |
| `Please select a subscription plan!` | Product has subscription plans but none/invalid `subscription_plan_id` given | Fetch `getProduct`'s `subscriptions` and pass a valid `subscription_plan_id` |
| `<Option name> required!` | A required product option wasn't supplied to `addCartProduct` | Check `getProduct`'s `options[].required` and ask the customer to choose |
| `<Field> must be between N and M characters!` (`First Name`, `Last Name`, `Address 1`, `City`, `Postcode`) | A checkout field arrived too short or too long | Ask the customer for a value of the right length — never pad, truncate or invent one to satisfy the rule |
| `<Field> does not appear to be valid!` (`E-Mail address`, `Telephone`) | The value failed format validation | Read the value back to the customer and ask them to correct it — never substitute a plausible-looking one |
| `Payment method <code> cannot be completed in chat! It requires the customer to finish payment on the provider site.` | The chosen method needs a redirect to the payment provider, which cannot happen here | Offer one of the methods this store can complete in chat, or tell the customer to finish that payment on the site. The cart is untouched |
| `Payment could not be completed! The order was created but never confirmed.` | The gateway did not confirm the charge; the order exists at status `0` | Do **not** say the order was placed, and do not retry blindly. The cart is left intact — report the failure and offer another payment method |
| `Card Owner required for this payment method!` and siblings (`Card Number`, `Card Expiry Date Month`, `Card Expiry Date Year`, `Card Security Code (CVV2)`) | The chosen method listed that field in `required_fields`, but it arrived missing or empty | Ask the customer for that one field and retry — never substitute a placeholder or test value |

Always explain errors in plain, shopper-friendly language — never raw JSON-RPC codes.

---

## Best Practices

1. **One transport for everything** — every tool is `POST` with all arguments in a JSON body. No `GET`, no query strings. Building a `?chat_id=...` URL means you are calling it wrong.
2. **Verify before you confirm** — a cart response IS the cart. After `addCartProduct` the `product_id` must be in `products[]`; after `deleteCartProduct` the `cart_id` must be gone. Check, then tell the customer. Never announce success you haven't seen in a response.
3. **Filter and sort on the server, don't post-process** — `getProducts` and `getCategories` take `sort`, `order` and a full set of filters. "The three cheapest hoodies" is one call with `name`, `sort: "price"`, `order: "ASC"` — not a broad fetch you then reorder yourself. Page 1 already holds the answer, and you avoid pulling 20 rows to show 3.
4. **Resolve IDs before drilling in** — use the plural search tool (`getProducts`, `getCurrentCustomerOrders`) to find an ID, then the singular tool for full detail.
5. **Never invent method codes** — always pass through the exact `code` from `getShippingMethods`/`getPaymentMethods` for `createOrder`'s `shipping_method_code`/`payment_method_code`.
6. **Resolve options before adding to cart** — check a product's `options[]` (and `required`) via `getProduct` before calling `addCartProduct`, to avoid a rejected call.
7. **Recap before `createOrder`** — cart contents, address, shipping and payment choice — and get explicit confirmation; this call is a real purchase and clears the cart on success. Never put card data in the recap — "the card ending in <last 4>" is the most you may show.
8. **Payment fields are per method, and always strings** — take `required_fields` / `optional_fields` from `getPaymentMethods` for the method the customer picked; the sets differ between methods. Send every value as a JSON string (`"01"`, not `1`). Ask the customer for them; they exist in no tool response.
9. **`cart_id` ≠ `product_id`** — cart edit/delete tools key off the cart line (`cart_id` from `getCartProducts`), not the product.
10. **getCurrentCustomerOrder(s) excludes incomplete orders** — only shows the logged-in customer's own orders with `order_status_id > 0`. Orders at status `0` are unconfirmed (abandoned checkout) and are never visible on the storefront side.
11. **Category IDs from `getProducts`** — `category_id` filtering includes the full category path, so it also returns products in subcategories.
12. **Never fabricate IDs or field values** — every number/name shown to the customer must come from a tool response.

---
