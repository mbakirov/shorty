# shorty
Url shortner

## Installation
1. Run `composer install` to install Symfony libs
2. Run PostgreSQL
3. Add `.env` file with `DATABASE_URL`
4. Run `symfony server:start`

## Usage
1. To add the link use POST-request `/v1/shortlink` with body `{"link": "xxxx", "status": 301}`
2. To redirect end-user to shorted link use root endpoint `/~dsada`