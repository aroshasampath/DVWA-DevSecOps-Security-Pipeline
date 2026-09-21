# DVWA DevSecOps Security Pipeline

## Project Overview

This project implements a DevSecOps security pipeline for the Damn Vulnerable Web Application (DVWA). The application is deployed locally using Docker Compose and is protected by automated security checks in GitHub Actions.

The project demonstrates secure coding remediation, static application security testing, dependency scanning, secret scanning, and container image scanning.

## Architecture

The environment contains:

- Browser client accessing DVWA at http://127.0.0.1:4280
- DVWA PHP web application running in a Docker container
- MariaDB database running in a separate Docker container
- GitHub repository and GitHub Actions pipeline
- Security tools: Semgrep, Trivy, and Gitleaks

Architecture evidence is available in:

docs/evidence/02-architecture/

## Threat Modelling

A STRIDE threat model was created to identify security risks affecting the DVWA application, Docker environment, source repository, and CI pipeline.

The key threat categories considered were:

- Spoofing
- Tampering
- Repudiation
- Information Disclosure
- Denial of Service
- Elevation of Privilege

Threat-model evidence is available in:

docs/evidence/03-threat-model/

## Security Vulnerabilities Remediated

Four vulnerabilities were tested, remediated, and validated.

| Vulnerability | Affected File | Security Remediation |
| --- | --- | --- |
| SQL Injection | vulnerabilities/sqli/source/low.php | Input validation and safe database query handling |
| Stored XSS | vulnerabilities/xss_s/index.php | Output encoding of stored untrusted data |
| Command Injection | vulnerabilities/exec/source/low.php | IP validation and safe shell argument handling |
| Reflected XSS | vulnerabilities/xss_r/source/low.php | HTML output encoding using htmlspecialchars() |

For each vulnerability, the repository includes:

- Vulnerable behavior evidence
- Exploit proof in the local controlled DVWA environment
- Before and after source-code copies
- Secure remediation evidence
- Functional normal-input validation
- SAST comparison results

Evidence is stored in:

- docs/evidence/05-sql-injection/
- docs/evidence/06-stored-xss/
- docs/evidence/07-command-injection/
- docs/evidence/08-reflected-xss/

## DevSecOps Pipeline

The GitHub Actions workflow is located at:

.github/workflows/devsecops-pipeline.yml

The workflow runs automatically on every push and includes the following security gates:

1. Build and PHP Validation
   - Validates the Docker Compose configuration.
   - Builds the DVWA Docker image.
   - Runs PHP syntax checks on the four remediated files.

2. SAST - Custom Semgrep Rules
   - Uses the custom rules in security/semgrep-ci.yml.
   - Blocks direct untrusted HTTP request data from reaching HTML output or shell commands.

3. Dependency Vulnerability Scan
   - Uses Trivy filesystem scanning to identify known dependency vulnerabilities.

4. Secret Scan
   - Uses Gitleaks to detect accidentally committed secrets, tokens, or credentials.

5. Container Image Scan
   - Uses Trivy to scan the built DVWA container image for vulnerabilities.

## Security Gate Demonstration

A controlled temporary branch named demo/sast-blocking-failure was used to demonstrate that the SAST gate blocks insecure code.

An intentionally unsafe direct request output was introduced only in that temporary branch. Semgrep detected the issue as:

security.no-direct-request-to-html

The GitHub Actions job failed with a blocking finding and exit code 1. The temporary branch was deleted after collecting the evidence. The secure main branch remained unchanged and passed all pipeline gates.

Pipeline evidence is available in:

docs/evidence/09-devsecops-pipeline/

## Running the Application Locally

Start the containers:

    docker compose up -d --build

Verify running services:

    docker compose ps

Open DVWA:

    http://127.0.0.1:4280

Stop the environment when finished:

    docker compose down

## Local SAST Validation

Run the custom Semgrep rules against the remediated files:

    docker run --rm -v "${PWD}:/src" -w /src semgrep/semgrep semgrep scan --error --config security/semgrep-ci.yml vulnerabilities/sqli/source/low.php vulnerabilities/xss_s/index.php vulnerabilities/xss_r/source/low.php vulnerabilities/exec/source/low.php

A secure implementation produces zero blocking findings.

## Repository Evidence

All project evidence is organised under:

docs/evidence/

The evidence includes setup validation, architecture, threat modelling, SAST baseline results, vulnerability remediation proof, and CI/CD pipeline results.

## Ethical Use

DVWA is intentionally vulnerable and was used only in a local controlled Docker environment for academic security testing. No testing was performed against unauthorised external systems.