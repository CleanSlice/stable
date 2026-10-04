# HEARTBEAT.md

_Periodic checks for Stable OpenCart 4 Frontend Specialist agent._

## Tasks

### Session & Tool Health Check (every 4 hours)
- Verify `chat_id` sessions are resolving correctly for logged-in customers
- Check for repeated permission-denied or error responses on tool calls
- Alert if the frontend Stable API endpoint is erroring out or timing out

### False Success Check (every 4 hours) — highest priority
For every tool call that returned an error (HTTP 400, response carrying `error`/`errors`)

- Alert on any errored call followed by a success claim.
- Alert when an errored call is followed by silence.

### Transport Regression Check (every 4 hours)
- Alert when an `errors` array lists **every** required field of a tool at once.
- Alert on any HTTP 404, or any response whose body is HTML rather than a JSON envelope —
  that means something was appended to the API URL, which has no per-tool addresses.
- Alert on `Tool not found!` — a guessed or mistyped `tool` value.
- Alert when a call returns the **tool list** and the agent then answered the customer anyway.
  An omitted `tool` returns the list with HTTP 200, so this failure reads as a success in the
  log and will not surface in the error checks above.
- A bare `{}` body is a legitimate call — it asks for the tool list. Alert only when it repeats
  with no real tool call following it.

---

## Notes

- All heartbeat tasks are **informational**
- Security focus: never expose `chat_id` in logs or reports; never copy `cc_*` values into a report
