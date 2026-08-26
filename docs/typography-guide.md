# Typography Usage Guide - Campos Sport

## Overview

This guide provides comprehensive documentation for the typography system implemented in Campos Sport. It establishes consistent usage rules for colors, font sizes, weights, and component-specific guidelines to ensure a cohesive visual experience across all user interfaces.

## Color Palette Usage Rules

### Primary Color System

The typography system is built around the primary color `#801336` (rojo vino) with carefully crafted variations:

```css
/* Primary Color Variations */
--color-primary: #801336;           /* Main brand color */
--color-primary-50: #fdf2f4;        /* Lightest tint */
--color-primary-100: #fce7eb;       /* Very light backgrounds */
--color-primary-200: #f9d0d9;       /* Light backgrounds */
--color-primary-300: #f4a8ba;       /* Subtle accents */
--color-primary-400: #ed7396;       /* Light text on dark backgrounds */
--color-primary-500: #e04574;       /* Medium emphasis */
--color-primary-600: #cc2857;       /* Muted text */
--color-primary-700: #ab1e47;       /* Secondary text */
--color-primary-800: #901c42;       /* Strong emphasis */
--color-primary-900: #801336;       /* Primary text */
--color-primary-950: #470a1e;       /* Darkest shade */
```

### Color Usage Rules

#### Text Colors
- **Primary Text**: Use `--color-primary-900` (#801336) for main headings, important labels, and primary content
- **Secondary Text**: Use `--color-primary-700` (#ab1e47) for subheadings and secondary content
- **Muted Text**: Use `--color-primary-600` (#cc2857) for less important text, captions, and metadata
- **Light Text**: Use `--color-primary-400` (#ed7396) for text on dark backgrounds

#### Background Colors
- **Light Backgrounds**: Use `--color-primary-50` to `--color-primary-200` for subtle background tints
- **Accent Backgrounds**: Use `--color-primary-300` to `--color-primary-500` for highlighted sections
- **Strong Backgrounds**: Use `--color-primary-600` to `--color-primary-900` for buttons and emphasis

### Semantic Color System

#### Success Colors (Green Harmony)
```css
--color-success: #059669;           /* Primary success color */
--color-success-light: #d1fae5;     /* Light success background */
--color-success-dark: #047857;      /* Dark success emphasis */
```

**Usage**: Success messages, completed states, positive indicators

#### Warning Colors (Orange Harmony)
```css
--color-warning: #d97706;           /* Primary warning color */
--color-warning-light: #fef3c7;     /* Light warning background */
--color-warning-dark: #b45309;      /* Dark warning emphasis */
```

**Usage**: Warning messages, pending states, caution indicators

#### Error Colors (Red Harmony)
```css
--color-error: #dc2626;             /* Primary error color */
--color-error-light: #fee2e2;       /* Light error background */
--color-error-dark: #b91c1c;        /* Dark error emphasis */
```

**Usage**: Error messages, failed states, destructive actions

## Font Size and Weight Usage Examples

### Font Size Scale

#### Display Sizes (Hero Content)
```css
--text-display-lg: 3rem;    /* 48px - Hero titles, landing pages */
--text-display-md: 2.5rem;  /* 40px - Major section headers */
--text-display-sm: 2rem;    /* 32px - Page titles, dashboard headers */
```

**Usage Examples**:
- Landing page hero: `text-display-lg`
- Dashboard main title: `text-display-sm`
- Section dividers: `text-display-md`

#### Heading Sizes (Content Structure)
```css
--text-heading-xl: 1.875rem; /* 30px - Major content headers */
--text-heading-lg: 1.5rem;   /* 24px - Section headings */
--text-heading-md: 1.25rem;  /* 20px - Subsection headings */
--text-heading-sm: 1.125rem; /* 18px - Product names, card titles */
```

**Usage Examples**:
- Product category titles: `text-heading-lg`
- Product names: `text-heading-sm`
- Form section headers: `text-heading-md`
- Card titles: `text-heading-sm`

#### Body Sizes (Regular Content)
```css
--text-body-lg: 1rem;        /* 16px - Primary body text */
--text-body-md: 0.875rem;    /* 14px - Secondary body text */
--text-body-sm: 0.75rem;     /* 12px - Captions, metadata */
--text-body-xs: 0.625rem;    /* 10px - Fine print (use sparingly) */
```

**Usage Examples**:
- Main content paragraphs: `text-body-lg`
- Form labels and descriptions: `text-body-md`
- Table data: `text-body-md`
- Timestamps and metadata: `text-body-sm`

### Font Weight Guidelines

```css
--font-light: 300;      /* Decorative text, large displays */
--font-regular: 400;    /* Body text, descriptions */
--font-medium: 500;     /* Form labels, navigation */
--font-semibold: 600;   /* Subheadings, important labels */
--font-bold: 700;       /* Main headings, emphasis */
```

**Weight Usage Rules**:
- **Light (300)**: Only for large display text where readability isn't compromised
- **Regular (400)**: Default for all body text and descriptions
- **Medium (500)**: Form labels, navigation links, secondary emphasis
- **Semibold (600)**: Section headings, product names, important labels
- **Bold (700)**: Main page titles, primary headings, strong emphasis

## Component-Specific Typography Guidelines

### Admin Interface Components

#### Dashboard Elements
```scss
// Main dashboard title
.admin-dashboard-title {
  font-size: var(--text-display-sm);     /* 32px */
  font-weight: var(--font-bold);         /* 700 */
  color: var(--color-primary);           /* #801336 */
}

// Section headings
.admin-section-heading {
  font-size: var(--text-heading-lg);     /* 24px */
  font-weight: var(--font-semibold);     /* 600 */
  color: var(--color-primary);           /* #801336 */
  margin-bottom: 1rem;
}

// Statistics cards
.admin-stat-value {
  font-size: var(--text-heading-xl);     /* 30px */
  font-weight: var(--font-bold);         /* 700 */
  color: var(--color-primary);           /* #801336 */
}

.admin-stat-label {
  font-size: var(--text-body-md);        /* 14px */
  font-weight: var(--font-medium);       /* 500 */
  color: var(--color-primary-600);       /* #cc2857 */
}
```

#### Table Components
```scss
// Table headers
.admin-table-header {
  font-size: var(--text-body-md);        /* 14px */
  font-weight: var(--font-semibold);     /* 600 */
  color: white;                          /* On primary background */
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

// Table data
.admin-table-data {
  font-size: var(--text-body-md);        /* 14px */
  font-weight: var(--font-regular);      /* 400 */
  color: var(--color-primary-700);       /* #ab1e47 */
}

// Table actions
.admin-table-action {
  font-size: var(--text-body-sm);        /* 12px */
  font-weight: var(--font-medium);       /* 500 */
  color: var(--color-primary);           /* #801336 */
}
```

#### Form Components
```scss
// Form labels
.admin-form-label {
  font-size: var(--text-body-md);        /* 14px */
  font-weight: var(--font-medium);       /* 500 */
  color: var(--color-primary);           /* #801336 */
  margin-bottom: 0.25rem;
}

// Form inputs
.admin-form-input {
  font-size: var(--text-body-md);        /* 14px */
  font-weight: var(--font-regular);      /* 400 */
  color: var(--color-primary-700);       /* #ab1e47 */
}

// Form help text
.admin-form-help {
  font-size: var(--text-body-sm);        /* 12px */
  font-weight: var(--font-regular);      /* 400 */
  color: var(--color-primary-600);       /* #cc2857 */
}
```

### User Interface Components

#### Product Catalog
```scss
// Product names
.product-name {
  font-size: var(--text-heading-sm);     /* 18px */
  font-weight: var(--font-semibold);     /* 600 */
  color: var(--color-primary);           /* #801336 */
  line-height: 1.4;
}

// Product prices
.product-price {
  font-size: var(--text-heading-md);     /* 20px */
  font-weight: var(--font-bold);         /* 700 */
  color: var(--color-primary);           /* #801336 */
}

// Product descriptions
.product-description {
  font-size: var(--text-body-md);        /* 14px */
  font-weight: var(--font-regular);      /* 400 */
  color: var(--color-primary-600);       /* #cc2857 */
  line-height: 1.5;
}

// Product categories
.product-category {
  font-size: var(--text-body-sm);        /* 12px */
  font-weight: var(--font-medium);       /* 500 */
  color: var(--color-primary-700);       /* #ab1e47 */
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
```

#### Navigation Components
```scss
// Main navigation links
.nav-link {
  font-size: var(--text-body-md);        /* 14px */
  font-weight: var(--font-medium);       /* 500 */
  color: white;                          /* On dark background */
}

.nav-link:hover {
  color: var(--color-primary-200);       /* Light hover state */
}

// Breadcrumb navigation
.breadcrumb-item {
  font-size: var(--text-body-sm);        /* 12px */
  font-weight: var(--font-regular);      /* 400 */
  color: var(--color-primary-600);       /* #cc2857 */
}

.breadcrumb-current {
  font-weight: var(--font-medium);       /* 500 */
  color: var(--color-primary);           /* #801336 */
}
```

#### Button Components
```scss
// Primary buttons
.btn-primary {
  font-size: var(--text-body-md);        /* 14px */
  font-weight: var(--font-medium);       /* 500 */
  color: white;
  background-color: var(--color-primary); /* #801336 */
}

// Secondary buttons
.btn-secondary {
  font-size: var(--text-body-md);        /* 14px */
  font-weight: var(--font-medium);       /* 500 */
  color: var(--color-primary);           /* #801336 */
  border-color: var(--color-primary);
}

// Large buttons
.btn-large {
  font-size: var(--text-body-lg);        /* 16px */
  font-weight: var(--font-semibold);     /* 600 */
}
```

### Worker Interface Components

#### Status Indicators
```scss
// Order status badges
.status-pending {
  font-size: var(--text-body-sm);        /* 12px */
  font-weight: var(--font-semibold);     /* 600 */
  color: var(--color-warning-dark);      /* #b45309 */
  background-color: var(--color-warning-light); /* #fef3c7 */
}

.status-completed {
  font-size: var(--text-body-sm);        /* 12px */
  font-weight: var(--font-semibold);     /* 600 */
  color: var(--color-success-dark);      /* #047857 */
  background-color: var(--color-success-light); /* #d1fae5 */
}

.status-cancelled {
  font-size: var(--text-body-sm);        /* 12px */
  font-weight: var(--font-semibold);     /* 600 */
  color: var(--color-error-dark);        /* #b91c1c */
  background-color: var(--color-error-light); /* #fee2e2 */
}
```

#### Quick Actions
```scss
// Action buttons
.worker-action-btn {
  font-size: var(--text-body-md);        /* 14px */
  font-weight: var(--font-medium);       /* 500 */
  color: white;
  background-color: var(--color-primary); /* #801336 */
}

// Action labels
.worker-action-label {
  font-size: var(--text-body-sm);        /* 12px */
  font-weight: var(--font-medium);       /* 500 */
  color: var(--color-primary-700);       /* #ab1e47 */
}
```

## Message and Alert Components

### System Messages
```scss
// Success messages
.alert-success {
  font-size: var(--text-body-md);        /* 14px */
  font-weight: var(--font-regular);      /* 400 */
  color: var(--color-success-dark);      /* #047857 */
  background-color: var(--color-success-light); /* #d1fae5 */
}

// Warning messages
.alert-warning {
  font-size: var(--text-body-md);        /* 14px */
  font-weight: var(--font-regular);      /* 400 */
  color: var(--color-warning-dark);      /* #b45309 */
  background-color: var(--color-warning-light); /* #fef3c7 */
}

// Error messages
.alert-error {
  font-size: var(--text-body-md);        /* 14px */
  font-weight: var(--font-regular);      /* 400 */
  color: var(--color-error-dark);        /* #b91c1c */
  background-color: var(--color-error-light); /* #fee2e2 */
}
```

### Toast Notifications
```scss
.toast-notification {
  font-size: var(--text-body-md);        /* 14px */
  font-weight: var(--font-regular);      /* 400 */
  line-height: 1.4;
}

.toast-title {
  font-size: var(--text-body-md);        /* 14px */
  font-weight: var(--font-semibold);     /* 600 */
  margin-bottom: 0.25rem;
}
```

## Accessibility Guidelines

### Contrast Requirements
All text must meet WCAG AA standards with a minimum contrast ratio of 4.5:1:

- **Primary text on white**: `#801336` on `#ffffff` = 7.2:1 ✓
- **Secondary text on white**: `#ab1e47` on `#ffffff` = 5.8:1 ✓
- **Muted text on white**: `#cc2857` on `#ffffff` = 4.6:1 ✓
- **White text on primary**: `#ffffff` on `#801336` = 7.2:1 ✓

### Font Size Minimums
- **Minimum body text**: 14px (0.875rem)
- **Minimum UI text**: 12px (0.75rem) - use sparingly
- **Never use**: Text smaller than 12px

### Line Height Guidelines
- **Body text**: 1.5 (150%)
- **Headings**: 1.2-1.4 (120%-140%)
- **UI elements**: 1.4 (140%)

## Implementation Examples

### CSS Utility Classes
```css
/* Text sizes */
.text-display-lg { font-size: var(--text-display-lg); }
.text-display-md { font-size: var(--text-display-md); }
.text-display-sm { font-size: var(--text-display-sm); }
.text-heading-xl { font-size: var(--text-heading-xl); }
.text-heading-lg { font-size: var(--text-heading-lg); }
.text-heading-md { font-size: var(--text-heading-md); }
.text-heading-sm { font-size: var(--text-heading-sm); }
.text-body-lg { font-size: var(--text-body-lg); }
.text-body-md { font-size: var(--text-body-md); }
.text-body-sm { font-size: var(--text-body-sm); }

/* Text colors */
.text-primary { color: var(--color-primary); }
.text-primary-light { color: var(--color-primary-700); }
.text-primary-muted { color: var(--color-primary-600); }
.text-success { color: var(--color-success); }
.text-warning { color: var(--color-warning); }
.text-error { color: var(--color-error); }

/* Font weights */
.font-light { font-weight: var(--font-light); }
.font-regular { font-weight: var(--font-regular); }
.font-medium { font-weight: var(--font-medium); }
.font-semibold { font-weight: var(--font-semibold); }
.font-bold { font-weight: var(--font-bold); }
```

### Tailwind Configuration
```javascript
// tailwind.config.js
module.exports = {
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#fdf2f4',
          100: '#fce7eb',
          200: '#f9d0d9',
          300: '#f4a8ba',
          400: '#ed7396',
          500: '#e04574',
          600: '#cc2857',
          700: '#ab1e47',
          800: '#901c42',
          900: '#801336',
          950: '#470a1e',
        }
      },
      fontSize: {
        'display-lg': ['3rem', { lineHeight: '1.1' }],
        'display-md': ['2.5rem', { lineHeight: '1.1' }],
        'display-sm': ['2rem', { lineHeight: '1.2' }],
        'heading-xl': ['1.875rem', { lineHeight: '1.3' }],
        'heading-lg': ['1.5rem', { lineHeight: '1.3' }],
        'heading-md': ['1.25rem', { lineHeight: '1.4' }],
        'heading-sm': ['1.125rem', { lineHeight: '1.4' }],
        'body-lg': ['1rem', { lineHeight: '1.5' }],
        'body-md': ['0.875rem', { lineHeight: '1.5' }],
        'body-sm': ['0.75rem', { lineHeight: '1.4' }],
      }
    }
  }
}
```

## Common Patterns and Best Practices

### Do's
✅ Use consistent font sizes from the defined scale
✅ Maintain proper contrast ratios for accessibility
✅ Apply semantic colors appropriately (success, warning, error)
✅ Use font weights to establish clear hierarchy
✅ Test typography at different zoom levels
✅ Ensure text remains readable on all background colors

### Don'ts
❌ Create custom font sizes outside the defined scale
❌ Use colors that don't harmonize with the primary palette
❌ Apply bold weight to large amounts of body text
❌ Use light font weights for small text sizes
❌ Ignore contrast requirements for accessibility
❌ Mix different typography systems within the same interface

### Quick Reference Checklist
- [ ] Font size is from the defined scale
- [ ] Color harmonizes with primary palette
- [ ] Contrast ratio meets WCAG AA standards (4.5:1 minimum)
- [ ] Font weight is appropriate for the text size
- [ ] Line height provides comfortable reading
- [ ] Text is readable at 200% zoom
- [ ] Semantic colors are used correctly for messages

This typography guide ensures consistent, accessible, and visually appealing text across all interfaces in the Campos Sport application.
