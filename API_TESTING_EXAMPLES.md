# API Testing Examples for KPI-360-Guru

## Test Using cURL (Windows PowerShell)

### 1. Test Login API
```powershell
# Login dengan user yang ada di database
$response = Invoke-RestMethod -Uri "http://localhost:8000/api/login" `
  -Method POST `
  -ContentType "application/json" `
  -Body '{"email":"test@example.com","password":"password"}'

# Display response
$response | ConvertTo-Json

# Save token for next requests
$token = $response.token
```

### 2. Test Get User Profile
```powershell
# Use token from login
$headers = @{
  "Authorization" = "Bearer $token"
}

$user = Invoke-RestMethod -Uri "http://localhost:8000/api/user" `
  -Method GET `
  -Headers $headers

$user | ConvertTo-Json
```

### 3. Test Logout
```powershell
$logout = Invoke-RestMethod -Uri "http://localhost:8000/api/logout" `
  -Method POST `
  -Headers $headers

$logout | ConvertTo-Json
```

---

## Test Using cURL (Command Prompt / Bash)

### 1. Login
```bash
curl -X POST http://localhost:8000/api/login ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"test@example.com\",\"password\":\"password\"}"
```

### 2. Get User (replace YOUR_TOKEN)
```bash
curl -X GET http://localhost:8000/api/user ^
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 3. Logout
```bash
curl -X POST http://localhost:8000/api/logout ^
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Postman / Thunder Client Collection

### Request 1: Login
- **Method:** POST
- **URL:** `http://localhost:8000/api/login`
- **Headers:**
  - `Content-Type: application/json`
- **Body (raw JSON):**
```json
{
  "email": "test@example.com",
  "password": "password"
}
```

### Request 2: Get User
- **Method:** GET
- **URL:** `http://localhost:8000/api/user`
- **Headers:**
  - `Authorization: Bearer {{token}}`

### Request 3: Logout
- **Method:** POST
- **URL:** `http://localhost:8000/api/logout`
- **Headers:**
  - `Authorization: Bearer {{token}}`

---

## Expected Responses

### Login Success Response:
```json
{
  "success": true,
  "message": "Login berhasil",
  "token": "1|AbCdEfGhIjKlMnOpQrStUvWxYz123456",
  "user": {
    "id": 1,
    "name": "Test User",
    "email": "test@example.com",
    "role": "admin"
  }
}
```

### Get User Response:
```json
{
  "success": true,
  "user": {
    "id": 1,
    "name": "Test User",
    "email": "test@example.com",
    "role": "admin"
  }
}
```

### Logout Response:
```json
{
  "success": true,
  "message": "Logout berhasil"
}
```

---

## Testing Checklist

- [ ] Login with valid credentials → Returns token ✓
- [ ] Login with invalid credentials → Returns error
- [ ] Get user profile with valid token → Returns user data
- [ ] Get user profile without token → Returns 401 Unauthorized
- [ ] Logout with valid token → Revokes token
- [ ] Use revoked token → Returns 401 Unauthorized
