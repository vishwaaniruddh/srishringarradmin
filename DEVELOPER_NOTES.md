# New Admin Architecture (MVC)

This folder contains the new administration system built with a dedicated MVC (Model-View-Controller) architecture.

## 🚨 VERY IMPORTANT NOTES
- **DATABASE INTEGRITY**: **NEVER delete or truncate** any record from the database. If maintenance is needed, provide the SQL query for manual execution. All operations must prioritize data preservation.
- **CODE ISOLATION**: All new logic, controllers, models, and views **MUST** be contained within this `new_admin/` directory. Modifications outside this folder are strictly prohibited.
- **LEGACY PRESERVATION**: DO NOT modify existing files in the parent `public_html/admin/` or other `public_html/` directories. This new system acts as a parallel, modern alternative.
- **TRANSACTIONS**: Use `mysqli_begin_transaction` for all multi-step data insertions to ensure integrity.
- **POS DATABASE**: Treat `$con3` as read-only. Use it only for verification.
- **SECURITY**: Always use `check_auth.php` or a dedicated AuthController to verify user sessions.

## Directory Structure
- `Core/`: Base classes (Controller, Model, Database)
- `Controllers/`: Logic for handling requests
- `Models/`: Database interactions
- `Views/`: UI templates
- `Config/`: Configuration files

## How to use
The system uses a Front Controller pattern. All requests go through `index.php`.
Example: `index.php?controller=dashboard&action=index`

## Hooking into existing system
To use existing database connections, the `Core\Database` class automatically pulls from `../config.php`.
