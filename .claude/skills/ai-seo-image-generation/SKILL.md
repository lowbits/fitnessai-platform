---
name: ai-seo-image-generation
description: "Generate SEO-optimized AI image prompts and manage image assets for the fytrr website. Use this skill whenever creating images for blog posts, landing pages, OG images, or any web content that needs SEO-optimized visuals. Covers prompt crafting, file naming, alt text, optimization, and brand consistency."
---

# AI SEO Image Generation

Generate brand-consistent, SEO-optimized images for fytrr web content using AI image generation.

## Before You Start

Read the prompt engineering and optimization references:
-> `references/prompt-engineering.md`
-> `references/optimization-checklist.md`

## Quick Reference

### 1. Prompt Formula

```
[Subject] + [Style] + [Colors] + [Mood] + [Composition]
```

**Example:**
"Digital marketing strategy concept + modern flat illustration + blue, orange, white + professional confident + centered composition"

### 2. File Naming Convention

**Structure:** `[keyword]-[descriptor]-[modifier].webp`

Good: `content-marketing-strategy-framework-2025.webp`
Bad: `IMG_7834.jpg`, `midjourney-export.png`, `image1.webp`

### 3. Alt Text Strategy

**Do:** "Business team analyzing growth metrics on digital dashboard with upward trending graphs"
**Don't:** "AI generated image", "stock photo", "illustration"

Include: primary keyword naturally, actual image description, page context, accessibility info.

### 4. Technical Requirements

- Format: WebP (preferred), fallback to optimized PNG/JPG
- Max file size: <200KB
- Responsive sizing for mobile
- Appropriate dimensions for placement context

### 5. Image Types by Content Stage

| Stage | Image Type |
|-------|-----------|
| Awareness | Abstract concepts, educational diagrams |
| Consideration | Comparison visuals, feature illustrations |
| Decision | Product mockups, result visualizations |

### 6. When Generating Prompts

Always specify these elements:
- **Subject matter** - clear and specific, not vague
- **Style** - flat design, photorealistic, 3D, illustration, etc.
- **Color scheme** - brand colors or contextually appropriate
- **Mood/tone** - professional, playful, energetic, calm
- **Composition** - centered, rule of thirds, wide shot, close-up

### 7. Common Pitfalls to Avoid

- Generic "AI art" aesthetic
- Unrealistic hands/faces (avoid people when possible, use abstract/conceptual)
- Brand inconsistency across image series
- Overuse of same style
- Missing SEO optimization (file name, alt text, size)
- No schema markup for images
