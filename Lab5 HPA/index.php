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


<hr>
<h3>Lab5 - HPA Live Resource</h3>
<div>CPU: <span id="cpu">-</span></div>
<div>MEM: <span id="mem">-</span></div>

<script>
let last = null;

function fmtBytes(b){
  if (b == null) return "-";
  const units = ["B","KiB","MiB","GiB"];
  let i=0; let v=b;
  while (v>=1024 && i<units.length-1){ v/=1024; i++; }
  return v.toFixed(1)+" "+units[i];
}

async function tick(){
  const res = await fetch("/metrics.php", {cache:"no-store"});
  const cur = await res.json();

  // memory percent vs limit
  const memCur = cur.mem_current_bytes;
  const memLimit = cur.mem_limit_bytes ? Number(cur.mem_limit_bytes) : (cur.mem_max_bytes ?? null);
  let memText = `${fmtBytes(memCur)} / ${fmtBytes(memLimit)}`;
  if (memCur != null && memLimit != null && memLimit > 0) {
    const p = (memCur / memLimit) * 100;
    memText += ` (${p.toFixed(1)}%)`;
  }

  // cpu usage percent: compute delta usage over delta time, normalize by cpu limit
  // cpu_usage_usec increases over time; usageRate = dUsage / dTime
  let cpuText = "-";
  if (last && cur.cpu_usage_usec != null && last.cpu_usage_usec != null) {
    const du = cur.cpu_usage_usec - last.cpu_usage_usec;     // usec
    const dt = (cur.ts_ms - last.ts_ms) * 1000;              // ms -> usec
    if (dt > 0 && du >= 0) {
      const coresUsed = du / dt; // 1.0 = 1 core
      const cpuLimitM = cur.cpu_limit_m ? Number(cur.cpu_limit_m) : null;
      if (cpuLimitM) {
        const limitCores = cpuLimitM / 1000.0;
        const pct = (coresUsed / limitCores) * 100;
        cpuText = `${coresUsed.toFixed(2)} cores / ${limitCores.toFixed(2)} (${pct.toFixed(1)}%)`;
      } else {
        cpuText = `${coresUsed.toFixed(2)} cores`;
      }
    }
  }

  document.getElementById("mem").textContent = memText;
  document.getElementById("cpu").textContent = cpuText;

  last = cur;
}

setInterval(tick, 1000);
tick();
</script>

<h3>壓力測試</h3>

<iframe name="stressFrame" style="display:none;"></iframe>

<form method="POST" action="/stress.php" target="stressFrame">
  <button name="on" value="1">Stress CPU (100s)</button>
</form>

<p style="color:#666;">按下後不要離開頁面，看上面的 CPU% 變化。</p>

</body></html>
