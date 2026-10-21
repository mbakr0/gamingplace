# GamingPlace

GamingPlace is a gaming e-commerce storefront built with **PHP, CodeIgniter 3, MySQL, HTML, CSS, and vanilla JavaScript**. The redesigned experience uses cinematic game artwork, dark graphite surfaces, acid-lime accents, responsive layouts, and a focused shopping journey.

## Storefront features

- **Responsive storefront:** editorial hero, game collection, mobile navigation, and a cohesive footer.
- **Database-driven catalogue:** game names, artwork, descriptions, prices, and detail pages continue to come from the existing MySQL database.
- **Automatic database bootstrap:** creates the required missing tables on application startup without dropping existing tables or rows, then inserts the six starter games and starter categories if they are missing.
- **Collection tools:** instant title search, price-range filters, and sorting by price or name.
- **Product details:** dedicated game pages with artwork, description, quantity controls, and add-to-bag forms.
- **Shopping bag:** quantity updates, removal by setting quantity to zero, item subtotals, shipping, tax, and order summary.
- **Accounts:** registration, sign-in, sign-out, password hashing, and server-side validation through the existing CodeIgniter controllers.
- **PayPal Express Checkout:** server-to-server checkout requests, payment confirmation before saving an order, a cancellation path, and a recovery path for a confirmed payment whose order record could not be saved.
- **Responsive and accessible details:** semantic landmarks, keyboard-operable controls, descriptive labels, focus states, mobile layouts, and reduced-motion support.

## Technology

| Area | Implementation |
| --- | --- |
| Application | CodeIgniter 3 / PHP |
| Data | MySQL through CodeIgniter's Query Builder |
| Styling | Custom CSS layered over the existing Bootstrap stylesheet |
| Interactions | Vanilla JavaScript |
| Cart | CodeIgniter Cart library and session |
| Payments | PayPal Express Checkout NVP API |
| Deployment target | Vercel PHP runtime; local development with XAMPP |

## Local development with XAMPP

1. Place or clone the repository at `C:\xampp\htdocs\gamingplace`.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Create the MySQL database named `gamingplace` and import your existing database schema if you haven't already.
4. In phpMyAdmin, select the same database configured by `DB_NAME` in `application/config/database.php`, open **Import**, and import `database/seed_products.sql`. The seed adds six demo entries using existing image files; it can be run again without duplicating matching titles.
5. Open `http://localhost/gamingplace/`.

The seed SQL expects the `products` table to have the `title`, `description`, `price`, and `image` columns used by the app, with an auto-incrementing `id`. If your existing table has additional required columns without defaults, adjust the seed to match its structure shown in phpMyAdmin's **Structure** tab.

The application loads `.env.local` in the local front controller without overwriting environment variables already supplied by the host. Never commit `.env.local` or real credentials.

## Environment configuration

Use `.env.example` as a starting point for the required variable names. For local XAMPP development, keep real local values in `.env.local` (which the front controller reads) and never commit that file.

Database variables:

- `DB_HOST`
- `DB_USER`
- `DB_PASS`
- `DB_NAME`

PayPal variables:

- `PAYPAL_API_USERNAME`
- `PAYPAL_API_PASSWORD`
- `PAYPAL_API_SIGNATURE`
- `PAYPAL_MODE=sandbox` for testing, or `PAYPAL_MODE=live` after the full sandbox flow has been verified.

Also configure `CI_ENV=production` on the hosted deployment. Add secrets through Vercel's project environment settings; do not put payment secrets in source files or client-side JavaScript.

## Important checkout note

Checkout requires valid PayPal API credentials, PHP cURL, a reachable database, and the existing `orders` schema expected by the controller. Test the complete sandbox flow—including cancellation, payment confirmation, the recorded transaction, and retry/recovery behavior—before switching to live credentials. A provider redirect alone is not treated as payment confirmation.

The project uses a legacy CodeIgniter 3 architecture and a Vercel PHP runtime. Runtime compatibility, database connectivity, session persistence, and the deployed payment flow must be verified in the actual hosting environment before accepting real payments.

## Main application paths

- `application/controllers/Products.php` — collection and game details.
- `application/controllers/cart.php` — cart, checkout, payment return, and cancellation.
- `application/controllers/users.php` — registration and authentication.
- `application/models/Product_model.php` and `User_model.php` — database access.
- `application/views/` — storefront, details, cart, and registration views.
- `assets/css/custom.css` — storefront design system and responsive layouts.
- `assets/js/storefront.js` — mobile navigation, collection search, filters, sorting, and small UI interactions.
- `database/seed_products.sql` — six starter catalogue entries.

## Portfolio summary

A full storefront redesign that retains a legacy PHP application's database and MVC structure while refreshing the visual system, improving responsive behavior, introducing useful catalogue controls, and hardening payment handling around server-side confirmation.
