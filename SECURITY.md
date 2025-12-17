# Security Policy

## Supported Versions

As this is a college portfolio project, only the current main branch is actively monitored for security considerations.

| Version | Supported          |
| ------- | ------------------ |
| 1.0.x   | ✅ Yes             |
| < 1.0   | ❌ No              |

## Reporting a Vulnerability

I take the security of ProHire seriously. If you discover a security vulnerability within this project (such as SQL injection or session management issues), please follow these steps:

1. **Do Not Open a Public Issue:** Please do not disclose vulnerabilities publicly on the GitHub Issues tracker to prevent potential exploitation.
2. **Contact the Developer:** Please report the vulnerability privately via the contact information provided on my GitHub profile.
3. **Include Details:** Provide a clear description of the vulnerability, steps to reproduce it, and the potential impact it could have on the user or company data.

## Security Best Practices for this Project

Since this project utilizes **PHP and MySQL**, it is designed with the following security considerations in mind:

- **Database Safety:** Users are encouraged to use prepared statements to prevent SQL injection attacks.
- **Session Security:** The project uses PHP session management to ensure users can only access their respective dashboards (Admin/Company/User).
- **Configuration Security:** The `config.php` file should be updated with local credentials and never shared publicly with real production passwords.

---
*Note: This project is for educational and portfolio purposes. Users should perform their own security audits before using it in a live production environment.*
