# SEO Image Optimization Checklist

## Post-Generation Checklist

After generating any AI image, apply all of these before publishing:

### 1. File Format & Size
- [ ] Convert to WebP format (preferred) or optimized PNG/JPG
- [ ] File size under 200KB (under 100KB preferred for blog images)
- [ ] Use appropriate quality setting (80-85% for photos, lossless for illustrations with sharp edges)

### 2. File Naming
**Structure:** `[primary-keyword]-[descriptor]-[modifier].webp`

| Context | Example |
|---------|---------|
| Blog header | `kalorienbedarf-berechnen-guide.webp` |
| OG image | `kalorienrechner-og-image.webp` |
| Feature image | `meal-tracking-app-feature.webp` |
| How-to step | `workout-plan-erstellen-schritt-3.webp` |

Rules:
- Lowercase only
- Hyphens between words (no underscores)
- Include primary keyword
- Be descriptive but concise
- No generic names (image1, photo, screenshot)

### 3. Image Dimensions

| Placement | Dimensions | Notes |
|-----------|-----------|-------|
| OG/Social | 1200x630 | Required for social sharing |
| Blog header | 1200x630 or 1200x800 | Wide format works best |
| Blog inline | 800x600 or 1000x750 | Depends on content width |
| Hero image | 1920x1080 or 1600x900 | Full-width sections |
| Thumbnail | 400x300 or 600x400 | Card/grid layouts |

### 4. Alt Text

**Formula:** Describe what's in the image + include primary keyword naturally

**Good examples:**
- "Kalorienrechner zeigt täglichen Kalorienbedarf basierend auf Alter, Gewicht und Aktivitätslevel"
- "Wochenplan für Meal Prep mit ausgewogenen Mahlzeiten und Nährwertangaben"
- "Trainingsplan-Übersicht mit Fortschrittsdiagramm für Krafttraining"

**Bad examples:**
- "AI generated image" / "Bild"
- "fitness" / "food"
- "Screenshot" / "Foto"

**Rules:**
- 125 characters or fewer (ideal)
- Natural language, not keyword stuffing
- Describe what a user would see
- Include primary keyword where natural
- Consider accessibility (screen readers)

### 5. Responsive Images

When implementing in templates:
```html
<!-- Use srcset for responsive sizes -->
<img
  src="/images/blog/kalorienbedarf-berechnen-guide.webp"
  srcset="
    /images/blog/kalorienbedarf-berechnen-guide-400w.webp 400w,
    /images/blog/kalorienbedarf-berechnen-guide-800w.webp 800w,
    /images/blog/kalorienbedarf-berechnen-guide-1200w.webp 1200w
  "
  sizes="(max-width: 640px) 400px, (max-width: 1024px) 800px, 1200px"
  alt="Descriptive alt text here"
  width="1200"
  height="630"
  loading="lazy"
/>
```

### 6. Schema Markup

For key images, add structured data:
```json
{
  "@type": "ImageObject",
  "url": "https://fytrr.com/images/blog/kalorienbedarf-berechnen-guide.webp",
  "width": 1200,
  "height": 630,
  "caption": "Descriptive caption matching alt text"
}
```

### 7. Image Sitemap

Ensure images are included in the sitemap for Google Image Search indexing.

### 8. Surrounding Content

- Image should be near relevant text content
- Use descriptive captions where appropriate
- Internal links near images boost relevance
- Place important images above the fold when possible

## Optimization Commands

```bash
# Convert to WebP (requires cwebp)
cwebp -q 82 input.png -o output.webp

# Resize with ImageMagick
convert input.webp -resize 1200x630 -quality 82 output.webp

# Batch optimize all images in a directory
for f in *.png; do cwebp -q 82 "$f" -o "${f%.png}.webp"; done
```

## Laravel Integration

When storing generated images in the Laravel project:
- Store in `public/images/[section]/` (e.g., `public/images/blog/`, `public/images/og/`)
- Reference via `asset('images/blog/filename.webp')` in Blade
- For Inertia/Vue pages, use `/images/blog/filename.webp` path
- Consider using Laravel's Storage facade for dynamic/generated images
