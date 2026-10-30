# testlaravel

Laravel 13 專案，用於測試 GitHub → Coolify 自動部署。使用 PHP 8.5 + Apache，容器監聽 **80**，健康檢查為 **`/up`**。

首頁以 Blade 與內嵌 CSS 呈現，不需要 Node.js 或前端編譯。Session、cache 使用檔案，queue 使用同步模式；首次部署不用建立資料庫。

## 本機啟動

需要 PHP 8.5（含 Laravel 所需擴充）及 Composer 2。

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

開啟 http://localhost:8000 ，健康檢查為 http://localhost:8000/up 。既有 `.env` 請保留，不要重複覆蓋或重新產生正式環境金鑰。

```bash
php artisan test
vendor/bin/pint --test
composer validate --strict
```

`composer setup` 另會建立本機 SQLite 資料庫並執行預設 migrations；僅測試首頁不必執行。`composer dev` 可啟動本機開發伺服器。

## 推送 GitHub

本資料夾已有 Git repository，可將新增檔案提交至目前的 `main` 分支：

```bash
git add .
git commit -m "Set up Laravel 13 for Coolify deployment"
git push origin main
```

請一併提交 `composer.lock`。`.env`、`vendor/`、SQLite 檔案與執行期資料已排除於 Git；Docker build 也排除本機 `.env`。

## Coolify 設定

1. 在 Coolify 建立 Application，透過 **GitHub App** 連接此 repository，選擇 `main` 分支。
2. 填寫以下設定（不同 Coolify 版本的欄位名稱可能略有差異）：

   | 設定 | 值 |
   | --- | --- |
   | Build Pack / Build Strategy | `Dockerfile` |
   | Base Directory | `/` |
   | Dockerfile Location | `/Dockerfile`（repository 根目錄） |
   | Ports Exposes | `80` |
   | Domains | 你的測試網址，例如 `https://laravel.example.com` |
   | Health Check | HTTP、port `80`、path `/up`、預期狀態 `200` |

3. 將 `.env.coolify.example` 的設定填入 Coolify **Environment Variables**，啟用執行期注入。修改 `APP_URL` 為實際網址，保持 `APP_DEBUG=false`。
4. 在本機執行以下指令，將輸出的金鑰填入 Coolify 的 `APP_KEY`，不要提交金鑰到 GitHub：

   ```bash
   php artisan key:generate --show
   ```

   此指令只顯示新金鑰，不修改本機 `.env`。同一部署環境應持續使用同一個 `APP_KEY`，不要在每次部署時重新產生。容器缺少 `APP_KEY` 會停止啟動並記錄提示。

5. 若測試網址只有 HTTP，先將 `SESSION_SECURE_COOKIE=false`；使用 HTTPS 時設為 `true`。
6. 點選 **Deploy**，確認首頁與 `/up` 均正常。
7. 在 **Advanced → Deployment → Auto deploy** 選擇 **Deploy on push (webhooks)**（舊版介面可能稱為 Auto Deploy）。GitHub App 整合會處理 webhook。

若使用 Public Repository 或 Deploy Key 來源，請依 Coolify 的 **Manual Git Webhooks** 說明，在 GitHub repository 設定 webhook；僅填 repository URL 不代表已完成自動部署設定。

### 驗證自動部署

修改 `resources/views/welcome.blade.php` 中的 `deploy-test-001`，例如改成 `deploy-test-002`，再 commit / push 至 `main`。在 Coolify Deployment Logs 確認此次 commit 已部署，重新整理首頁確認版本已更新。

GitHub Actions 會執行 Laravel 測試、格式檢查、套件安全檢查，以及 Docker 建置與 HTTP smoke test。**Coolify 的 Deploy on push 與 GitHub Actions 各自觸發，不會等待 CI 通過才部署。** 此設定用於測試自動部署；若之後要以 CI 通過作為部署條件，需改為 CI 成功後呼叫 Coolify Deploy Webhook。

## 儲存與資料庫

此預設適合單一容器的部署測試。未掛載持久儲存時，重新部署會清除檔案 session、cache 與上傳檔案。若要保留，可在 Coolify 掛載 persistent volume 到 `/var/www/html/storage`。日誌輸出至 stderr，可直接在 Coolify 查看。

專案保留 Laravel 的 SQLite / MySQL 設定、Model 與 migrations，容器也含對應 PDO 擴充。需要資料庫時，再設定 `DB_*` 並於容器執行 `php artisan migrate --force`；容器啟動時不會自動執行 migrations。SQLite 資料庫也需要獨立的持久化路徑。此範本沒有背景 queue worker 或排程服務。

## 本機測試 Docker

先啟動 Docker Desktop，再執行：

```bash
docker build -t testlaravel .
# 使用專供本機測試的金鑰，勿使用正式環境金鑰。
app_key="$(php artisan key:generate --show --no-ansi)"
docker run --rm -p 127.0.0.1:8080:80 \
  -e APP_KEY="$app_key" \
  -e APP_URL=http://localhost:8080 \
  testlaravel
```

開啟 http://localhost:8080 。Docker 使用 Apache 提供服務，並在啟動時產生 config、route 與 view cache，確保採用 Coolify 注入的環境變數。

## 官方參考

- [Laravel 13 部署文件](https://laravel.com/framework/docs/13.x/deployment)
- [Coolify Dockerfile](https://coolify.io/docs/applications/builds/dockerfile)
- [Coolify 自動部署](https://coolify.io/docs/applications/deployments/automatic-deployments)
- [Coolify 手動 Git Webhooks](https://coolify.io/docs/applications/deployments/manual-git-webhooks)
