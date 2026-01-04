# Calorie Tracking Tests

## Vor dem Ausführen der Tests

Stelle sicher, dass die Migrationen ausgeführt wurden:

```bash
php artisan migrate
```

Oder für die Test-Datenbank:

```bash
php artisan migrate --env=testing
```

## Tests ausführen

### Alle Calorie Tracking Tests:
```bash
php artisan test tests/Feature/CalorieTrackingTest.php
```

### Alle Calorie-bezogenen Tests (inkl. Plan-Integration):
```bash
php artisan test --filter=Calorie
```

### Einzelnen Test ausführen:
```bash
php artisan test --filter="user can track calories without meal"
```

## Mögliche Probleme

### Problem: "Table 'calorie_trackings' doesn't exist"

**Lösung**: Migration ausführen
```bash
php artisan migrate
```

### Problem: Tests produzieren keine Ausgabe

**Lösung 1**: Prüfe ob die Test-Datenbank existiert
```bash
php artisan db:show --database=testing
```

**Lösung 2**: Führe Migrationen fresh aus
```bash
php artisan migrate:fresh --env=testing
```

**Lösung 3**: Verwende `--verbose` flag
```bash
php artisan test tests/Feature/CalorieTrackingTest.php --verbose
```

## Test-Übersicht

Die CalorieTrackingTest.php enthält 15 Tests:

1. ✅ user can track calories without meal
2. ✅ user can track calories with meal reference
3. ✅ user can track calories with only required fields
4. ✅ user can get their calorie trackings
5. ✅ user can filter calorie trackings by date range
6. ✅ user can get a specific calorie tracking
7. ✅ user can update a calorie tracking
8. ✅ user cannot access another users calorie tracking
9. ✅ calorie tracking requires authentication
10. ✅ calorie tracking requires valid tracked_date
11. ✅ calorie tracking requires valid calories
12. ✅ calorie tracking validates macros range
13. ✅ user can delete a calorie tracking
14. ✅ user cannot delete another users calorie tracking
15. ✅ multiple calorie entries can be tracked on same day

## Bekannte Probleme

### Factory Status

Die `MealPlanFactory` und `WorkoutPlanFactory` verwenden jetzt standardmäßig `status => 'generated'` statt zufällige Werte. Dies stellt sicher, dass Tests konsistent funktionieren.

Wenn du für spezifische Tests andere Status benötigst:
```php
MealPlan::factory()->create(['status' => 'pending'])
MealPlan::factory()->create(['status' => 'failed'])
```

