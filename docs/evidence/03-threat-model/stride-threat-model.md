# DVWA DevSecOps STRIDE Threat Model



## 1. System Scope



This threat model evaluates the containerised Damn Vulnerable Web Application (DVWA), its MariaDB database, Docker Compose network, source-code repository, and planned GitHub Actions DevSecOps pipeline.



The model is based on the system architecture documented in the DVWA DevSecOps System Architecture diagram.



## 2. Protected Assets



\- User credentials

\- Authentication sessions and cookies

\- MariaDB records

\- DVWA application source code

\- Application and database configuration

\- Docker images and containers

\- GitHub repository and commit history

\- GitHub Actions secrets

\- CI/CD workflow integrity

\- Security scan results and audit evidence



## 3. Trust Boundaries



### TB1 - User to Web Application Boundary



HTTP requests cross from the user's browser into the DVWA web container through host port 4280, which is mapped to container port 80.



### TB2 - Docker Internal Network Boundary



The DVWA web container communicates with the MariaDB container through the Docker Compose internal network using TCP port 3306.



### TB3 - Repository to CI/CD Pipeline Boundary



Source code pushed to the GitHub repository is processed by the GitHub Actions workflow, which builds and scans the application and its Docker image.



## 4. Attack Surface



\- DVWA login form

\- SQL Injection input fields

\- Stored XSS input fields

\- Command Injection input fields

\- File Inclusion functionality

\- Authentication session cookies

\- HTTP endpoint on 127.0.0.1:4280

\- Application configuration files

\- Docker image and dependencies

\- GitHub repository and workflow files

\- Environment variables and pipeline secrets



## 5. STRIDE Threat Register



| ID | Threat | STRIDE Category | Affected Component | Attack Scenario | Likelihood | Impact | Score | Initial Risk |

|---|---|---|---|---|---:|---:|---:|---|

| T1 | Brute-force authentication | Spoofing | DVWA login page | An attacker repeatedly guesses the administrator password and impersonates a legitimate user. | 3 | 3 | 9 | Critical |

| T2 | SQL Injection | Tampering, Information Disclosure | DVWA web application and MariaDB | Untrusted input alters a SQL query and returns or modifies unauthorised database records. | 3 | 3 | 9 | Critical |

| T3 | Stored Cross-Site Scripting | Tampering, Spoofing | DVWA application and user browser | An attacker stores malicious JavaScript that executes when another user opens the affected page. | 3 | 2 | 6 | High |

| T4 | OS Command Injection | Tampering, Elevation of Privilege | DVWA web container | Malicious shell operators are supplied to an input passed to the operating system, causing unauthorised commands to execute. | 3 | 3 | 9 | Critical |

| T5 | Sensitive information exposure | Information Disclosure | Configuration, repository and CI/CD pipeline | Credentials or connection details are exposed through committed files, logs or insecure workflow output. | 2 | 3 | 6 | High |

| T6 | Untraceable malicious actions | Repudiation | DVWA application and CI/CD pipeline | Insufficient logging allows users or attackers to deny login attempts, exploitation or unauthorised changes. | 2 | 2 | 4 | Medium |



## 6. Risk Rating Method



Risk Score = Likelihood x Impact



### Likelihood



\- 1 - Low: The threat is difficult or unlikely to occur.

\- 2 - Medium: The threat may occur when specific conditions exist.

\- 3 - High: The threat is straightforward or likely to be exploited.



### Impact



\- 1 - Low: Minor effect with limited security consequences.

\- 2 - Medium: Limited data exposure, user impact or service disruption.

\- 3 - High: Serious data exposure, system compromise or major disruption.



### Risk Levels



\- Score 1-2: Low

\- Score 3-4: Medium

\- Score 6: High

\- Score 9: Critical



## 7. Threat-to-Control Mapping



| ID | Planned Security Control | Control Location |

|---|---|---|

| T1 | Rate limiting, temporary lockout, secure password handling and authentication logging | DVWA authentication code |

| T2 | Parameterised queries, prepared statements and server-side input validation | SQL Injection PHP source code |

| T3 | Context-aware output encoding, input validation and Content Security Policy | Stored XSS PHP source code and response headers |

| T4 | Strict allowlist validation and removal of unsafe shell-command construction | Command Injection PHP source code |

| T5 | Environment variables, .gitignore, GitHub encrypted secrets and Gitleaks scanning | Configuration files and GitHub Actions workflow |

| T6 | Security event logging, Git commit history and GitHub Actions audit records | Application logging and CI/CD platform |



## 8. Risk Justification



T1 is rated Critical because the login endpoint is directly accessible and repeated password guessing may allow an attacker to impersonate an administrator.



T2 is rated Critical because unsafe SQL construction can be exploited easily and may expose or modify sensitive database records.



T3 is rated High because malicious JavaScript can be stored and executed in another user's browser, although its impact is generally limited to the affected user's session and browser context.



T4 is rated Critical because successful command injection may execute operating-system commands inside the web container and compromise the application environment.



T5 is rated High because exposed credentials may provide access to protected services, repositories or pipeline resources.



T6 is rated Medium because missing audit evidence does not directly compromise the system but makes incident investigation, accountability and recovery significantly more difficult.

