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



Tested by attempting to edit/delete User A's blog post while logged in as User B, both through the UI and by directly typing protected URLs into the browser address bar.

## Database Operations (Group 5)

| Test | Result |
|---|---|
| Insertion — new blog post appears in `blogPost` table | ✅ Pass |
| Retrieval — homepage correctly displays existing posts | ✅ Pass |
| Update — editing a post updates `title`/`content`/`updated_at` in DB | ✅ Pass |
| Deletion — deleting a post removes its row from `blogPost` | ✅ Pass |
| Foreign key — every `blogPost.user_id` matches a real row in `user` table | ✅ Pass |

##  Features Tested

| Feature | Result |
|---|---|
| Blog search (title/content) | ✅ Pass |
| Empty state (no search results) | ✅ Pass |
| Rich text formatting (bold/italic/code/heading) | ✅ Pass |
| Password complexity validation (8+ chars, number, symbol) | ✅ Pass |
| Responsive layout (desktop/tablet/mobile) | ✅ Pass |
| Mobile hamburger navigation | ✅ Pass |