# Move Workout to Tomorrow - Mobile Integration

## React Native / TypeScript Beispiel

### API Service

```typescript
// services/workoutService.ts

interface MoveWorkoutResponse {
  message: string;
  rest_day: {
    id: number;
    date: string;
    name: string;
    type: string;
    description: string;
  };
  moved_workout: {
    id: number;
    date: string;
    name: string;
    type: string;
    duration_minutes: number;
    exercises_count: number;
  };
}

interface ApiError {
  error: string;
  message: string;
  tomorrow_workout?: {
    id: number;
    name: string;
    type: string;
  };
  plan_end_date?: string;
}

export const moveWorkoutToTomorrow = async (
  workoutId: number,
  token: string
): Promise<MoveWorkoutResponse> => {
  const response = await fetch(
    `${API_BASE_URL}/api/v2/workouts/${workoutId}/move`,
    {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
      },
    }
  );

  if (!response.ok) {
    const error: ApiError = await response.json();
    throw new Error(error.message);
  }

  return response.json();
};
```

### React Component

```typescript
// components/WorkoutActions.tsx

import React, { useState } from 'react';
import { View, Text, TouchableOpacity, Alert, ActivityIndicator } from 'react-native';
import { moveWorkoutToTomorrow } from '../services/workoutService';

interface WorkoutActionsProps {
  workoutId: number;
  workoutName: string;
  token: string;
  onSuccess?: () => void;
}

export const WorkoutActions: React.FC<WorkoutActionsProps> = ({
  workoutId,
  workoutName,
  token,
  onSuccess,
}) => {
  const [isMoving, setIsMoving] = useState(false);

  const handleMoveToTomorrow = () => {
    Alert.alert(
      'Workout verschieben',
      `Möchtest du "${workoutName}" auf morgen verschieben? Heute wird ein Ruhetag eingeplant.`,
      [
        {
          text: 'Abbrechen',
          style: 'cancel',
        },
        {
          text: 'Verschieben',
          onPress: async () => {
            setIsMoving(true);
            try {
              const result = await moveWorkoutToTomorrow(workoutId, token);
              
              Alert.alert(
                'Erfolgreich verschoben',
                `Heute: ${result.rest_day.name}\nMorgen: ${result.moved_workout.name}`,
                [
                  {
                    text: 'OK',
                    onPress: () => onSuccess?.(),
                  },
                ]
              );
            } catch (error) {
              Alert.alert(
                'Fehler',
                error instanceof Error 
                  ? error.message 
                  : 'Workout konnte nicht verschoben werden'
              );
            } finally {
              setIsMoving(false);
            }
          },
        },
      ]
    );
  };

  return (
    <View style={styles.container}>
      <TouchableOpacity
        style={[styles.button, isMoving && styles.buttonDisabled]}
        onPress={handleMoveToTomorrow}
        disabled={isMoving}
      >
        {isMoving ? (
          <ActivityIndicator color="#fff" />
        ) : (
          <>
            <Text style={styles.buttonIcon}>📅</Text>
            <Text style={styles.buttonText}>Auf morgen verschieben</Text>
          </>
        )}
      </TouchableOpacity>
    </View>
  );
};

const styles = {
  container: {
    padding: 16,
  },
  button: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#007AFF',
    padding: 16,
    borderRadius: 12,
  },
  buttonDisabled: {
    opacity: 0.6,
  },
  buttonIcon: {
    fontSize: 20,
    marginRight: 8,
  },
  buttonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
};
```

### State Management (Redux Toolkit)

```typescript
// store/workoutSlice.ts

import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import { moveWorkoutToTomorrow } from '../services/workoutService';

export const moveWorkoutToTomorrowAsync = createAsyncThunk(
  'workout/moveToTomorrow',
  async ({ workoutId, token }: { workoutId: number; token: string }) => {
    return await moveWorkoutToTomorrow(workoutId, token);
  }
);

const workoutSlice = createSlice({
  name: 'workout',
  initialState: {
    isMoving: false,
    error: null as string | null,
  },
  reducers: {},
  extraReducers: (builder) => {
    builder
      .addCase(moveWorkoutToTomorrowAsync.pending, (state) => {
        state.isMoving = true;
        state.error = null;
      })
      .addCase(moveWorkoutToTomorrowAsync.fulfilled, (state, action) => {
        state.isMoving = false;
        // Refresh plan data here
      })
      .addCase(moveWorkoutToTomorrowAsync.rejected, (state, action) => {
        state.isMoving = false;
        state.error = action.error.message || 'Unknown error';
      });
  },
});

export default workoutSlice.reducer;
```

### Error Handling

```typescript
// utils/workoutErrors.ts

export const getWorkoutMoveErrorMessage = (error: ApiError): string => {
  switch (error.error) {
    case 'Invalid operation':
      if (error.message.includes('rest day')) {
        return 'Ruhetage können nicht verschoben werden.';
      }
      if (error.message.includes('beyond plan duration')) {
        return `Workout kann nicht über das Plan-Ende (${error.plan_end_date}) hinaus verschoben werden.`;
      }
      return error.message;
      
    case 'Conflict':
      return `Morgen ist bereits ein Workout geplant: ${error.tomorrow_workout?.name}`;
      
    case 'Unauthorized':
      return 'Du hast keine Berechtigung für dieses Workout.';
      
    case 'Not found':
      return 'Workout wurde nicht gefunden.';
      
    default:
      return 'Ein Fehler ist aufgetreten. Bitte versuche es erneut.';
  }
};
```

### Usage in Screen

```typescript
// screens/WorkoutDetailScreen.tsx

import React from 'react';
import { View, ScrollView, Text } from 'react-native';
import { WorkoutActions } from '../components/WorkoutActions';
import { useSelector } from 'react-redux';
import { useNavigation } from '@react-navigation/native';

export const WorkoutDetailScreen: React.FC = () => {
  const navigation = useNavigation();
  const { workout, token } = useSelector((state: RootState) => ({
    workout: state.workout.currentWorkout,
    token: state.auth.token,
  }));

  const handleMoveSuccess = () => {
    // Refresh the plan
    navigation.goBack();
    // Or trigger a refetch of the plan
  };

  if (!workout) {
    return <Text>Loading...</Text>;
  }

  return (
    <ScrollView>
      <View style={{ padding: 16 }}>
        <Text style={{ fontSize: 24, fontWeight: 'bold' }}>
          {workout.name}
        </Text>
        <Text style={{ marginTop: 8, color: '#666' }}>
          {workout.description}
        </Text>

        {/* Other workout details */}

        {workout.type !== 'rest' && (
          <WorkoutActions
            workoutId={workout.id}
            workoutName={workout.name}
            token={token}
            onSuccess={handleMoveSuccess}
          />
        )}
      </View>
    </ScrollView>
  );
};
```

## UX Best Practices

### 1. Confirmation Dialog

Immer eine Bestätigung anfordern, bevor das Workout verschoben wird:

```typescript
Alert.alert(
  'Workout verschieben?',
  'Heute wird ein Ruhetag eingeplant. Das Workout wird auf morgen verschoben.',
  [
    { text: 'Abbrechen', style: 'cancel' },
    { text: 'Verschieben', onPress: handleMove },
  ]
);
```

### 2. Loading State

Zeige einen Loading-Indicator während der API-Anfrage:

```typescript
{isMoving ? <ActivityIndicator /> : <ButtonContent />}
```

### 3. Success Feedback

Gebe klares Feedback nach erfolgreicher Verschiebung:

```typescript
Alert.alert(
  'Erfolgreich! 🎉',
  `Heute: Ruhetag\nMorgen: ${workoutName}`,
  [{ text: 'OK', onPress: refreshPlan }]
);
```

### 4. Error Handling

Zeige benutzerfreundliche Fehlermeldungen:

```typescript
Alert.alert('Fehler', getWorkoutMoveErrorMessage(error));
```

### 5. Conditional Display

Verstecke den Button für Ruhetage:

```typescript
{workout.type !== 'rest' && <MoveToTomorrowButton />}
```

## Testing

### Jest Tests

```typescript
// __tests__/moveWorkout.test.ts

import { moveWorkoutToTomorrow } from '../services/workoutService';

global.fetch = jest.fn();

describe('moveWorkoutToTomorrow', () => {
  beforeEach(() => {
    (fetch as jest.Mock).mockClear();
  });

  it('should move workout successfully', async () => {
    (fetch as jest.Mock).mockResolvedValue({
      ok: true,
      json: async () => ({
        message: 'Workout moved to tomorrow successfully',
        rest_day: { id: 1, name: 'Rest Day', type: 'rest' },
        moved_workout: { id: 2, name: 'Push Day', type: 'strength' },
      }),
    });

    const result = await moveWorkoutToTomorrow(123, 'token');

    expect(fetch).toHaveBeenCalledWith(
      expect.stringContaining('/workouts/123/move'),
      expect.objectContaining({
        method: 'POST',
        headers: expect.objectContaining({
          Authorization: 'Bearer token',
        }),
      })
    );

    expect(result.rest_day.type).toBe('rest');
    expect(result.moved_workout.name).toBe('Push Day');
  });

  it('should throw error on conflict', async () => {
    (fetch as jest.Mock).mockResolvedValue({
      ok: false,
      status: 409,
      json: async () => ({
        error: 'Conflict',
        message: 'Tomorrow already has a workout scheduled',
      }),
    });

    await expect(moveWorkoutToTomorrow(123, 'token')).rejects.toThrow(
      'Tomorrow already has a workout scheduled'
    );
  });
});
```

## Performance Considerations

1. **Optimistic Updates**: Optional UI sofort updaten, vor API-Response
2. **Cache Invalidation**: Plan-Cache nach erfolgreicher Verschiebung invalidieren
3. **Debouncing**: Mehrfache Klicks verhindern mit Debouncing
4. **Offline Support**: Queue-System für Offline-Unterstützung


