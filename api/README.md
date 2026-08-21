# AssetTracker JSON API

All requests use the root dispatcher:

```text
http://localhost/AssetTracker/api.php?api=departments
```

JSON request bodies are required for `POST`, `PUT`, and `PATCH` requests.
Protected endpoints use the same PHP session as the web application.

## Implemented resources

| Method | URL | Purpose |
| --- | --- | --- |
| GET, POST | `api=departments` | List or create departments |
| GET | `api=departments&id=1` | Fetch one department |
| GET, POST | `api=designations` | List or create designations |
| GET | `api=assets` | List assets; supports `category_id`, `status`, `search` |
| GET | `api=assets/show&id=1` | Fetch one asset |
| POST, PUT, PATCH, DELETE | `api=assets&id=1` | Create, update, or delete assets |
| GET | `api=assets/history&id=1` | Fetch assignment history |
| GET | `api=assets/requests` | List filtered requests |
| POST | `api=assets/request&id=1` | Create an asset request |
| POST, PATCH | `api=assets/requests/cancel&id=1` | Cancel a pending request |
| GET | `api=users` | Paginated users |
| POST, PUT, PATCH, DELETE | `api=users&id=1` | Create, update, or soft-delete users |
| GET | `api=users/dashboard-data` | Dashboard JSON |
| GET, POST, PATCH, DELETE | `api=notices` | List, create, update, or delete notices |
| GET, PATCH | `api=notices/mark-confirmed&id=1` | Confirm a notice |
| POST | `api=auth/login` | Authenticate and start a session |
| POST | `api=auth/logout` | End the current session |

Successful responses use `{ "success": true, "data": ... }`. Errors use `{ "success": false, "error": { "code": ..., "message": ... } }`.
