# Deploying GamingPlace on Vercel

This is a starting configuration for the legacy CodeIgniter 3 application. It has not yet been deployed or runtime-tested.

## Setup

1. Create a hosted MySQL database and import the project's database schema.
2. Import this GitHub repository into Vercel.
3. Add these Vercel Environment Variables: `DB_HOST`, `DB_USER`, `DB_PASS`, and `DB_NAME`.
4. Set `BASE_URL` to the deployed site's URL, but note that `application/config/config.php` must be updated to read this variable; it currently hard-codes a localhost URL.
5. Deploy and test product browsing, login, sessions, cart, and checkout.

## Limitations

- The PHP runtime is community-maintained, not an official Vercel runtime.
- Test legacy CodeIgniter compatibility and all routes using Vercel function logs.
- Use external storage for uploads that must persist; do not rely on the function filesystem.
- Never commit database credentials.
