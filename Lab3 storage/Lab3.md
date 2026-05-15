# Lab3 Storage(PV, PVC) / StatefulSet
---

## 一、Build image

### 1. 修改 [`index.php`](index.php)

接續之前的檔案，在下方新增 Lab3 的內容。

```bash
cd web
vim index.php
```

![編輯 index.php](img/3.png)

### 2. Build & Push image

```bash
docker build -t k8slab:lab3 .
docker tag k8slab:lab3 {repo}:lab3
docker push {repo}:lab3
```

![Build & Push image](img/4.png)

---

## 二、部署 Namespace / Service / Deployment

### 1. 建立工作目錄並撰寫 YAML

```bash
mkdir lab3
cd lab3
vim lab3-namespace.yaml
vim lab3-svc.yaml
```

對應的 YAML 檔案：

- [`lab3-namespace.yaml`](yaml/lab3-namespace.yaml)
- [`lab3-svc.yaml`](yaml/lab3-svc.yaml)

![lab3-namespace.yaml](img/6-1.png)
![lab3-svc.yaml](img/6-2.png)
![檔案列表](img/6-3.png)

### 2. 撰寫 Deployment YAML


vim [`lab3-deploy.yaml`](yaml/lab3-deploy.yaml)

![lab3-deploy.yaml](img/7.png)

### 3. 套用 YAML

```bash
kubectl apply -f lab3-namespace.yaml
kubectl apply -f lab3-svc.yaml
kubectl apply -f lab3-deploy.yaml
kubectl get all -n lab3
```

![kubectl apply](img/8-1.png)
![kubectl get all](img/8-2.png)

### 4. 瀏覽器存取

```
http://{masterip}:30083
```

> **截圖一**：需要截圖網址、Lab3 寫入畫面

![web](img/9.png)

### 5. 測試 Pod 重建

```bash
kubectl delete pod {截圖一的 pod 名稱} -n lab3
kubectl get pod -n lab3
```

回到 web 刷新，觀察 Deployment 自動重建 Pod。

> **截圖二**：需要截圖新建的 pod、Lab3 寫入畫面

![kubectl delete pod](img/10-1.png)
![新的 Pod 與 web 畫面](img/10-2.png)

---

## 三、部署 PVC

### 1. PVC / Storage 概念

![PVC 概念](img/12.png)

![Kubernetes StorageClass](img/13.png)

### 2. 撰寫並套用 PVC

```bash
vim lab3-pvc.yaml
kubectl apply -f lab3-pvc.yaml
kubectl get pvc -n lab3

kubectl apply -f https://raw.githubusercontent.com/rancher/local-path-provisioner/master/deploy/local-path-storage.yaml
kubectl get sc
```

對應的 YAML 檔案：[`lab3-pvc.yaml`](yaml/lab3-pvc.yaml)

![lab3-pvc.yaml](img/14-1.png)
![kubectl apply pvc / sc](img/14-2.png)

### 3. 將 Deployment 掛載到 PVC

```bash
cp lab3-deploy.yaml lab3-deploy-pvc.yaml
vim lab3-deploy-pvc.yaml      # 掛載到 pvc 上
kubectl apply -f lab3-deploy-pvc.yaml
```

對應的 YAML 檔案：[`lab3-deploy-pvc.yaml`](yaml/lab3-deploy-pvc.yaml)

![lab3-deploy-pvc.yaml](img/15-1.png)
![apply deploy-pvc](img/15-2.png)

### 4. 瀏覽器存取並驗證

```bash
# 瀏覽器存取
http://{masterip}:30083

kubectl get pvc -n lab3
```

> **截圖三**：`{學號}-pvc` 成功綁定畫面

![PVC 綁定](img/16-1.png)

> **截圖四**：web 寫入畫面同時存在兩個 pod

![web 畫面](img/16-2.png)

---

## 四、部署 StatefulSet

### 1. StatefulSet 概念

![StatefulSet 概念](img/18.png)

### 2. 撰寫 YAML

```bash
vim lab3-stateful.yaml
vim lab3-headless-svc.yaml
```

對應的 YAML 檔案：

- [`lab3-stateful.yaml`](yaml/lab3-stateful.yaml)
- [`lab3-headless-svc.yaml`](yaml/lab3-headless-svc.yaml)

![lab3-stateful.yaml](img/19-1.png)
![lab3-headless-svc.yaml](img/19-2.png)

### 3. 套用 YAML

```bash
kubectl apply -f lab3-headless-svc.yaml
kubectl apply -f lab3-stateful.yaml
kubectl get pod -n lab3
kubectl get pvc -n lab3
```

![apply stateful / 觀察 pod 與 pvc](img/20.png)

### 4. 用 port-forward 存取 StatefulSet Pod

```bash
kubectl port-forward pod/lab3-11477027-stateful-0 -n lab3 30081:80 --address 0.0.0.0

# 瀏覽器存取
http://{masterip}:30081
```

![port-forward 存取](img/21.png)

### 5. 測試 StatefulSet Pod 重建

```bash
kubectl delete pod lab3-11477027-stateful-0 -n lab3
kubectl port-forward pod/lab3-11477027-stateful-0 -n lab3 30081:80 --address 0.0.0.0

# 瀏覽器存取
http://{masterip}:30081
```

刪除後 StatefulSet 會以**相同名稱**重建 Pod，且資料仍透過 PVC 保留。
![刪除後重建](img/22-1.png)
> **截圖五**：web 寫入畫面同時存在兩個同名不同 IP 的 pod

![web 同名 Pod 畫面](img/22-2.png)

