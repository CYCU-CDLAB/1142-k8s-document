<?php
date_default_timezone_set('Asia/Taipei');

$pod = getenv('POD_NAME');
$podip = getenv('POD_IP');
$ns = getenv('POD_NAMESPACE');
$node = getenv('NODE_NAME');
$nodeip = getenv('NODE_IP');
$now = date("Y-m-d H:i:s");
$appVer = getenv('APP_VERSION');
?>


<html><body>
<h2>K8s Teaching Web</h2>

<ul>
  <li><b>Pod:</b> <?= htmlspecialchars($pod) ?></li>
  <li><b>Pod IP:</b> <?= htmlspecialchars($podip) ?></li>
  <li><b>Namespace:</b> <?= htmlspecialchars($ns) ?></li>
  <li><b>Node:</b> <?= htmlspecialchars($node) ?></li>
  <li><b>Node IP:</b> <?= htmlspecialchars($nodeip) ?></li>
  <li><b>Time:</b> <?= htmlspecialchars($now) ?></li>
  <li><b>App Version:</b> <?= htmlspecialchars($appVer) ?></li>
</ul>

<p>Tip: 重新整理頁面，觀察分流（不同 Pod/Node）。</p>

<!-- lab4 -->
<?php
function readCfg($path, $default) {
  $v = @file_get_contents($path);
  if ($v === false) return $default;
  $v = trim($v);
  return $v === "" ? $default : $v;
}

$title  = readCfg("/config/TITLE", "Default Title");
$bg     = readCfg("/config/BG_COLOR", "#ffffff");
$notice = readCfg("/config/NOTICE", "No notice");
?>

<hr>
<div style="padding:12px; border:1px solid #ddd; border-radius:10px; background: <?= htmlspecialchars($bg) ?>;">
  <h2><?= htmlspecialchars($title) ?></h2>
  <p><?= htmlspecialchars($notice) ?></p>
  <small style="color:#666;">(來源：/config/* 由 ConfigMap 掛載)</small>
</div> 

</body></html>

