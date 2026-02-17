# HILOTEC Website -- CMS Admin Guide

This guide explains how to manage the content of the HILOTEC corporate website using the Filament admin panel. It is written for non-technical staff and covers every section of the CMS in detail.

---

## Table of Contents

1. [Logging In](#1-logging-in)
2. [Dashboard and Navigation Overview](#2-dashboard-and-navigation-overview)
3. [Content Management -- Inhalte](#3-content-management--inhalte)
   - [3.1 Services (Leistungen)](#31-services-leistungen)
   - [3.2 Posts (Beitraege)](#32-posts-beitraege)
   - [3.3 Pages (Seiten)](#33-pages-seiten)
   - [3.4 Team Members](#34-team-members)
   - [3.5 Partners](#35-partners)
4. [References Management -- Referenzen](#4-references-management--referenzen)
   - [4.1 Reference Categories (Kategorien)](#41-reference-categories-kategorien)
   - [4.2 References](#42-references)
5. [Contact Inquiries -- Anfragen](#5-contact-inquiries--anfragen)
6. [Settings -- Einstellungen](#6-settings--einstellungen)
   - [6.1 Allgemein (General) Tab](#61-allgemein-general-tab)
   - [6.2 Kontakt (Contact) Tab](#62-kontakt-contact-tab)
   - [6.3 Footer Tab](#63-footer-tab)
   - [6.4 Social Media Tab](#64-social-media-tab)
7. [Common Concepts](#7-common-concepts)
   - [Publishing and Visibility](#publishing-and-visibility)
   - [Slugs (URL Identifiers)](#slugs-url-identifiers)
   - [Sort Order and Drag-and-Drop Reordering](#sort-order-and-drag-and-drop-reordering)
   - [The Rich Text Editor](#the-rich-text-editor)
8. [Image Recommendations](#8-image-recommendations)
9. [Frequently Asked Questions (FAQ)](#9-frequently-asked-questions-faq)
10. [Troubleshooting](#10-troubleshooting)

---

## 1. Logging In

1. Open your web browser and navigate to your site's admin URL:
   ```
   https://www.hilotec.com/admin
   ```
2. You will see a login screen with two fields: **Email** and **Password**.
3. Enter your admin email address and password.
4. Click the **Sign in** button.

> **Important:** After your first login, change the default password immediately. You can do this via the user menu in the bottom-left corner of the admin panel.

> **Forgot your password?** Click the "Forgot your password?" link on the login screen to receive a password-reset email.

### What You See After Login

After a successful login you land on the **Dashboard**. The dashboard shows two information widgets:

- **Account Widget** -- displays your name and a link to edit your profile.
- **Filament Info Widget** -- shows system information.

On the **left side** of the screen is the sidebar navigation. This is how you access every section of the CMS.

---

## 2. Dashboard and Navigation Overview

The sidebar navigation is organized into groups. Here is a complete map of every item you will see:

| Sidebar Group | Menu Item | Icon | What It Manages |
|---|---|---|---|
| *(top)* | Dashboard | squares | Your landing page after login |
| **Inhalte** | Services | wrench | IT service offerings shown on the homepage and /angebot |
| **Inhalte** | Beitraege | newspaper | Blog/news articles shown under /aktuelles |
| **Inhalte** | Seiten | document | Static pages (Ueber uns, Impressum, Datenschutz, etc.) |
| **Inhalte** | Team Members | people group | Team member profiles shown on the Ueber-uns page |
| **Inhalte** | Partners | chain link | Technology partner logos and links |
| **Referenzen** | Kategorien | folder | Industry categories that group references |
| **Referenzen** | References | building | Client reference entries |
| **Kontakt** | Anfragen | envelope | Incoming contact form submissions (read-only) |
| *(bottom)* | Einstellungen | gear/cog | Site-wide settings: company info, contact details, footer, social links |

Click any menu item to go to that section. Each section opens with a **list view** showing all existing records. From the list view, you can create new records, edit existing ones, or delete them.

---

## 3. Content Management -- Inhalte

The **Inhalte** group contains the five core content types for the website.

---

### 3.1 Services (Leistungen)

Services represent the IT service offerings displayed on the homepage and on the dedicated **/angebot** page.

#### The List View

When you click **Inhalte > Services**, you see a table with three columns:

| Column | Description |
|---|---|
| **Title** | The name of the service (searchable and sortable) |
| **Sort order** | A number that determines display order (sortable) |
| **Is published** | A green checkmark (published) or red X (hidden) |

The list is sorted by sort order by default. You can drag and drop rows to rearrange them.

#### Creating a New Service

1. Click **Inhalte > Services** in the sidebar.
2. Click the **New service** button in the top-right corner.
3. Fill in the form fields:

| Field | Required | Description |
|---|---|---|
| **Title** | Yes | The name of the service, e.g., "IT-Sicherheit". Max 255 characters. |
| **Slug** | Yes | A URL-friendly identifier, e.g., "it-sicherheit". Must be unique across all services. Use only lowercase letters, numbers, and hyphens. |
| **Icon** | No | The filename of an icon to display with the service, e.g., "security.svg". |
| **Excerpt** | No | A short summary (2-3 sentences) used in preview/overview cards on the homepage. |
| **Body** | No | The full service description. Uses the rich text editor (see Section 7). Spans the full width of the form. |
| **Sort order** | No | A number controlling display order. Lower numbers appear first. Defaults to 0. |
| **Is published** | No | Toggle switch. When ON (default), the service is visible on the website. When OFF, it is hidden. |

4. Click **Create** to save.

#### Editing an Existing Service

1. Click **Inhalte > Services** in the sidebar.
2. Find the service in the list. You can use the search bar to search by title.
3. Click the pencil (edit) icon on the right side of the row.
4. Make your changes in the form.
5. Click **Save** to apply.

#### Reordering Services

There are two ways to change the order in which services appear:

- **Drag and drop:** On the list view, grab the handle icon on the left edge of a row and drag it to the desired position. The sort order numbers update automatically.
- **Manual sort order:** Edit a service and change the **Sort order** number directly. Lower numbers appear first.

#### Deleting Services

1. On the list view, select one or more services using the checkboxes on the left.
2. A **Bulk actions** dropdown appears at the top. Click it and select **Delete**.
3. Confirm the deletion in the dialog that appears.

> **Warning:** Deleting a service is permanent and cannot be undone.

---

### 3.2 Posts (Beitraege)

Posts are blog or news articles displayed on the **/aktuelles** page.

#### The List View

When you click **Inhalte > Beitraege**, you see a table with three columns:

| Column | Description |
|---|---|
| **Title** | The headline of the post (searchable and sortable) |
| **Published at** | The publication date in dd.mm.YYYY format (sortable) |
| **Is published** | A green checkmark (published) or red X (hidden) |

The list is sorted by publication date in descending order (newest first).

#### Creating a New Post

1. Click **Inhalte > Beitraege** in the sidebar.
2. Click the **New post** button in the top-right corner.
3. Fill in the form fields:

| Field | Required | Description |
|---|---|---|
| **Title** | Yes | The headline. Max 255 characters. |
| **Slug** | Yes | URL identifier, e.g., "neue-office-loesungen". Must be unique. |
| **Excerpt** | No | A short teaser text shown in post listings (2-3 sentences). |
| **Body** | No | The full article content. Uses the rich text editor. Spans full width. |
| **Featured image** | No | An image displayed as the post's banner/thumbnail. Click the upload area or drag and drop an image file. Uploaded images are stored in the `posts` directory. |
| **Published at** | No | A date and time picker. Sets when the post becomes visible. **A post only appears on the website when this date is in the past AND is_published is ON.** |
| **Is published** | No | Toggle switch. Defaults to ON. |

4. Click **Create** to save.

> **Critical:** For a post to be visible on the website, **both** conditions must be met:
> - The **Is published** toggle must be ON.
> - The **Published at** date must be set to a date/time in the past (or the current moment).
>
> If either condition is not met, the post will not appear. This lets you write posts in advance and schedule them for future publication.

#### Editing an Existing Post

1. Click **Inhalte > Beitraege** in the sidebar.
2. Find the post by scrolling or using the search bar.
3. Click the pencil (edit) icon.
4. Make your changes.
5. Click **Save**.

#### Deleting Posts

Select one or more posts using the checkboxes, then use **Bulk actions > Delete**.

---

### 3.3 Pages (Seiten)

Pages are static content pages such as "Ueber uns" (About Us), "Impressum" (Legal Notice), and "Datenschutz" (Privacy Policy).

#### The List View

When you click **Inhalte > Seiten**, you see a table with three columns:

| Column | Description |
|---|---|
| **Title** | The page name (searchable and sortable) |
| **Slug** | The URL path for this page |
| **Is published** | A green checkmark (published) or red X (hidden) |

#### Creating a New Page

1. Click **Inhalte > Seiten** in the sidebar.
2. Click the **New page** button.
3. Fill in the form fields:

| Field | Required | Description |
|---|---|---|
| **Title** | Yes | The page title, e.g., "Impressum". Max 255 characters. |
| **Slug** | Yes | URL path, e.g., "impressum" produces the URL /impressum. Must be unique. |
| **Hero heading** | No | The large headline displayed in the hero banner at the top of the page. Max 255 characters. |
| **Hero subheading** | No | A smaller line of text displayed below the hero heading. Max 255 characters. |
| **Hero image** | No | The path to the hero background image, e.g., "heroes/ueber_uns_hero_bg.jpg". Max 255 characters. |
| **Body** | No | The main page content. Uses the rich text editor. Spans full width. |
| **Meta title** | No | The SEO title that appears in the browser tab and in search engine results. Max 255 characters. If left blank, the page Title is used. |
| **Meta description** | No | The SEO description shown in search engine results below the title. Keep it under 160 characters for best results. |
| **Is published** | No | Toggle switch. Defaults to ON. |

4. Click **Create** to save.

#### Editing an Existing Page

1. Click **Inhalte > Seiten** in the sidebar.
2. Click the pencil (edit) icon next to the page you want to edit.
3. Make your changes.
4. Click **Save**.

> **Tip about SEO fields:** The **Meta title** and **Meta description** fields are important for search engine optimization. A well-written meta description (under 160 characters) can improve click-through rates from Google search results.

---

### 3.4 Team Members

Team members are displayed on the **Ueber uns** (About Us) page of the website.

#### The List View

When you click **Inhalte > Team Members**, you see a table with four columns:

| Column | Description |
|---|---|
| **Name** | The team member's full name (searchable and sortable) |
| **Role** | Their job title or function |
| **Sort order** | Display order number (sortable) |
| **Is published** | A green checkmark (visible) or red X (hidden) |

The list is sorted by sort order by default. Drag-and-drop reordering is available.

#### Creating a New Team Member

1. Click **Inhalte > Team Members** in the sidebar.
2. Click the **New team member** button.
3. Fill in the form fields:

| Field | Required | Description |
|---|---|---|
| **Name** | Yes | Full name of the team member, e.g., "Max Mustermann". Max 255 characters. |
| **Role** | No | Job title or function, e.g., "Geschaeftsfuehrer", "Systemadministrator". Max 255 characters. |
| **Email** | No | Email address. Must be a valid email format if provided. Max 255 characters. |
| **Phone** | No | Phone number, e.g., "+41 34 408 01 00". Max 255 characters. |
| **Photo** | No | Upload a portrait photo. Click the upload area or drag and drop. Stored in the `team` directory. See Section 8 for recommended image sizes. |
| **Bio** | No | A short biography or description (a few sentences). |
| **Sort order** | No | Display order number. Lower numbers appear first. Defaults to 0. |
| **Is published** | No | Toggle switch. Defaults to ON. |

4. Click **Create** to save.

#### Reordering Team Members

Drag and drop rows on the list view, or edit individual records and change the **Sort order** number.

---

### 3.5 Partners

Partners are technology or business partners whose logos and links are displayed on the website.

#### The List View

When you click **Inhalte > Partners**, you see a table with four columns:

| Column | Description |
|---|---|
| **Name** | The partner company name (searchable and sortable) |
| **Website** | The partner's website URL |
| **Sort order** | Display order number (sortable) |
| **Is published** | A green checkmark (visible) or red X (hidden) |

The list is sorted by sort order by default. Drag-and-drop reordering is available.

#### Creating a New Partner

1. Click **Inhalte > Partners** in the sidebar.
2. Click the **New partner** button.
3. Fill in the form fields:

| Field | Required | Description |
|---|---|---|
| **Name** | Yes | The partner company name, e.g., "Microsoft". Max 255 characters. |
| **Logo** | No | Upload the partner's logo image. Click the upload area or drag and drop. Stored in the `partners` directory. See Section 8 for recommended sizes. |
| **Website** | No | The partner's website URL, e.g., "https://www.microsoft.com". Max 255 characters. |
| **Description** | No | A short description of the partnership or the partner company. |
| **Sort order** | No | Display order number. Lower = earlier. Defaults to 0. |
| **Is published** | No | Toggle switch. Defaults to ON. |

4. Click **Create** to save.

#### Reordering Partners

Drag and drop rows on the list view, or edit the **Sort order** number directly.

---

## 4. References Management -- Referenzen

References showcase HILOTEC's clients, organized by industry category. They appear on the **/referenzen** page.

---

### 4.1 Reference Categories (Kategorien)

Categories are used to group references by industry (e.g., "Baugewerbe", "Gesundheitswesen").

#### The List View

When you click **Referenzen > Kategorien**, you see a table with three columns:

| Column | Description |
|---|---|
| **Name** | The category name (searchable and sortable) |
| **Referenzen** | A count of how many references belong to this category |
| **Sort order** | Display order number (sortable) |

The list is sorted by sort order. Drag-and-drop reordering is available.

#### Creating a New Category

1. Click **Referenzen > Kategorien** in the sidebar.
2. Click the **New reference category** button.
3. Fill in the form fields:

| Field | Required | Description |
|---|---|---|
| **Name** | Yes | The category name, e.g., "Landwirtschaft". Max 255 characters. |
| **Slug** | Yes | URL-friendly identifier, e.g., "landwirtschaft". Must be unique. |
| **Sort order** | No | Display order number. Defaults to 0. |

4. Click **Create** to save.

> **Note:** Categories do not have an "Is published" toggle. They are always visible as long as they contain at least one published reference.

#### Editing or Deleting a Category

- Click the pencil icon to edit a category's name, slug, or sort order.
- To delete, use the checkbox and **Bulk actions > Delete**.

> **Warning:** Deleting a category will also delete ALL references that belong to it. This action is permanent.

---

### 4.2 References

Individual reference entries represent client companies and are assigned to a category.

#### The List View

When you click **Referenzen > References**, you see a table with four columns:

| Column | Description |
|---|---|
| **Company name** | The client's company name (searchable and sortable) |
| **Category** | The industry category this reference belongs to (sortable) |
| **Website** | The client's website |
| **Is published** | A green checkmark (visible) or red X (hidden) |

The list is sorted alphabetically by company name by default.

#### Creating a New Reference

1. Click **Referenzen > References** in the sidebar.
2. Click the **New reference** button.
3. Fill in the form fields:

| Field | Required | Description |
|---|---|---|
| **Reference category** | Yes | Select the industry category from the dropdown. You can type to search. |
| **Company name** | Yes | The name of the client company, e.g., "Muster AG". Max 255 characters. |
| **Address** | No | The client's address. Max 255 characters. |
| **Description** | No | A brief description of the services provided or the project. |
| **Website** | No | The client's website URL. Max 255 characters. |
| **Sort order** | No | Display order within the category. Defaults to 0. |
| **Is published** | No | Toggle switch. Defaults to ON. |

4. Click **Create** to save.

#### Editing an Existing Reference

1. Click **Referenzen > References**.
2. Click the pencil (edit) icon next to the reference.
3. Make your changes. You can also change the category assignment here.
4. Click **Save**.

---

## 5. Contact Inquiries -- Anfragen

Contact inquiries are messages submitted through the public contact form on the website. They are **view-only** in the admin panel -- you cannot edit or create submissions, only read them and mark them as read.

#### The List View

When you click **Kontakt > Anfragen**, you see a table with four columns:

| Column | Description |
|---|---|
| **Name** | The sender's name (searchable and sortable) |
| **Email** | The sender's email address (searchable) |
| **Created at** | When the message was submitted, in dd.mm.YYYY HH:mm format (sortable) |
| **Is read** | A green checkmark (read) or red X (unread) |

The list is sorted by creation date in descending order (newest first).

#### Viewing an Inquiry

1. Click **Kontakt > Anfragen** in the sidebar.
2. Find the inquiry in the list.
3. Click the **eye icon** (View) on the right side of the row.
4. You will see the full inquiry details:
   - **Name** -- the sender's name (read-only, greyed out)
   - **Email** -- the sender's email address (read-only, greyed out)
   - **Phone** -- the sender's phone number, if provided (read-only, greyed out)
   - **Message** -- the full message text (read-only, greyed out)
   - **Is read** -- a toggle you CAN change to mark the inquiry as read

5. Toggle **Is read** to ON to mark the inquiry as processed.

#### Deleting Inquiries

Select one or more inquiries using checkboxes, then use **Bulk actions > Delete** to remove them.

---

## 6. Settings -- Einstellungen

The Settings page manages site-wide values that appear across the entire website: company name, contact details, footer content, and social media links. Changes here take effect immediately across all pages.

To access settings, click the **Einstellungen** item at the bottom of the sidebar (gear/cog icon).

The settings form is organized into four tabs. After making changes on any tab, click the **Speichern** (Save) button at the bottom of the form.

---

### 6.1 Allgemein (General) Tab

These settings define the company's identity and appear on the homepage and in site-wide elements.

| Field | Label | Description | Where It Appears |
|---|---|---|---|
| **company_name** | Firmenname | The full legal company name | Header, footer, browser tab, SEO titles |
| **company_slogan** | Slogan | The main tagline, e.g., "Sichere IT, die einfach funktioniert." | Homepage hero section as the primary heading |
| **company_subtitle** | Untertitel | A secondary descriptive line | Homepage hero section below the slogan |
| **founded_year** | Gruendungsjahr | The year the company was founded, e.g., "1995" | Homepage "Ihr IT-Partner seit..." section |
| **about_short** | Kurzbeschreibung | A short paragraph about the company | Homepage about teaser section |

---

### 6.2 Kontakt (Contact) Tab

These settings contain the company's contact information, used on the contact page and in the footer.

| Field | Label | Description | Where It Appears |
|---|---|---|---|
| **address_line1** | Adresse | Street address, e.g., "Untere Hohle Gasse 5" | Footer, contact page |
| **address_zip_city** | PLZ / Ort | Postal code and city, e.g., "CH-3550 Langnau i.E." | Footer, contact page |
| **address_country** | Land | Country, e.g., "Schweiz" | Footer, contact page |
| **phone_support_infra** | Telefon IT-Infrastruktur | Phone number for IT infrastructure support | Footer, contact page |
| **phone_label_infra** | Label IT-Infrastruktur | Label text for the infra phone, e.g., "Support IT-Infrastruktur" | Footer, contact page |
| **phone_support_software** | Telefon Software | Phone number for software support | Footer, contact page |
| **phone_label_software** | Label Software | Label text for the software phone, e.g., "Support Chronikos, M-Soft und Sage50" | Footer, contact page |
| **email** | E-Mail | Main contact email, e.g., "info@hilotec.com" | Footer, contact page |
| **website** | Website | Company website URL | Footer |
| **business_hours** | Oeffnungszeiten | Business hours, e.g., "Mo-Fr, 08:00-12:00 und 13:30-18:00" | Contact page |

---

### 6.3 Footer Tab

These settings control the call-to-action banner above the footer and the footer's bottom bar.

| Field | Label | Description | Where It Appears |
|---|---|---|---|
| **cta_heading** | CTA Ueberschrift | The heading in the gold call-to-action box above the footer | CTA section above footer on all pages |
| **cta_button_text** | CTA Button Text | The text on the CTA button, e.g., "Kontakt aufnehmen" | CTA section button |
| **cta_button_url** | CTA Button URL | The URL the CTA button links to, e.g., "/kontakt" | CTA section button link |
| **copyright_text** | Copyright | Copyright line, e.g., "Copyright 2025, HILOTEC Engineering + Consulting AG" | Very bottom of the footer |
| **teamviewer_text** | TeamViewer Text | Descriptive text for the remote support link | Footer, remote support column |
| **teamviewer_url** | TeamViewer URL | URL to TeamViewer download, e.g., "https://get.teamviewer.com/hilotec" | Footer, remote support link |

---

### 6.4 Social Media Tab

These settings provide links to the company's social media profiles, shown as icon links in the footer.

| Field | Label | Description | Where It Appears |
|---|---|---|---|
| **linkedin** | LinkedIn URL | Full URL to the LinkedIn company page | Footer (LinkedIn icon) |
| **github** | GitHub URL | Full URL to the GitHub organization page | Footer (GitHub icon) |

---

## 7. Common Concepts

These concepts apply across multiple content types in the admin panel.

### Publishing and Visibility

Most content types (Services, Posts, Pages, Team Members, Partners, References) have an **Is published** toggle switch.

- **ON (green):** The item is visible on the public website.
- **OFF (grey):** The item is hidden from the public website. It still exists in the admin panel and can be re-published at any time.

This is useful for:
- Preparing content before making it live.
- Temporarily hiding content without deleting it.
- Archiving outdated content.

**Special case -- Posts:** Posts have an additional requirement. Besides the Is published toggle being ON, the **Published at** date must also be set to a date and time in the past. This enables scheduled publishing: set a future date, and the post will automatically become visible when that date arrives.

### Slugs (URL Identifiers)

A **slug** is a URL-friendly text identifier used to build web addresses. For example:
- A service with slug `it-sicherheit` is accessible at `/angebot/it-sicherheit`
- A page with slug `impressum` is accessible at `/impressum`

Rules for slugs:
- Use only **lowercase letters**, **numbers**, and **hyphens** (dashes).
- No spaces, special characters, or uppercase letters.
- Each slug must be **unique** within its content type (you cannot have two services with the same slug).
- Avoid changing slugs for existing content -- any external links or bookmarks pointing to the old URL will break.

### Sort Order and Drag-and-Drop Reordering

Content types that support reordering (Services, Team Members, Partners, Reference Categories) offer two methods:

1. **Drag and drop:** On the list view, look for a small grip handle on the far left of each row. Click and hold the handle, then drag the row up or down to the desired position. Release to drop. The sort order numbers update automatically.

2. **Manual number entry:** Edit a record and type a number in the **Sort order** field. Items with lower numbers appear first. Items with the same sort order are shown in the order they were created.

### The Rich Text Editor

The **Body** field in Services, Posts, and Pages uses a TipTap-based rich text editor. It provides a toolbar with the following formatting options:

| Tool | What It Does |
|---|---|
| **Bold** | Makes selected text **bold** |
| **Italic** | Makes selected text *italic* |
| **Strikethrough** | ~~Strikes through~~ selected text |
| **Headings** (H2, H3) | Creates section headings -- use these to structure long content |
| **Bullet list** | Creates an unordered (bulleted) list |
| **Ordered list** | Creates a numbered list |
| **Link** | Select text, then click the link tool to add a hyperlink |
| **Image** | Insert an image into the body content |
| **Blockquote** | Formats text as an indented quote |

Tips for using the rich text editor:
- To create a link, first select the text you want to turn into a link, then click the link button in the toolbar and paste the URL.
- To remove a link, place your cursor in the linked text and click the unlink button.
- Use headings (H2, H3) to organize long articles. Avoid using H1 -- the page title already serves as H1.
- You can paste text from other applications. The editor will attempt to preserve basic formatting.

---

## 8. Image Recommendations

### Recommended Sizes and Formats

| Image Type | Recommended Size | Aspect Ratio | Format |
|---|---|---|---|
| **Post featured image** | 1200 x 630 px | 1.91:1 (landscape) | JPG or WebP |
| **Team member photo** | 400 x 400 px minimum | 1:1 (square) | JPG or WebP |
| **Partner logo** | 300 x 150 px | 2:1 (landscape) | PNG (transparent background preferred) |
| **Hero background image** | 1920 x 800 px | ~2.4:1 (wide landscape) | JPG or WebP |

### File Format Guide

| Format | Best For | Notes |
|---|---|---|
| **JPG** | Photos, complex images | Good compression for photographs. Does not support transparency. |
| **PNG** | Logos, graphics with transparency | Supports transparent backgrounds. Larger file size than JPG for photos. |
| **WebP** | All image types | Modern format with excellent compression. Supported by all modern browsers. Best choice when possible. |
| **SVG** | Icons, simple vector graphics | Used for service icons (referenced by filename in the Icon field). Not uploaded through the file upload widget. |

### Optimization Tips

- **Keep file sizes small.** Aim for under 200 KB per image. Large images slow down page loading.
- **Resize before uploading.** Do not upload a 4000 x 3000 px photo when 1200 x 630 px is sufficient.
- **Use compression tools.** Free online tools like TinyPNG (tinypng.com) or Squoosh (squoosh.app) can reduce file size by 50-80% without visible quality loss.
- **Name files descriptively.** Use names like `team-max-mustermann.jpg` rather than `IMG_20240115.jpg`.
- **Use WebP when possible.** It provides the best quality-to-size ratio.

### How to Upload an Image

1. In the form editor, find the image upload field (e.g., **Featured image**, **Photo**, or **Logo**).
2. You will see a rectangular dropzone area with an upload prompt.
3. Either:
   - **Click** the dropzone to open a file browser, then select your image, or
   - **Drag and drop** an image file from your computer directly onto the dropzone.
4. A preview of the uploaded image will appear.
5. To **remove** an uploaded image, click the X or delete button on the image preview.
6. To **replace** an image, remove the current one and upload a new one.

Uploaded images are stored in:
- `posts/` directory for post featured images
- `team/` directory for team member photos
- `partners/` directory for partner logos

---

## 9. Frequently Asked Questions (FAQ)

### "Why doesn't my post appear on the website?"

Posts have two visibility requirements that must **both** be satisfied:

1. **Is published** must be toggled ON.
2. **Published at** must be set to a date and time that is in the past.

Checklist:
- [ ] Go to **Inhalte > Beitraege** and click the edit icon next to the post.
- [ ] Verify that **Is published** is toggled ON (the switch should be to the right/green).
- [ ] Verify that **Published at** has a date and time set, and that it is not in the future.
- [ ] If you just changed these values, click **Save** and reload the public website.

If all settings look correct and the post still does not appear, try clearing your browser cache or opening the site in a private/incognito window.

---

### "How do I upload an image?"

1. Navigate to the content item you want to add an image to (e.g., a post, team member, or partner).
2. Click the edit icon to open the form.
3. Find the image field (**Featured image**, **Photo**, or **Logo**).
4. Click the upload area or drag and drop a file from your computer.
5. Wait for the upload to complete -- you will see a thumbnail preview.
6. Click **Save** to store the record with the new image.

Supported formats: JPG, PNG, WebP. See Section 8 for recommended sizes.

---

### "How do I change the phone number in the footer?"

1. Click **Einstellungen** (the gear icon) in the sidebar.
2. Click the **Kontakt** tab.
3. Find the field you want to change:
   - **Telefon IT-Infrastruktur** -- for the IT infrastructure support number
   - **Telefon Software** -- for the software support number
4. Update the phone number.
5. Click the **Speichern** (Save) button at the bottom.
6. The change takes effect immediately across the entire website.

---

### "How do I add a new team member?"

1. Click **Inhalte > Team Members** in the sidebar.
2. Click the **New team member** button.
3. Fill in at least the **Name** field (required). Add the role, email, phone, photo, and bio as needed.
4. Set the **Sort order** to control where the member appears relative to others.
5. Make sure **Is published** is ON.
6. Click **Create**.

---

### "How do I change the company address on the website?"

1. Click **Einstellungen** in the sidebar.
2. Click the **Kontakt** tab.
3. Update the **Adresse**, **PLZ / Ort**, and/or **Land** fields.
4. Click **Speichern**.

---

### "How do I hide a service without deleting it?"

1. Click **Inhalte > Services** in the sidebar.
2. Click the edit icon next to the service.
3. Toggle **Is published** to OFF.
4. Click **Save**.

The service is now hidden from the website but remains in the admin panel. You can re-enable it at any time by toggling Is published back to ON.

---

### "Can I schedule a post for future publication?"

Yes. When creating or editing a post:

1. Set the **Published at** date and time to the desired future date.
2. Make sure **Is published** is toggled ON.
3. Click **Save** or **Create**.

The post will automatically become visible on the website once the Published at date has passed. No further action is needed.

---

### "How do I reorder items on the homepage?"

Services, team members, and partners can be reordered:

1. Go to the relevant list view (e.g., **Inhalte > Services**).
2. Look for the drag handle on the left side of each row (it looks like a set of dots or lines).
3. Click and hold the handle, then drag the row to its new position.
4. Release to drop. The order is saved automatically.

---

## 10. Troubleshooting

### I cannot log in

| Symptom | Solution |
|---|---|
| "These credentials do not match our records" | Double-check your email address and password. Passwords are case-sensitive. |
| Forgot password | Click "Forgot your password?" on the login page. Enter your admin email to receive a reset link. |
| Account locked or rate-limited | The system allows 60 requests per minute. If you have made many rapid attempts, wait one minute and try again. |
| Still cannot access | Contact your system administrator. They can verify your email is in the authorized admin list and reset your password. |

### My changes are not showing on the website

| Symptom | Solution |
|---|---|
| Edited content not visible | Make sure you clicked **Save** (or **Speichern** for settings) after making changes. |
| Browser shows old content | Clear your browser cache, or press Ctrl+Shift+R (Cmd+Shift+R on Mac) to hard-refresh the page. |
| Settings changes not taking effect | Settings are cached for up to 60 minutes. Saving settings in the admin panel clears the cache, but if changes still do not appear, ask your developer to clear the application cache. |
| Post not visible | See the FAQ entry "Why doesn't my post appear on the website?" above. Both Is published and Published at must be correct. |

### Image upload fails

| Symptom | Solution |
|---|---|
| Upload appears to do nothing | The file may be too large. Reduce the image size to under 2 MB and try again. |
| Error message on upload | Ensure the file is a supported image format (JPG, PNG, WebP). Other file types are not accepted. |
| Image appears broken on website | Make sure the storage link is set up correctly. Contact your developer if the issue persists. |

### Content appears in the wrong order

| Symptom | Solution |
|---|---|
| Items not in expected order | Check the **Sort order** values. Items with lower numbers appear first. Items with the same number appear in creation order. |
| Drag and drop not working | Make sure you are grabbing the handle on the far left of the row, not clicking elsewhere. Not all content types support drag-and-drop (only Services, Team Members, Partners, and Reference Categories do). |

### I accidentally deleted something

Deletions are **permanent**. There is no recycle bin or undo feature. If critical content was deleted, contact your system administrator -- they may be able to restore it from a database backup.

> **Best practice:** Before deleting content, consider toggling **Is published** to OFF instead. This hides the content from the website while preserving it in the admin panel for future use.

### The admin panel looks broken or loads slowly

| Symptom | Solution |
|---|---|
| Layout is broken | Try a hard refresh (Ctrl+Shift+R). If the issue persists, try a different browser (Chrome, Firefox, or Edge recommended). |
| Panel is very slow | Check your internet connection. If the issue is server-side, contact your system administrator. |
| Session expired | After a period of inactivity, you will be logged out. Simply log in again. |

---

*This guide covers all sections of the HILOTEC admin panel. If you encounter an issue not addressed here, contact your system administrator or developer for assistance.*
