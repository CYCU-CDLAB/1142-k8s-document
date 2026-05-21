<?php
date_default_timezone_set('Asia/Taipei');

$pod = getenv('POD_NAME');
$podip = getenv('POD_IP');
$ns = getenv('POD_NAMESPACE');
$node = getenv('NODE_NAME');
$nodeip = getenv('NODE_IP');
$now = date("Y-m-d H:i:s");
$appVer = getenv('APP_VERSION') ?: 'v1'; // 預設給 v1

// 抓取 Gateway 相關的 HTTP 請求資訊
$requestUri = $_SERVER['REQUEST_URI'];
$clientIp = $_SERVER['REMOTE_ADDR'];
$xForwardedFor = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '無 (Direct)';

// 根據 App 版本決定背景顏色，產生強烈的視覺對比
$bgColor = "#ffffff"; // 預設白色
$themeColor = "#333333";
if (strtolower($appVer) === "v1") {
    $bgColor = "#e6f7ff"; // 淺藍色
    $themeColor = "#0050b3";
} elseif (strtolower($appVer) === "v2") {
    $bgColor = "#f6ffed"; // 淺綠色
    $themeColor = "#389e0d";
}
?>

<html>
<body style="background-color: <?= htmlspecialchars($bgColor) ?>; transition: background-color 0.3s ease;">
<h2 style="color: <?= htmlspecialchars($themeColor) ?>;">K8s Gateway Teaching Web (Version: <?= htmlspecialchars($appVer) ?>)</h2>

<div style="background: rgba(255,255,255,0.7); padding: 15px; border-radius: 8px; border: 1px solid #ccc; margin-bottom: 20px;">
  <h3>Gateway 流量觀察區</h3>
  <ul>
    <li><b>實際收到路徑 (Received Path):</b> <code style="background:#eee; padding:2px 6px; color:red; font-size: 1.1em;"><?= htmlspecialchars($requestUri) ?></code></li>
    <li><b>X-Forwarded-For:</b> <?= htmlspecialchars($xForwardedFor) ?></li>
    <li><b>Pod 直接看到的來源 IP:</b> <?= htmlspecialchars($clientIp) ?></li>
  </ul>
</div>

<h3>Pod 基本資訊</h3>
<ul>
  <li><b>Pod:</b> <?= htmlspecialchars($pod) ?></li>
  <li><b>Pod IP:</b> <?= htmlspecialchars($podip) ?></li>
  <li><b>Namespace:</b> <?= htmlspecialchars($ns) ?></li>
  <li><b>Node:</b> <?= htmlspecialchars($node) ?></li>
  <li><b>Node IP:</b> <?= htmlspecialchars($nodeip) ?></li>
  <li><b>Time:</b> <?= htmlspecialchars($now) ?></li>
  <li><b>App Version:</b> <strong style="color: <?= htmlspecialchars($themeColor) ?>; font-size: 1.2em;"><?= htmlspecialchars($appVer) ?></strong></li>
</ul>

<p>Tip: 重新整理頁面，觀察分流與背景顏色變化。</p>

</body></html>