# Deploying GamingPlace

GamingPlace is a legacy CodeIgniter 3 PHP application. The Vercel configuration is a starting point only; it has not been verified end-to-end on the current PHP runtime.

## Required setup

1. Provision a hosted MySQL database and import the project's schema/data.
2. Configure DB_HOST, DB_USER, DB_PASS, and DB_NAME in the hosting environment. Use a restricted database user and never commit credentials.
3. Set BASE_URL to the canonical public URL, including the trailing slash, for example https://your-domain.example/.
4. Set CI_ENV=production in the deployment environment.
5. Deploy, then test product/category browsing, registration, login/logout, session persistence, cart updates, and the complete payment return/cancel flow.

## Production blockers / limitations to verify

- This is legacy CodeIgniter 3 code using a community PHP runtime on Vercel. Test every route and review function logs; a successful build alone is not a runtime test.
- Vercel's function filesystem is ephemeral. Do not rely on local disk for uploads, sessions, or other persistent state.
- Confirm payment credentials are supplied through environment variables and that payment is verified server-side before marking orders as paid. Never treat a browser redirect as proof of payment.
- Use HTTPS and verify the database provider's TLS requirements.
- Run an end-to-end test with PayPal sandbox credentials before accepting real payments.