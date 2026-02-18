# HILOTEC Website — Documentation Index

This is the complete handover documentation package for the HILOTEC corporate website. It covers everything from initial setup to long-term maintenance, organized into numbered documents for easy reference.

**Audience:** HILOTEC Engineering + Consulting AG team (IT infrastructure professionals)

**Project:** Laravel 12 + Filament 4 + Tailwind CSS 4 + Alpine.js corporate website

---

## Documents

| #  | Document | Description | For |
|----|----------|-------------|-----|
| 00 | [Quick Start Guide](00-GETTING-STARTED.md) | Get the project running locally in under 5 minutes. Covers the bare minimum: clone, install, serve. | Everyone |
| 01 | [Local Development Environment](01-DEVELOPMENT-SETUP.md) | Detailed development setup including IDE configuration, recommended workflow, and branch strategy. | Developers |
| 02 | [Production Deployment Guide](02-DEPLOYMENT.md) | Full server setup with Nginx/Apache configuration, SSL certificates, deploy script, and GDPR-compliant self-hosted fonts. | Sysadmins |
| 03 | [Admin Panel User Guide](03-ADMIN-GUIDE.md) | How to manage website content through the Filament admin panel — pages, settings, images, and FAQ entries. | Content Editors |
| 04 | [Technical Architecture & Learning Guide](04-TECHNICAL.md) | Deep dive into the codebase: MVC structure, Eloquent models, controllers, database schema, and Filament resources. | Developers |
| 05 | [Design System Reference](05-DESIGN-SYSTEM.md) | Colors, typography, Blade components, Alpine.js interactions, and responsive design patterns used across the site. | Developers |
| 06 | [SEO Documentation](06-SEO.md) | Meta tag strategy, sitemap generation, structured data markup, and performance optimization for search engines. | Developers |
| 07 | [Ongoing Maintenance Guide](07-MAINTENANCE.md) | Routine maintenance tasks: dependency updates, log management, monitoring setup, and health check endpoints. | Sysadmins |
| 08 | [Backup & Disaster Recovery](08-BACKUP-RECOVERY.md) | Backup scripts, restore procedures, and incident response playbook for the production environment. | Sysadmins |
| 09 | [Security Considerations](09-SECURITY.md) | Middleware configuration, server hardening, GDPR compliance, and common attack mitigations. | Sysadmins |
| 10 | [Design Versions Comparison](10-BRANCH-COMPARISON.md) | Side-by-side comparison of the `master` and `design-v2` branches, with a recommended merge strategy. | Decision Makers |

---

## Quick Navigation

Find the right document based on what you need to do:

| I want to...                          | Read                                                                          |
|---------------------------------------|-------------------------------------------------------------------------------|
| Set up the project locally            | [00 — Quick Start](00-GETTING-STARTED.md), [01 — Dev Setup](01-DEVELOPMENT-SETUP.md) |
| Deploy to production                  | [02 — Deployment](02-DEPLOYMENT.md)                                           |
| Manage website content                | [03 — Admin Guide](03-ADMIN-GUIDE.md)                                         |
| Understand the codebase               | [04 — Technical](04-TECHNICAL.md), [05 — Design System](05-DESIGN-SYSTEM.md)  |
| Maintain the site long-term           | [07 — Maintenance](07-MAINTENANCE.md), [08 — Backup](08-BACKUP-RECOVERY.md), [09 — Security](09-SECURITY.md) |
| Improve SEO                           | [06 — SEO](06-SEO.md)                                                         |
| Decide which design version to use    | [10 — Branch Comparison](10-BRANCH-COMPARISON.md)                             |

---

## Note on Previous Documentation

The following files in this directory are from an earlier documentation effort and have been **superseded** by the numbered documents above:

- `TECHNICAL.md` — replaced by [04-TECHNICAL.md](04-TECHNICAL.md)
- `ADMIN_GUIDE.md` — replaced by [03-ADMIN-GUIDE.md](03-ADMIN-GUIDE.md)
- `DESIGN_SYSTEM.md` — replaced by [05-DESIGN-SYSTEM.md](05-DESIGN-SYSTEM.md)
- `DEPLOYMENT.md` — replaced by [02-DEPLOYMENT.md](02-DEPLOYMENT.md)

These older files are retained for reference but should not be used as the primary source of truth. Refer to the numbered documents for up-to-date information.
