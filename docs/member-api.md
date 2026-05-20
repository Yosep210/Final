# Member API

Base endpoint: `/api/members`

Authentication:
- Requires Sanctum token.

Response format:
- Success responses use `MemberResource`.
- Validation errors return `422 Unprocessable Entity`.
- Unauthorized actions return `403 Forbidden`.
- Missing records return `404 Not Found`.

Available endpoints:
- `GET /api/members`
- `POST /api/members`
- `GET /api/members/{member}`
- `PUT /api/members/{member}`
- `DELETE /api/members/{member}`

Member payload:
- `name` string required
- `username` string required unique
- `email` string required unique
- `password` string required on create, optional on update
- `password_confirmation` string required when password is sent
- `status` string required
- `referral_code` string nullable
- `email_verified_at` date nullable
- `last_login_at` date nullable

Example create payload:

```json
{
  "name": "John Doe",
  "username": "johndoe",
  "email": "john@example.com",
  "password": "Password123!",
  "password_confirmation": "Password123!",
  "status": "active",
  "referral_code": "REF-001"
}
```

Example list response:

```json
{
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "username": "johndoe",
      "email": "john@example.com",
      "status": "active",
      "referral_code": "REF-001",
      "email_verified_at": null,
      "last_login_at": null,
      "created_at": "2026-05-20 10:00:00",
      "updated_at": "2026-05-20 10:00:00"
    }
  ],
  "links": [],
  "meta": []
}
```
