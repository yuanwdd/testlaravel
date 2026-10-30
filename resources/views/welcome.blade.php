<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <style>
        body { margin: 0; padding: 24px; min-height: 100vh; box-sizing: border-box; display: grid; place-items: center; background: #f5f7fa; color: #182338; font-family: system-ui, sans-serif; }
        main { width: min(100%, 640px); box-sizing: border-box; padding: 40px; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; }
        .status { color: #16703c; font-weight: 600; }
        h1 { font-size: 32px; margin: 16px 0; }
        p { line-height: 1.8; }
        code { background: #edf2f7; padding: 3px 6px; border-radius: 4px; }
        a { color: #2157a5; }
    </style>
</head>
<body>
    <main>
        <div class="status">應用程式已啟動</div>
        <h1>Laravel 13</h1>
        <p>{{ config('app.name') }} 已成功回應請求。</p>
        <p>修改此頁文字並推送至 GitHub，再確認 Coolify 自動部署後是否顯示更新內容。</p>
        <p>測試版本：<code>deploy-test-001</code></p>
        <a href="/up">查看健康檢查</a>
    </main>
</body>
</html>
