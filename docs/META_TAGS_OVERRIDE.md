# Meta Tags Override - Inertia Head Setup

## Problem gelöst ✅

Die Meta Tags aus Vue Components (`<Head>`) werden jetzt korrekt gerendert und überschreiben die Defaults.

## Wie es funktioniert:

### 1. **app.blade.php** - Minimal Defaults
```blade
<head>
    <!-- Basis Meta Tags die IMMER da sind -->
    <meta name="theme-color" content="...">
    <meta name="robots" content="index, follow">
    <meta property="og:locale" content="...">
    
    <!-- hreflang Links -->
    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
        <link rel="alternate" hreflang="{{ $localeCode }}" ... />
    @endforeach
    
    <!-- Inertia Head - hier kommen die Page-spezifischen Tags rein -->
    @inertiaHead
    
    @vite(...)
</head>
```

### 2. **Welcome.vue** - Vollständige Meta Tags
```vue
<template>
    <Head :title="$t('meta.title')">
        <meta name="description" :content="$t('meta.description')" />
        <link rel="canonical" :href="..." />
        
        <!-- Open Graph -->
        <meta property="og:type" content="website" />
        <meta property="og:title" :content="..." />
        <meta property="og:description" :content="..." />
        <meta property="og:image" content="..." />
        
        <!-- Twitter -->
        <meta name="twitter:card" content="..." />
        <meta name="twitter:title" content="..." />
        
        <!-- Structured Data -->
        <script type="application/ld+json">...</script>
    </Head>
</template>
```

### 3. **WorkoutPlan/Show.vue** - Spezifische SEO Tags
```vue
<template>
    <Head>
        <title>{{ props.meta.title }}</title>
        <meta name="description" :content="props.meta.description" />
        <link rel="canonical" :href="props.meta.canonical" />
        <meta name="keywords" :content="props.meta.keywords.join(', ')" />
        
        <!-- Open Graph -->
        <meta property="og:title" :content="props.meta.title" />
        <meta property="og:description" :content="props.meta.description" />
        <meta property="og:url" :content="props.meta.canonical" />
        <meta property="og:type" content="article" />
        
        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="props.meta.title" />
        <meta name="twitter:description" :content="props.meta.description" />
        
        <!-- Schema.org -->
        <script type="application/ld+json" v-html="schemaJson"></script>
    </Head>
</template>
```

## Wichtige Punkte:

### ✅ **DO:**
1. **Jede Page muss eigene Meta Tags definieren** via `<Head>` Component
2. **@inertiaHead muss VOR @vite kommen** in app.blade.php
3. **Vollständige Meta Tags in jeder Page** - title, description, canonical, OG, Twitter
4. **Dynamische Werte aus Props/i18n verwenden**

### ❌ **DON'T:**
1. **Keine hardcoded Meta Tags in app.blade.php** (außer theme-color, robots, locale)
2. **Nicht auf Default-Tags verlassen** - jede Page muss eigene haben
3. **Keine doppelten Meta Tag Definitionen**

## Vorteile:

### 1. **SEO-Optimierung pro Page**
```
/de/kostenloser-trainingsplan/abnehmen
→ Title: "Kostenloser Trainingsplan zum Abnehmen - 8 Wochen"
→ Description: "Effektiver Trainingsplan zum Abnehmen..."
→ Keywords: ['trainingsplan abnehmen', 'workout plan']

/en/free-workout-plan/weight-loss
→ Title: "Free Weight Loss Workout Plan - 8 Week Program"
→ Description: "Effective weight loss workout plan..."
→ Keywords: ['weight loss workout', 'fat loss training']
```

### 2. **Dynamic Content**
```vue
<!-- Aus Controller Props -->
<title>{{ props.meta.title }}</title>

<!-- Aus i18n -->
<title>{{ $t('meta.title') }}</title>

<!-- Computed -->
<title>{{ computedTitle }}</title>
```

### 3. **Schema.org Integration**
```vue
<script type="application/ld+json" v-html="schemaJson"></script>
```
→ Wird korrekt im `<head>` gerendert

## Testing:

### 1. **View Page Source** (CMD+U)
```html
<head>
    <title>Kostenloser Trainingsplan zum Abnehmen...</title>
    <meta name="description" content="Effektiver Trainingsplan...">
    <link rel="canonical" href="https://fitnessai.me/de/kostenloser-trainingsplan/abnehmen">
    
    <!-- Alle Meta Tags von Show.vue -->
</head>
```

### 2. **Google Rich Results Test**
```
https://search.google.com/test/rich-results
```
→ Teste deine URLs, sollte Schema.org Markup erkennen

### 3. **Facebook Sharing Debugger**
```
https://developers.facebook.com/tools/debug/
```
→ Teste Open Graph Tags

### 4. **Twitter Card Validator**
```
https://cards-dev.twitter.com/validator
```
→ Teste Twitter Meta Tags

## Beispiel für neue Pages:

```vue
<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

interface Props {
    meta: {
        title: string;
        description: string;
        canonical: string;
    };
}

const props = defineProps<Props>();
</script>

<template>
    <Head>
        <title>{{ props.meta.title }}</title>
        <meta name="description" :content="props.meta.description" />
        <link rel="canonical" :href="props.meta.canonical" />
        
        <!-- Open Graph -->
        <meta property="og:type" content="article" />
        <meta property="og:title" :content="props.meta.title" />
        <meta property="og:description" :content="props.meta.description" />
        <meta property="og:url" :content="props.meta.canonical" />
        <meta property="og:image" content="https://fitnessai.me/og-image.png" />
        
        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="props.meta.title" />
        <meta name="twitter:description" :content="props.meta.description" />
        <meta name="twitter:image" content="https://fitnessai.me/twitter-image.png" />
    </Head>
    
    <div>
        <!-- Page Content -->
    </div>
</template>
```

## Checkliste für jede neue Page:

- [ ] `<Head>` Component importiert
- [ ] `<title>` Tag gesetzt
- [ ] `<meta name="description">` gesetzt
- [ ] `<link rel="canonical">` gesetzt
- [ ] Open Graph Tags (og:title, og:description, og:url, og:image, og:type)
- [ ] Twitter Card Tags (twitter:card, twitter:title, twitter:description, twitter:image)
- [ ] Schema.org JSON-LD (optional, aber empfohlen für SEO)

---

**Status:** ✅ Meta Tag Override funktioniert vollständig!

Jede Page kann jetzt ihre eigenen Meta Tags definieren, die korrekt im `<head>` gerendert werden.

