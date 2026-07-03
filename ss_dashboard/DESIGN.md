---
name: Premium Bridal Admin
colors:
  surface: '#131313'
  surface-dim: '#131313'
  surface-bright: '#393939'
  surface-container-lowest: '#0e0e0e'
  surface-container-low: '#1c1b1b'
  surface-container: '#201f1f'
  surface-container-high: '#2a2a2a'
  surface-container-highest: '#353534'
  on-surface: '#e5e2e1'
  on-surface-variant: '#dec1b3'
  inverse-surface: '#e5e2e1'
  inverse-on-surface: '#313030'
  outline: '#a58b7f'
  outline-variant: '#574238'
  surface-tint: '#ffb68f'
  primary: '#ffb68f'
  on-primary: '#542100'
  primary-container: '#f47d31'
  on-primary-container: '#5c2500'
  inverse-primary: '#9c4400'
  secondary: '#e9c349'
  on-secondary: '#3c2f00'
  secondary-container: '#af8d11'
  on-secondary-container: '#342800'
  tertiary: '#3adfab'
  on-tertiary: '#003828'
  tertiary-container: '#00b386'
  on-tertiary-container: '#003d2c'
  error: '#ffb4ab'
  on-error: '#690005'
  error-container: '#93000a'
  on-error-container: '#ffdad6'
  primary-fixed: '#ffdbca'
  primary-fixed-dim: '#ffb68f'
  on-primary-fixed: '#331200'
  on-primary-fixed-variant: '#773200'
  secondary-fixed: '#ffe088'
  secondary-fixed-dim: '#e9c349'
  on-secondary-fixed: '#241a00'
  on-secondary-fixed-variant: '#574500'
  tertiary-fixed: '#60fcc6'
  tertiary-fixed-dim: '#3adfab'
  on-tertiary-fixed: '#002116'
  on-tertiary-fixed-variant: '#00513b'
  background: '#131313'
  on-background: '#e5e2e1'
  surface-variant: '#353534'
typography:
  display-lg:
    fontFamily: Manrope
    fontSize: 40px
    fontWeight: '700'
    lineHeight: 48px
    letterSpacing: -0.02em
  headline-md:
    fontFamily: Manrope
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  title-sm:
    fontFamily: Manrope
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 24px
  body-md:
    fontFamily: Work Sans
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-numeric:
    fontFamily: JetBrains Mono
    fontSize: 13px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.02em
  label-caps:
    fontFamily: Work Sans
    fontSize: 11px
    fontWeight: '700'
    lineHeight: 16px
    letterSpacing: 0.08em
  display-lg-mobile:
    fontFamily: Manrope
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  unit: 4px
  container-padding: 24px
  gutter: 16px
  stack-sm: 8px
  stack-md: 16px
  stack-lg: 32px
---

## Brand & Style

The design system is engineered for a luxury bridal rental and sales platform, where the user experience must mirror the high-end nature of the products—sophisticated, precise, and authoritative. The target audience is boutique administrators and inventory specialists who require high-density information without sacrificing aesthetic elegance.

The chosen style is **Modern Minimalism with Tonal Depth**. By moving away from flat black to a nuanced charcoal base, we create a canvas that allows vibrant orange accents and data-rich visualizations to "glow" without causing visual fatigue. The interface uses subtle gradients and micro-interactions to evoke a sense of physical luxury, while maintaining the structural rigor needed for complex rental logistics and inventory health tracking.

## Colors

The palette is anchored by a deep charcoal and jet-black foundation to provide maximum contrast for critical business metrics. 

- **Primary (Saffron Orange):** Used for primary actions, critical alerts, and branding highlights. It represents the energy and vitality of the bridal market.
- **Secondary (Champagne Gold):** Reserved for luxury indicators, premium membership statuses, and "Sold" or "Exclusive" markers.
- **Surface Strategy:** We utilize a four-tier elevation system (Canvas, Surface, Surface-High, Surface-Highest) to create hierarchy through tonal shifts rather than heavy shadows. 
- **Data Visualization:** High-contrast semantics are used for health tracking—`data_critical` for out-of-stock items, `data_success` for revenue growth, and `data_warning` for low-stock alerts.

## Typography

This design system employs a three-font strategy to balance elegance with technical utility.

- **Manrope (Headlines):** Used for titles and key metrics. Its modern, geometric construction provides a clean, high-end feel.
- **Work Sans (Body):** Selected for its exceptional legibility in data-dense tables and inventory descriptions.
- **JetBrains Mono (Data):** Specifically for SKUs, Bill IDs, and currency values. The monospaced nature ensures that columns of numbers align perfectly, allowing for quick scanning of financial data.

Use `label-caps` for section headers and table headers to create a clear structural distinction. All currency values should use the `label-numeric` token to ensure the Rupee symbol and digits maintain consistent spacing.

## Layout & Spacing

The layout follows a **12-column fluid grid** for the main content area, with a fixed sidebar width of 260px. 

- **Density:** This is a high-density dashboard. Use the 4px base unit to maintain tight but breathable relationships between data points.
- **Rental Tracking:** Complex tables should use a "Condensed Rows" approach with 8px vertical padding to maximize the number of visible bookings.
- **Breakpoints:** 
  - **Desktop (1280px+):** Full 12-column view with persistent sidebar.
  - **Tablet (768px - 1279px):** Sidebar collapses to icons; content switches to 8-column grid.
  - **Mobile (<767px):** Single column stack; sidebar becomes a bottom navigation bar or hamburger menu.

## Elevation & Depth

Visual hierarchy is established through **Tonal Layering** and **Soft Inner Glows** rather than traditional drop shadows.

1.  **Canvas (Level 0):** `#0A0A0A` – The deepest layer.
2.  **Card Surfaces (Level 1):** `#1A1A1A` with a subtle `1px` solid border of `#2E2E2E`.
3.  **Active/Hover States (Level 2):** Use a subtle `#F47D31` (Primary) inner glow (blur: 20px, opacity: 5%) to make interactive elements feel "backlit."
4.  **Modals/Overlays (Level 3):** Background blur (backdrop-filter: blur(12px)) with a semi-transparent dark fill to maintain context of the dashboard underneath.

Data visualization cards should have no shadow but may use a top border accent (2px) in the primary color to denote "Active" or "Critical" status.

## Shapes

The shape language is **Soft (0.25rem)**. This provides a professional, sharp edge that feels modern and architectural.

- **Buttons & Inputs:** Use the standard `rounded` (4px) for a crisp, organized appearance.
- **Status Chips:** Use `rounded-xl` (12px) to create a visual contrast against the more rigid rectangular forms of the data cards and tables.
- **Inventory Images:** Thumbnails should maintain a strict 0px or 2px radius to reflect the precision of luxury fashion.

## Components

- **Action Buttons:** Primary buttons should be solid `#F47D31` with black text for maximum punch. Secondary buttons should be "Ghost" style with a `#2E2E2E` border and white text.
- **Data Tables:** Use alternating row highlights (Zebra striping) using `#151515` against the surface color. Table headers must be `label-caps` with a 40% opacity white text.
- **Inventory Health Chips:** Small, pill-shaped indicators. "Out of Stock" uses a solid `data_critical` background; "Low Stock" uses an outlined `data_warning` style.
- **Input Fields:** Dark backgrounds (`#0F0F0F`) with a 1px border. On focus, the border transitions to the primary saffron orange with a subtle outer glow.
- **Inventory Cards:** Must include a high-contrast SKU label in `label-numeric` typography at the top right for quick warehouse identification.
- **Rental Progress Bar:** A thin (4px) track showing the lifecycle of a rental (Booked -> Picked Up -> Returned) using tonal variations of the primary color.