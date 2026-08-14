# ByteLog — Testing Log

## Authentication

| Test | Result |
|---|---|
| Register new user | ✅ Pass |
| Login with correct credentials | ✅ Pass |
| Login with wrong password | ✅ Pass — shows "Invalid email or password" |
| Register with duplicate email | ✅ Pass — shows "Username or email is already taken" |
| Password stored as bcrypt hash (not plain text) | ✅ Pass — verified in phpMyAdmin |
| Logout destroys session | ✅ Pass |

## Blog CRUD

| Test | Result |
|---|---|
| Create blog (logged in) | ✅ Pass |
| View blog list on homepage | ✅ Pass |
| View single blog page | ✅ Pass |
| Edit own blog | ✅ Pass — form pre-fills existing data |
| Delete own blog | ✅ Pass — confirmation dialog shown |

## Authorization (Security)

| Test | Result |
|---|---|
| Logged-out user redirected away from `create-blog.php` | ✅ Pass |
| Non-owner cannot see Edit/Delete buttons on another user's blog | ✅ Pass |
| Non-owner cannot access `edit-blog.php?id=X` for another user's blog (direct URL) | ✅ Pass — redirected to homepage |
| Non-owner cannot delete another user's blog (server-side check in `delete-blog.php`) | ✅ Pass — same ownership check pattern as edit |
| All SQL queries use prepared statements | ✅ Pass — verified in code |
| All user-generated output escaped with `htmlspecialchars()` | ✅ Pass — verified in code |

## Test accounts used

- **User A (owner):** shehana / shehanadinuki@gmail.com
- **User B (non-owner):** testuser2 / testuser2@example.com

Tested by attempting to edit/delete User A's blog post while logged in as User B, both through the UI and by directly typing protected URLs into the browser address bar.