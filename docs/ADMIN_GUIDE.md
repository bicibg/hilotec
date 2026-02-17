# HILOTEC Website — Administrations-Handbuch

Dieses Handbuch erklärt, wie Sie die Inhalte der HILOTEC-Website über das Admin-Panel verwalten.

## Anmeldung

1. Öffnen Sie im Browser: **https://www.hilotec.com/admin**
2. Geben Sie Ihre E-Mail-Adresse und Ihr Passwort ein
3. Klicken Sie auf **Sign in**

> **Wichtig:** Ändern Sie das Standardpasswort nach der ersten Anmeldung.

## Übersicht

Nach der Anmeldung sehen Sie das Dashboard mit der Navigation auf der linken Seite:

| Bereich | Was Sie hier verwalten |
|---------|----------------------|
| **Inhalte → Leistungen** | Die 8 IT-Dienstleistungen (Angebot-Seite) |
| **Inhalte → Beiträge** | Blog-Artikel (Aktuelles-Seite) |
| **Inhalte → Seiten** | Statische Seiten (Über uns, Impressum, Datenschutz) |
| **Inhalte → Team Members** | Team-Mitglieder (Über-uns-Seite) |
| **Inhalte → Partners** | Partner-Logos und -Links |
| **Referenzen → Kategorien** | Branchen-Kategorien (Baugewerbe, Gesundheitswesen, etc.) |
| **Referenzen → References** | Einzelne Referenz-Einträge mit Firma, Adresse, Beschreibung |
| **Kontakt → Anfragen** | Eingehende Kontaktformular-Nachrichten |
| **Einstellungen** | Firmenname, Kontaktdaten, Footer-Texte, Social Media |

---

## Leistungen (Services) verwalten

Die Leistungen erscheinen auf der **Startseite** und unter **/angebot**.

### Leistung bearbeiten
1. Klicken Sie in der Navigation auf **Inhalte → Leistungen**
2. Klicken Sie auf das Stift-Symbol neben der gewünschten Leistung
3. Bearbeiten Sie die Felder:
   - **Title** — Name der Leistung (z.B. "IT-Sicherheit")
   - **Slug** — URL-Pfad (z.B. "it-sicherheit") — normalerweise nicht ändern
   - **Icon** — Dateiname des Icons (z.B. "security.svg")
   - **Excerpt** — Kurzbeschreibung für die Übersicht
   - **Body** — Ausführliche Beschreibung (Rich-Text-Editor mit Formatierung)
   - **Sort order** — Reihenfolge (1 = erste Position)
   - **Is published** — Ein/Aus-Schalter: Nur veröffentlichte Leistungen werden angezeigt
4. Klicken Sie auf **Save**

### Reihenfolge ändern
Auf der Listenansicht können Sie die Leistungen per **Drag & Drop** (am Griff-Symbol links) umsortieren.

---

## Referenzen verwalten

Die Referenzen erscheinen unter **/referenzen**, gruppiert nach Branchen.

### Neue Referenz hinzufügen
1. Klicken Sie auf **Referenzen → References**
2. Klicken Sie auf **New reference**
3. Füllen Sie die Felder aus:
   - **Reference category** — Wählen Sie die Branche aus der Dropdown-Liste
   - **Company name** — Firmenname des Kunden
   - **Address** — Adresse (optional)
   - **Description** — Beschreibung der erbrachten Leistungen
   - **Website** — Website-Domain ohne https:// (z.B. "firma.ch"), optional
   - **Is published** — Schalter zum Ein-/Ausblenden
4. Klicken Sie auf **Create**

### Kategorie hinzufügen
1. Klicken Sie auf **Referenzen → Kategorien**
2. Klicken Sie auf **New reference category**
3. Geben Sie den **Name** (z.B. "Landwirtschaft") und einen **Slug** (z.B. "landwirtschaft") ein
4. Klicken Sie auf **Create**

---

## Beiträge (Blog) verwalten

Blog-Beiträge erscheinen unter **/aktuelles**.

### Neuen Beitrag erstellen
1. Klicken Sie auf **Inhalte → Beiträge**
2. Klicken Sie auf **New post**
3. Füllen Sie die Felder aus:
   - **Title** — Überschrift des Beitrags
   - **Slug** — URL-Pfad (wird automatisch aus dem Titel generiert)
   - **Excerpt** — Kurzbeschreibung für die Übersicht
   - **Body** — Volltext mit Rich-Text-Editor (Formatierung, Links, Listen)
   - **Featured image** — Optionales Vorschaubild (per Klick hochladen oder Drag & Drop)
   - **Published at** — Datum und Uhrzeit der Veröffentlichung
   - **Is published** — Schalter zum Veröffentlichen/Verbergen
4. Klicken Sie auf **Create**

> **Tipp:** Ein Beitrag wird erst angezeigt, wenn sowohl **Is published** aktiviert als auch **Published at** ein Datum in der Vergangenheit hat.

---

## Seiten verwalten

Statische Seiten wie **Über uns**, **Impressum** und **Datenschutz**.

### Seite bearbeiten
1. Klicken Sie auf **Inhalte → Seiten**
2. Klicken Sie auf das Stift-Symbol neben der Seite
3. Bearbeiten Sie:
   - **Title** — Seitentitel
   - **Hero heading** — Überschrift im Hero-Bereich (grosses Bild oben)
   - **Hero subheading** — Unterüberschrift im Hero-Bereich
   - **Hero image** — Pfad zum Hero-Bild (z.B. "heroes/ueber_uns_hero_bg.jpg")
   - **Body** — Seiteninhalt (Rich-Text-Editor)
   - **Meta title** — SEO-Titel (erscheint im Browser-Tab und bei Google)
   - **Meta description** — SEO-Beschreibung (erscheint bei Google unter dem Titel)
4. Klicken Sie auf **Save**

---

## Team-Mitglieder verwalten

Team-Mitglieder erscheinen auf der **Über uns**-Seite.

### Neues Team-Mitglied hinzufügen
1. Klicken Sie auf **Inhalte → Team Members**
2. Klicken Sie auf **New team member**
3. Füllen Sie die Felder aus:
   - **Name** — Vollständiger Name
   - **Role** — Funktion (z.B. "Geschäftsführer", "Systemadministrator")
   - **Email** — E-Mail-Adresse (optional)
   - **Phone** — Telefonnummer (optional)
   - **Photo** — Foto hochladen (empfohlen: quadratisch, mind. 200x200px)
   - **Bio** — Kurze Beschreibung (optional)
   - **Sort order** — Reihenfolge
4. Klicken Sie auf **Create**

---

## Einstellungen

Hier verwalten Sie globale Inhalte, die auf allen Seiten erscheinen.

### Einstellungen bearbeiten
1. Klicken Sie in der Navigation auf **Einstellungen** (Zahnrad-Symbol)
2. Wählen Sie den gewünschten Tab:

#### Tab "Allgemein"
| Feld | Wo es erscheint |
|------|----------------|
| Firmenname | Header, Footer, SEO-Titel |
| Slogan | Startseite Hero-Überschrift |
| Untertitel | Startseite Hero-Unterzeile |
| Gründungsjahr | Startseite "Ihr IT-Partner seit..." |
| Kurzbeschreibung | Startseite About-Teaser |

#### Tab "Kontakt"
| Feld | Wo es erscheint |
|------|----------------|
| Adresse, PLZ/Ort, Land | Footer, Kontaktseite |
| Telefon IT-Infrastruktur | Footer, Kontaktseite |
| Telefon Software | Footer, Kontaktseite |
| E-Mail | Footer, Kontaktseite |
| Öffnungszeiten | Kontaktseite |

#### Tab "Footer"
| Feld | Wo es erscheint |
|------|----------------|
| CTA Überschrift | Gelbe Box über dem Footer |
| CTA Button Text | Button in der gelben Box |
| CTA Button URL | Ziel des CTA-Buttons |
| Copyright | Fusszeile ganz unten |
| TeamViewer Text | Footer, Fernwartung-Spalte |
| TeamViewer URL | Link zum TeamViewer-Download |

#### Tab "Social Media"
| Feld | Wo es erscheint |
|------|----------------|
| LinkedIn URL | Footer (Icon-Link) |
| GitHub URL | Footer (Icon-Link) |

3. Klicken Sie auf **Speichern**

---

## Kontaktanfragen einsehen

Nachrichten, die über das Kontaktformular eingehen, werden automatisch gespeichert.

1. Klicken Sie auf **Kontakt → Anfragen**
2. Sie sehen eine Liste aller Anfragen mit Name, E-Mail, Datum und Gelesen-Status
3. Klicken Sie auf das Augen-Symbol, um die vollständige Nachricht zu lesen
4. Markieren Sie Anfragen als gelesen über den **Is read**-Schalter

---

## Tipps und Hinweise

### Rich-Text-Editor
Der Body-Editor unterstützt:
- **Fett**, *kursiv*, ~~durchgestrichen~~
- Überschriften (H2, H3)
- Aufzählungen und nummerierte Listen
- Links (Text markieren → Link-Symbol klicken)
- Bilder einfügen

### Bilder
- Empfohlene Formate: JPG, PNG, WebP
- Maximale Dateigrösse: abhängig von Server-Konfiguration (Standard: 2MB)
- Bilder werden automatisch in `storage/app/public/` gespeichert

### Veröffentlichung
- Der **Is published**-Schalter steuert, ob ein Eintrag auf der Website sichtbar ist
- Sie können Inhalte vorbereiten und erst später veröffentlichen
- Bei Beiträgen bestimmt zusätzlich das **Published at**-Datum, ab wann der Beitrag sichtbar ist

### Slug (URL-Pfad)
- Der Slug bestimmt die URL einer Seite (z.B. "it-sicherheit" → /angebot/it-sicherheit)
- Verwenden Sie nur Kleinbuchstaben, Zahlen und Bindestriche
- Ändern Sie den Slug einer bestehenden Seite nur wenn nötig — bestehende Links werden sonst ungültig
