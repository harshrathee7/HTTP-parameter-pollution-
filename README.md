### HTTP Parameter Pollution (HPP)
HTTP Parameter Pollution (HPP) is a web vulnerability where an attacker manipulates HTTP request parameters by injecting multiple instances of the same parameter. This can lead to security bypasses, authentication flaws, logic manipulation, and even WAF evasion.  

---

## 1. Types of HPP  

### 1️) Client-Side HPP  
Occurs when multiple parameters are injected into a URL, and the browser or frontend application processes them incorrectly.  

#### Example:  
A victim clicks a crafted link with multiple `role` parameters:  
```
https://example.com/login?user=admin&role=user&role=admin
```
-> If the application does not properly validate the parameters, it might grant the attacker **admin privileges** instead of user access.  

 >> Use case: Bypassing security restrictions or forcing unintended behavior in the frontend.

---

### **2️) Server-Side HPP**  
Occurs when an attacker sends multiple identical parameters in a request, causing unexpected behavior on the server.  

#### **Example:**  
An API endpoint processes a `transfer` request:  
```
POST /transfer HTTP/1.1  
Host: bank.com  
Content-Type: application/x-www-form-urlencoded  

amount=1000&currency=USD&currency=INR
```
-> If the server incorrectly handles multiple parameters, it might process the **last value (INR)** or merge values, resulting in **unexpected financial transactions**.  

 >**Use case:** Exploiting parameter merging logic to override security mechanisms.

---

## **2. HPP Attack Techniques**  

### WAF Evasion
- Web Application Firewalls (WAFs) often block specific attack payloads.
- Injecting extra parameters can bypass detection.  

#### **Example:**  
A normal request blocked by WAF:
```
https://example.com/search?q=<script>alert(1)</script>
```
A manipulated request using HPP to bypass WAF:
```
https://example.com/search?q=<script>&q=alert(1)</script>
```
-> If the WAF only scans the first `q` parameter, the attack **bypasses security filters** and executes XSS.  

---

### Authentication Bypass
- Some authentication systems rely on a single `role` parameter.
- Injecting multiple `role` parameters may allow privilege escalation.  

#### **Example:**  
```
POST /auth  
username=admin&role=user&role=admin
```
-> If the application picks the last parameter, the attacker gains **admin access**.

---

### API Parameter Exploitation
- REST APIs often merge parameters.
- Attackers can override security-related parameters.  

#### **Example:**  
```
POST /update-profile  
email=attacker@example.com&email=admin@example.com
```
-> If the system does not properly handle duplicates, the attacker may change **another user’s email**.

---

## **3. Exploitation in Different Contexts**  

| **Context**         | **Vulnerable Case** | **Exploitation Impact** |
|----------------------|---------------------|-------------------------|
| Authentication       | Multiple `role` params | Privilege escalation |
| Web Application      | `order=1&order=2` | Order manipulation |
| Financial APIs       | `amount=100&amount=1000` | Fraudulent transactions |
| WAF Bypass          | `q=<script>&q=alert(1)` | XSS or SQL Injection |
| SSRF Exploitation   | `url=internal.com&url=evil.com` | Server-side request manipulation |

---

## **4. Mitigation Strategies**  

 **Sanitize and Validate Inputs:**  
- Allow only expected parameters.  
- Reject duplicate parameters in requests.  

 **Use Strong Parsing Mechanisms:**  
- Avoid automatic merging of parameters.  
- Ensure the backend processes parameters securely.  

 **Enforce Server-Side Security Measures:**  
- Implement **whitelisting** instead of **blacklisting**.  
- Log and monitor multiple occurrences of the same parameter.  

 **Use Secure Frameworks:**  
- Secure libraries such as **Spring Security** or **Django’s request handling** mitigate HPP risks.  

---

### **5. Real-World HPP Example: PayPal Vulnerability**  
A previous vulnerability in PayPal allowed an attacker to **modify transaction details** using multiple `currency` parameters. By injecting multiple values, an attacker could **manipulate exchange rates**, leading to financial fraud.  

---

### **Setting Up the HTTP Parameter Pollution (HPP) Lab**  

This guide will help you **host, test, and exploit** the vulnerable PHP web app.  

---

## **6. Install Required Tools**  

### **For Local Setup:**  
>> Install **PHP** (if not installed)  
   - Linux (Debian/Ubuntu):  
     ```bash
     sudo apt update && sudo apt install php -y
     ```
   - Windows: Download PHP from [php.net](https://www.php.net/downloads)  
   - macOS (Homebrew):  
     ```bash
     brew install php
     ```

>> Install **Burp Suite/ZAP Proxy** (for modifying HTTP requests)  
   - Download **Burp Suite Community Edition** from: [PortSwigger](https://portswigger.net/burp/communitydownload)  
   - Download **OWASP ZAP** from: [ZAP Website](https://www.zaproxy.org/download/)  

---

## **2️. Set Up the Vulnerable PHP App**  

1️) **Create a New Directory & Place the File**  
   ```bash
   mkdir hpp_lab && cd hpp_lab
   nano hpp_lab.php
   ```
   - Copy & paste the **vulnerable PHP code** (from the canvas).  
   - Save (`CTRL+X`, then `Y`, then `Enter`).  

2️) **Start the PHP Server**  
   ```bash
   php -S 0.0.0.0:8000
   ```
   - This runs the server on **http://localhost:8000**.  
   - Open a browser and go to **http://localhost:8000/hpp_lab.php**.  

---

## **3️. Exploiting HTTP Parameter Pollution (HPP)**  

### **Client-Side HPP Attack (Bypassing Login)**  
1️) Open your browser and visit:  
   ```
   http://localhost:8000/hpp_lab.php?user=attacker&role=user&role=admin
   ```
2️) If the app **does not properly handle multiple role parameters**, it may give **admin access** instead of user access.  

---

### **Server-Side HPP Attack (Transaction Manipulation)**  
1️) Open **Burp Suite** or **ZAP Proxy** and intercept a **POST request**:  
   ```plaintext
   POST /hpp_lab.php HTTP/1.1
   Host: localhost:8000
   Content-Type: application/x-www-form-urlencoded

   amount=1000&currency=USD&currency=INR
   ```
2️) Modify the `currency` parameter to have multiple values (`USD` and `INR`).  
3️) If the application does not handle duplicates properly, it may:  
   - Process **both currencies** incorrectly.  
   - Allow **currency manipulation** (e.g., sending **USD but processing INR**).  

---

## **4️. Additional Exploits**  
 **Bypass Web Application Firewalls (WAFs)**  
   - Some WAFs block `q=<script>alert(1)</script>`, but you can try:  
     ```
     http://localhost:8000/hpp_lab.php?q=<script>&q=alert(1)</script>
     ```
     - If WAF checks only the first `q`, this may trigger XSS.  

 **Modify API Behavior**  
   - Test an API endpoint with duplicate parameters:  
     ```
     http://localhost:8000/api/transfer?amount=5000&currency=USD&currency=INR
     ```
   - If vulnerable, it may incorrectly process **USD instead of INR**.  

---

### **Conclusion**  
HPP is a **powerful attack technique** that can bypass security controls, manipulate logic, and evade WAFs. Proper **input validation, secure parsing mechanisms, and API security** can prevent exploitation.  
