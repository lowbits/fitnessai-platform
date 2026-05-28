---
name: index-now
description: "Submit URLs to Bing IndexNow for faster crawling and indexing. Use when deploying new pages, updating meta tags, or any SEO-relevant change. Invoke with /index-now to auto-detect changed pages from recent commits, or /index-now <url1> <url2> to submit specific URLs."
---

# IndexNow URL Submission

Submit changed or new URLs to Bing via the IndexNow API for faster crawling.

## Configuration

- **Host:** fytrr.com
- **Key:** cadcb149cb1d40cead98f92e71c0c536
- **Key location:** https://fytrr.com/cadcb149cb1d40cead98f92e71c0c536.txt

## Usage

### With specific URLs

If the user provides URLs as arguments, submit exactly those URLs.

### Auto-detect from git changes

If no URLs are provided, detect which pages changed by looking at the last commit (`git diff --name-only HEAD~1`) and map changed files to public URLs using the rules below.

## File-to-URL Mapping Rules

When auto-detecting, check which files changed and collect the matching URL sets:

| Changed file | URLs to submit |
|---|---|
| `lang/en/routes.php` or `lang/de/routes.php` | All pages |
| `resources/js/i18n/locales.ts` | All pages (meta changes) |
| `config/freeWorkouts.php` | All workout plan URLs (both locales) |
| `config/blog.php` | All blog URLs (read slugs from config) |
| `resources/js/pages/Landing/*` | The 3 landing page URLs |
| `resources/js/pages/Welcome.vue` | Homepage URLs (`/en`, `/de`) |
| `resources/js/pages/CalorieCalculator.vue` | Calorie calculator URLs |
| `resources/js/pages/WorkoutPlan/*` | All workout plan URLs |
| `resources/js/pages/Blog/*` | All blog URLs |
| `resources/js/pages/About.vue` | About URLs |
| `app/Http/Controllers/*Controller.php` | Determine from controller name |

## Full URL List ("All Pages")

```
https://fytrr.com/en
https://fytrr.com/de
https://fytrr.com/en/free-workout-plan
https://fytrr.com/de/kostenloser-trainingsplan
https://fytrr.com/en/free-workout-plan/weight-loss
https://fytrr.com/en/free-workout-plan/muscle-gain
https://fytrr.com/en/free-workout-plan/beginner
https://fytrr.com/en/free-workout-plan/home
https://fytrr.com/en/free-workout-plan/women
https://fytrr.com/en/free-workout-plan/strength
https://fytrr.com/en/free-workout-plan/fat-loss
https://fytrr.com/en/free-workout-plan/abs
https://fytrr.com/en/free-workout-plan/over-40
https://fytrr.com/en/free-workout-plan/new-year-reset
https://fytrr.com/de/kostenloser-trainingsplan/abnehmen
https://fytrr.com/de/kostenloser-trainingsplan/muskelaufbau
https://fytrr.com/de/kostenloser-trainingsplan/anfaenger
https://fytrr.com/de/kostenloser-trainingsplan/zuhause
https://fytrr.com/de/kostenloser-trainingsplan/frauen
https://fytrr.com/de/kostenloser-trainingsplan/krafttraining
https://fytrr.com/de/kostenloser-trainingsplan/fettabbau
https://fytrr.com/de/kostenloser-trainingsplan/bauchmuskeltraining
https://fytrr.com/de/kostenloser-trainingsplan/ueber-40-training
https://fytrr.com/de/kostenloser-trainingsplan/neujahrs-trainingsplan
https://fytrr.com/en/free-workout-and-meal-plan
https://fytrr.com/de/persoenlicher-ernaehrungsplan
https://fytrr.com/en/ai-workout-plan-generator
https://fytrr.com/en/free-tools/calorie-calculator
https://fytrr.com/de/kostenlose-tools/kalorienrechner
https://fytrr.com/en/about
https://fytrr.com/de/ueber-uns
```

Blog URLs should be read dynamically from `config/blog.php` by extracting the slug keys under `en` and `de`.

## API Call

Submit via POST:

```bash
curl -s -X POST "https://api.indexnow.org/IndexNow" \
  -H "Content-Type: application/json" \
  -d '{
    "host": "fytrr.com",
    "key": "cadcb149cb1d40cead98f92e71c0c536",
    "keyLocation": "https://fytrr.com/cadcb149cb1d40cead98f92e71c0c536.txt",
    "urlList": [<urls>]
  }'
```

**Expected responses:**
- 200: URLs submitted successfully
- 202: URLs accepted for processing
- 422: Invalid request (check URL format)
- 429: Rate limited (wait and retry)

## Output

Always show:
1. How many URLs will be submitted
2. The full URL list
3. The HTTP status code from the API
4. Success or error message