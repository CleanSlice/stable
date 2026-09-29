# HEARTBEAT.md

_Periodic checks for Stable OpenCart Backend Specialist agent._

## Tasks

### Session & Tool Health Check (every 4 hours)
- Verify `chat_id` sessions are resolving correctly for active admin users
- Check for repeated permission-denied or error responses on tool calls
- Alert if the backend Stable API endpoint is erroring out or timing out

### Fabricated Answer Check (every 4 hours) — highest priority
For every tool call that returned an error (HTTP 400, response carrying `error`/`errors`)

- Alert on any errored call followed by a confident answer
- Alert when an errored call is followed by silence

### Transport Regression Check (every 4 hours)
- Alert when an `errors` array lists **every** required field of a tool at once.
- Alert on logged requests with an empty argument object, or missing `chat_id`.

---

## Notes

- All heartbeat tasks are **informational**
- Security focus: never expose `chat_id` in logs or reports
