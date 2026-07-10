# attendance-app

勤怠管理システム

## 環境構築

##　Dockerビルド

```bash
git clone git@github.com:aokiaiko/attendance-app.git
docker-compose up -d --build
```

## laravel環境構築

```bash
docker-compose exec php bash
composer install
cp .env.example .env　　
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan test
```

## URL

- **開発環境** : http://localhost/
- **ユーザー登録** : http://localhost/register
- **phpMyAdmin** : http://localhost:8080/
- **MailHog** : http://localhost:8025

##　 使用技術

- PHP 8.1
- Laravel 8.83.8
- MySQL 8.0
- nginx 1.21.1

##　 ER図

![ER図](src/app/docs/er.png)

## テストアカウント

### 一般ユーザー

- **メールアドレス** : test@test.com
- **パスワード** : password

### 一般ユーザー(別アカウント)

- **メールアドレス** : test2@test.com　　　　　
- **パスワード**: password

### 管理者

- **メールアドレス** : admin@test.com
- **パスワード** : password

## 補足

・画面要件では同日付の申請が複数表示されていましたが、本アプリでは同一勤怠に対して承認待ち申請を複数登録できない仕様としています。

・一般ユーザーと管理者は同一認証基盤を使用している為、切替える際はログアウト後に各ログイン画面からログインとしています。
