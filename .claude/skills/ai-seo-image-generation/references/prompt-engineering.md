# AI Image Prompt Engineering for SEO

## The Prompt Formula

Every image prompt should follow this structure:

```
[Subject] + [Style] + [Colors] + [Mood] + [Composition]
```

### Element Details

#### Subject Matter
Be specific and descriptive. The subject should relate directly to the page content and target keywords.

**Bad:** "fitness image"
**Good:** "Person tracking calories on a smartphone app with healthy meal prep containers on a kitchen counter"

#### Style Options
Choose based on content type and brand consistency:

| Style | Best For |
|-------|----------|
| Modern flat illustration | Blog headers, feature explanations |
| Photorealistic | Product pages, testimonials |
| 3D render | App screenshots, feature highlights |
| Minimalist line art | Icons, diagrams, how-to guides |
| Isometric illustration | Process flows, feature overviews |
| Abstract/geometric | Background images, hero sections |

#### Color Scheme
- Use brand colors when possible for consistency
- Specify exact colors or palettes in the prompt
- Consider contrast and readability when text overlays are needed

#### Mood/Tone
Match the content's intent:
- **Professional/confident** - B2B content, authority pages
- **Energetic/motivating** - Fitness content, workout pages
- **Calm/trustworthy** - Health content, nutrition guides
- **Playful/friendly** - Social content, beginner guides

#### Composition
- **Centered** - Hero images, OG images
- **Rule of thirds** - Blog post images
- **Wide/panoramic** - Banner images
- **Close-up/detail** - Feature highlights
- **Negative space** - Images needing text overlay

## Prompt Templates by Content Type

### Blog Post Header
```
[Topic visual metaphor] + modern flat illustration style + [brand colors] + [content mood] + wide composition with negative space on [left/right] for text overlay, clean background
```

### OG/Social Share Image
```
[Core concept visualization] + bold graphic design + [brand colors] + professional + centered composition, 1200x630 dimensions, high contrast for small preview
```

### Landing Page Hero
```
[Product/service in action] + photorealistic/illustration + [brand colors] + aspirational and professional + dynamic composition with clear focal point
```

### How-To/Tutorial Image
```
[Step visualization] + clean minimalist style + [brand colors] + instructional and clear + centered with numbered elements
```

### Calculator/Tool Page
```
[Tool concept visualization] + modern UI illustration + [brand colors] + professional and approachable + isometric or flat perspective
```

## Creating Image Series

For consistent brand visuals across a content series:

1. **Define base parameters** - Lock in style, color scheme, and composition rules
2. **Create a template prompt** - Use variables for the changing elements only
3. **Document successful prompts** - Save prompts that produce good results
4. **Maintain a style guide** - Reference images that match the brand

### Series Template Example
```
Base: "[SUBJECT] + modern flat illustration + blue (#2563EB), white, light gray + professional + centered composition, clean white background"

Blog 1: SUBJECT = "calorie counting concept with food icons and calculator"
Blog 2: SUBJECT = "meal planning concept with weekly calendar and food groups"
Blog 3: SUBJECT = "workout tracking concept with progress charts and dumbbells"
```

## Avoiding Common Prompt Mistakes

1. **Too vague** - "fitness picture" -> be specific about what's shown
2. **Too complex** - Don't request 10+ elements, keep it focused
3. **People-heavy** - AI struggles with realistic people; prefer conceptual/abstract when possible
4. **No style specified** - Always specify a style to avoid generic AI aesthetic
5. **Ignoring dimensions** - Specify aspect ratio/dimensions for the intended use
6. **Brand inconsistent** - Always reference brand colors and style guidelines
