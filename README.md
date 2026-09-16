<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Super Admin Dashboard

The public LMS UI is available at `/`. The separate backend portal is available at `/admin/login` and `/admin/dashboard`.

### Setup

1. Install PHP dependencies and configure MongoDB in `.env`.
2. Install frontend dependencies with `npm install`.
3. Build the assets with `npm run build`, or use `npm run dev` while developing.
4. Set `SUPER_ADMIN_EMAIL`, `SUPER_ADMIN_PASSWORD`, and `SUPER_ADMIN_NAME` in `.env`.
5. Run `php artisan migrate --seed` to backfill existing users as students and create the configured Super Admin.
6. Start Laravel with `php artisan serve`, then open `/admin/login`.
7. The portal login posts to `/api/v1/admin/login`; normal student and teacher accounts receive `403 Forbidden`.

### Frontend state

The dashboard is a Blade entry point with a small vanilla JavaScript application. The Sanctum token is stored in `localStorage` under `admin_sanctum_token`; the cached profile is stored under `admin_profile` so the header can render immediately. Dashboard data is loaded from `/api/v1/admin/dashboard` and the versioned admin resources. The reusable Axios client adds `Authorization: Bearer {token}` to every admin API request.

### Adding a page

Add a navigation link with a `data-section` value in `resources/views/dashboard.blade.php`, then add a section with the same `id`. Add the relevant `api.get`, render function, and event delegation in `resources/js/app.js`. The existing users and courses tables show the pattern for collection normalization, escaped output, and delete actions.

### Sanctum token lifecycle

The login response returns `{ user, token }`. The token is saved after a successful `/api/v1/admin/login` request and attached to protected admin API requests by the Axios request interceptor. A missing token redirects `/admin/dashboard` to `/admin/login`; any `401` or `403` response clears both local storage values and redirects to `/admin/login`. Logout calls `/api/v1/admin/logout` to revoke the current token, then clears local storage even if the network request fails. Laravel's `superadmin` middleware is the actual authorization boundary.
