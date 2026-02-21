# Color Theme Reference

## Royal Blue Theme

This authentication system uses a professional royal blue color scheme throughout the interface.

### Primary Colors

#### Main Gradient Background
```css
background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
```
- **Start**: `#1e3a8a` (Navy Blue - Blue 900)
- **End**: `#2563eb` (Royal Blue - Blue 600)

#### Primary Button Gradient
```css
background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
```
- Same as background for consistency

#### Accent/Link Color
```css
color: #2563eb;
```
- **Color**: `#2563eb` (Royal Blue - Blue 600)

### Secondary Colors

#### Table Header Background
```css
background: #2563eb;
color: white;
```
- **Background**: `#2563eb` (Royal Blue)
- **Text**: `#ffffff` (White)

#### Card Header Border
```css
border-bottom: 2px solid #2563eb;
```
- **Border**: `#2563eb` (Royal Blue)

#### Input Focus Border
```css
border-color: #2563eb;
```
- **Border**: `#2563eb` (Royal Blue)

### Button Hover Effects

#### Primary Button Hover
```css
box-shadow: 0 5px 15px rgba(37, 99, 235, 0.4);
```
- **Shadow Color**: Royal Blue at 40% opacity
- **RGB**: `rgba(37, 99, 235, 0.4)`

### Alert Colors

#### Success
```css
background: #d4edda;
color: #155724;
border: 1px solid #c3e6cb;
```
- **Background**: Light green
- **Text**: Dark green
- **Border**: Medium green

#### Error
```css
background: #f8d7da;
color: #721c24;
border: 1px solid #f5c6cb;
```
- **Background**: Light red
- **Text**: Dark red
- **Border**: Medium red

#### Warning
```css
background: #fff3cd;
color: #856404;
border: 1px solid #ffeeba;
```
- **Background**: Light yellow
- **Text**: Dark brown
- **Border**: Medium yellow

#### Info
```css
background: #d1ecf1;
color: #0c5460;
border: 1px solid #bee5eb;
```
- **Background**: Light cyan
- **Text**: Dark teal
- **Border**: Medium cyan

### Neutral Colors

#### White
```css
color: #ffffff;
```
- Used for button text and card backgrounds

#### Light Gray
```css
background: #f8f9fa;
```
- Used for card backgrounds

#### Medium Gray
```css
color: #666666;
```
- Used for descriptive text

#### Dark Gray
```css
color: #333333;
```
- Used for headings and body text

#### Border Gray
```css
border: 2px solid #e1e8ed;
```
- Used for input borders

### Status Badge Colors

#### Success Badge
```css
background: #d4edda;
color: #155724;
```

#### Failed Badge
```css
background: #f8d7da;
color: #721c24;
```

## Color Palette Summary

| Purpose | Hex Code | RGB | Usage |
|---------|----------|-----|-------|
| Navy Blue | `#1e3a8a` | `rgb(30, 58, 138)` | Gradient start, dark blue |
| Royal Blue | `#2563eb` | `rgb(37, 99, 235)` | Gradient end, accents, links |
| White | `#ffffff` | `rgb(255, 255, 255)` | Text on dark backgrounds |
| Light Gray | `#f8f9fa` | `rgb(248, 249, 250)` | Card backgrounds |
| Medium Gray | `#666666` | `rgb(102, 102, 102)` | Descriptive text |
| Dark Gray | `#333333` | `rgb(51, 51, 51)` | Headings, body text |
| Border Gray | `#e1e8ed` | `rgb(225, 232, 237)` | Borders |

## Tailwind CSS Equivalents

If using Tailwind CSS, these are the equivalent classes:

```
Navy Blue (#1e3a8a)     → bg-blue-900
Royal Blue (#2563eb)    → bg-blue-600
```

## Changing the Color Scheme

To change the entire color scheme, update these key values in `/public/assets/css/style.css`:

### 1. Background Gradient (line 10)
```css
background: linear-gradient(135deg, #YourDarkColor 0%, #YourLightColor 100%);
```

### 2. Primary Button (line 130)
```css
background: linear-gradient(135deg, #YourDarkColor 0%, #YourLightColor 100%);
```

### 3. Button Hover Shadow (line 137)
```css
box-shadow: 0 5px 15px rgba(YourR, YourG, YourB, 0.4);
```

### 4. Links and Accents (lines 105, 218, 74)
```css
color: #YourAccentColor;
border-color: #YourAccentColor;
```

### 5. Table Headers (line 307)
```css
background: #YourAccentColor;
```

### 6. Card Header Borders (line 267)
```css
border-bottom: 2px solid #YourAccentColor;
```

## Accessibility

All color combinations meet WCAG 2.1 Level AA standards for contrast:

- **Royal Blue on White**: 4.5:1 (AA Large Text)
- **Navy Blue on White**: 10.4:1 (AAA)
- **White on Royal Blue**: 4.5:1 (AA)
- **White on Navy Blue**: 10.4:1 (AAA)
- **Dark Gray on White**: 12.6:1 (AAA)

## Design Philosophy

The royal blue theme was chosen for:
- **Professionalism**: Blue conveys trust and security
- **Readability**: Strong contrast ratios
- **Modernity**: Gradient effects and smooth transitions
- **Brand Neutrality**: Suitable for any brand customization
- **Accessibility**: Meets WCAG standards

---

**Version**: 2.0.0  
**Theme**: Royal Blue  
**Last Updated**: February 2026
