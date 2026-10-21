# Deploying GamingPlace

GamingPlace is a legacy CodeIgniter 3 PHP application. Deployments should be tested end-to-end on the selected PHP runtime before the storefront is used for real orders.

## Required setup

1. Provision a hosted MySQL database and import the project's schema and product data.
2. Configure DB_HOST, DB_USER, DB_PASS, and DB_NAME in the hosting environment. Use a restricted database user and never commit credentials.
3. Set CI_ENV=production in the deployment environment. The application derives the asset URL from the incoming production host and supports a local XAMPP subfolder.
4. Configure PayPal Express Checkout credentials as environment variables: PAYPAL_API_USERNAME, PAYPAL_API_PASSWORD, and PAYPAL_API_SIGNATURE. Set PAYPAL_MODE=sandbox for testing; use PAYPAL_MODE=live only after sandbox tests and account verification are complete.
5. Deploy, then test product browsing, details, registration, login/logout, persistent sessions, cart quantity updates, PayPal cancellation, successful PayPal return, and the saved order records.

## Production blockers / limitations to verify

- This is legacy CodeIgniter 3 code using a community PHP runtime on Vercel. Test every route and review function logs; a successful build alone is not a runtime test.
- Vercel's function filesystem is ephemeral. Do not rely on local disk for uploads, sessions, or other persistent state.
- PayPal payment confirmation is verified server-side before an order is recorded. Never treat a browser redirect by itself as proof of payment.
- Confirm the database provider's TLS requirements and run an end-to-end test using PayPal sandbox credentials before accepting real payments.
- The cart assumes the configured database has the existing products, users, and orders tables and that the orders table uses the fields written by Cart::success().
