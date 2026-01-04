# Body Progress Tracking API

## Overview

Die Body Progress Tracking API ermöglicht es Benutzern, ihre körperlichen Fortschritte zu verfolgen, einschließlich Gewicht, Körperfettanteil, Muskelmasse und verschiedener Körperumfänge.

## Features

- **Gewichtsverfolgung** (Pflichtfeld)
- **Optionale Metriken**:
  - Körperfettanteil (%)
  - Muskelmasse (kg)
  - Taillenumfang (cm)
  - Brustumfang (cm)
  - Hüftumfang (cm)
  - Armumfang (cm)
  - Oberschenkelumfang (cm)
- **Notizen** für zusätzliche Informationen
- **Benutzerdefiniertes Aufzeichnungsdatum**

## Endpoints

### 1. Track Body Progress

**POST** `/api/v2/track/body-progress`

Erstellt einen neuen Body-Progress-Eintrag.

#### Request Body

```json
{
  "weight": 75.5,                    // Required (20-500 kg)
  "body_fat_percentage": 18.5,       // Optional (0-100%)
  "muscle_mass": 65.0,               // Optional (0-300 kg)
  "waist_circumference": 85.0,       // Optional (0-300 cm)
  "chest_circumference": 100.0,      // Optional (0-300 cm)
  "hip_circumference": 95.0,         // Optional (0-300 cm)
  "arm_circumference": 35.0,         // Optional (0-100 cm)
  "thigh_circumference": 55.0,       // Optional (0-150 cm)
  "notes": "Feeling strong today",   // Optional (max 1000 chars)
  "recorded_at": "2026-01-04 10:00:00" // Optional (defaults to now)
}
```

#### Minimal Request (nur Gewicht)

```json
{
  "weight": 75.5
}
```

#### Response (201 Created)

```json
{
  "message": "Body progress tracked successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "weight": "75.50",
    "body_fat_percentage": "18.50",
    "muscle_mass": "65.00",
    "waist_circumference": "85.00",
    "chest_circumference": "100.00",
    "hip_circumference": "95.00",
    "arm_circumference": "35.00",
    "thigh_circumference": "55.00",
    "notes": "Feeling strong today",
    "recorded_at": "2026-01-04T10:00:00.000000Z",
    "created_at": "2026-01-04T10:00:00.000000Z",
    "updated_at": "2026-01-04T10:00:00.000000Z"
  }
}
```

### 2. Update Body Progress

**PUT** `/api/v2/track/body-progress/{id}`

Aktualisiert einen bestehenden Body-Progress-Eintrag. Es können nur die Felder aktualisiert werden, die im Request enthalten sind.

#### Request Body

```json
{
  "weight": 76.0,
  "notes": "Updated weight"
}
```

#### Response (200 OK)

```json
{
  "message": "Body progress updated successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "weight": "76.00",
    "notes": "Updated weight",
    // ... other fields
  }
}
```

### 3. Get Body Progress History

**GET** `/api/v2/track/body-progress`

Ruft die Body-Progress-Historie des Benutzers ab (sortiert nach `recorded_at` absteigend).

#### Query Parameters

- `limit` (optional): Anzahl der Ergebnisse (1-100)
- `from` (optional): Startdatum (Y-m-d Format)
- `to` (optional): Enddatum (Y-m-d Format)

#### Examples

```
GET /api/v2/track/body-progress
GET /api/v2/track/body-progress?limit=10
GET /api/v2/track/body-progress?from=2026-01-01
GET /api/v2/track/body-progress?from=2026-01-01&to=2026-01-31
```

#### Response (200 OK)

```json
{
  "data": [
    {
      "id": 2,
      "weight": "76.00",
      "recorded_at": "2026-01-04T10:00:00.000000Z",
      // ... other fields
    },
    {
      "id": 1,
      "weight": "75.50",
      "recorded_at": "2026-01-03T10:00:00.000000Z",
      // ... other fields
    }
  ],
  "count": 2
}
```

### 4. Get Latest Body Progress

**GET** `/api/v2/track/body-progress/latest`

Ruft den neuesten Body-Progress-Eintrag ab.

#### Response (200 OK)

```json
{
  "data": {
    "id": 2,
    "user_id": 1,
    "weight": "76.00",
    "body_fat_percentage": "18.00",
    // ... other fields
    "recorded_at": "2026-01-04T10:00:00.000000Z"
  }
}
```

#### Response (200 OK) - No entries

```json
{
  "message": "No body progress entries found",
  "data": null
}
```

### 5. Delete Body Progress

**DELETE** `/api/v2/track/body-progress/{id}`

Löscht einen Body-Progress-Eintrag.

#### Response (200 OK)

```json
{
  "message": "Body progress entry deleted successfully"
}
```

#### Response (404 Not Found)

```json
{
  "error": "Body progress entry not found"
}
```

## Error Responses

### 422 Validation Error

```json
{
  "error": "Validation failed",
  "messages": {
    "weight": [
      "The weight field is required."
    ],
    "body_fat_percentage": [
      "The body fat percentage must not be greater than 100."
    ]
  }
}
```

### 404 Not Found

```json
{
  "error": "Body progress entry not found"
}
```

### 401 Unauthorized

```json
{
  "message": "Unauthenticated."
}
```

## Validation Rules

| Field | Type | Required | Min | Max | Description |
|-------|------|----------|-----|-----|-------------|
| weight_kg | numeric | Yes | 20 | 500 | Gewicht in kg |
| body_fat_percentage | numeric | No | 0 | 100 | Körperfettanteil in % |
| muscle_mass_kg | numeric | No | 0 | 300 | Muskelmasse in kg |
| waist_circumference_cm | numeric | No | 0 | 300 | Taillenumfang in cm |
| chest_circumference_cm | numeric | No | 0 | 300 | Brustumfang in cm |
| hip_circumference_cm | numeric | No | 0 | 300 | Hüftumfang in cm |
| arm_circumference_cm | numeric | No | 0 | 100 | Armumfang in cm |
| thigh_circumference_cm | numeric | No | 0 | 150 | Oberschenkelumfang in cm |
| notes | string | No | - | 1000 | Notizen |
| recorded_at | datetime | No | - | - | Aufzeichnungsdatum |

## Database Schema

```sql
CREATE TABLE body_progress (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  weight DECIMAL(5,2) NOT NULL,
  body_fat_percentage DECIMAL(5,2) NULL,
  muscle_mass DECIMAL(5,2) NULL,
  waist_circumference DECIMAL(5,2) NULL,
  chest_circumference DECIMAL(5,2) NULL,
  hip_circumference DECIMAL(5,2) NULL,
  arm_circumference DECIMAL(5,2) NULL,
  thigh_circumference DECIMAL(5,2) NULL,
  notes TEXT NULL,
  recorded_at TIMESTAMP NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_user_recorded (user_id, recorded_at)
);
```

## Usage Examples

### React Native / Mobile App

```javascript
// Track weight only
const trackWeight = async (weight) => {
  const response = await fetch('/api/v2/track/body-progress', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ weight }),
  });
  return response.json();
};

// Track full body progress
const trackFullProgress = async (data) => {
  const response = await fetch('/api/v2/track/body-progress', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      weight: data.weight,
      body_fat_percentage: data.bodyFat,
      muscle_mass: data.muscleMass,
      waist_circumference: data.waist,
      chest_circumference: data.chest,
      hip_circumference: data.hip,
      arm_circumference: data.arm,
      thigh_circumference: data.thigh,
      notes: data.notes,
    }),
  });
  return response.json();
};

// Get progress history for charts
const getProgressHistory = async (days = 30) => {
  const from = new Date();
  from.setDate(from.getDate() - days);
  
  const response = await fetch(
    `/api/v2/track/body-progress?from=${from.toISOString().split('T')[0]}`,
    {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    }
  );
  return response.json();
};

// Get latest weight
const getLatestWeight = async () => {
  const response = await fetch('/api/v2/track/body-progress/latest', {
    headers: {
      'Authorization': `Bearer ${token}`,
    },
  });
  const data = await response.json();
  return data.data?.weight;
};
```

## Security

- Alle Endpoints erfordern Authentifizierung via Sanctum Token
- Benutzer können nur ihre eigenen Body-Progress-Einträge sehen, bearbeiten und löschen
- Body-Progress-Einträge werden automatisch gelöscht, wenn der Benutzer gelöscht wird (CASCADE)

## Testing

Alle Funktionen sind durch umfangreiche Tests abgedeckt:

```bash
php artisan test --filter=BodyProgressTrackingTest
```

Tests umfassen:
- ✅ Track mit nur Gewicht
- ✅ Track mit allen Feldern
- ✅ Validierung (Pflichtfelder, Bereiche)
- ✅ Update von Einträgen
- ✅ Get Historie mit Filterung
- ✅ Get Latest
- ✅ Delete Einträge
- ✅ Autorisierung (nur eigene Daten)
- ✅ Cascade Delete bei User-Löschung

