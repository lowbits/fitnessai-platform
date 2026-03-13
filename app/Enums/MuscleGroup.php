<?php

namespace App\Enums;

enum MuscleGroup: string
{
    case Chest = 'chest';
    case Back = 'back';
    case UpperBack = 'upper_back';
    case LowerBack = 'lower_back';
    case Shoulders = 'shoulders';
    case RearDelts = 'rear_delts';
    case Biceps = 'biceps';
    case Triceps = 'triceps';
    case Forearms = 'forearms';
    case Core = 'core';
    case Glutes = 'glutes';
    case Quadriceps = 'quadriceps';
    case Hamstrings = 'hamstrings';
    case Calves = 'calves';
    case HipFlexors = 'hip_flexors';
    case Traps = 'traps';
    case Rhomboids = 'rhomboids';
    case RotatorCuff = 'rotator_cuff';
    case Legs = 'legs';
    case FullBody = 'full_body';
    case Cardio = 'cardio';

    public function label(): string
    {
        return __('enums.muscleGroup.'.$this->value);
    }

    /**
     * Map a raw (potentially non-English or inconsistent) muscle name to a canonical MuscleGroup.
     */
    public static function fromRaw(string $raw): ?self
    {
        $normalized = mb_strtolower(trim($raw));

        return self::rawMap()[$normalized] ?? null;
    }

    /**
     * @return array<string, self>
     */
    private static function rawMap(): array
    {
        /** @var array<string, self>|null $cache */
        static $cache = null;

        if ($cache !== null) {
            return $cache;
        }

        $variants = [
            ['chest', 'brust', 'upper chest', 'obere brust', 'oberer brust',
                'oberer brustbereich', 'oberere brust', 'oberer brustkorb',
                'untere brust', 'lower chest'],
            ['back', 'rücken', 'lats', 'latissimus', 'mid back', 'mid-back',
                'mittlerer rücken', 'rückenmitte', 'rückenbreite', 'rückenoberer',
                'rückenstrecker'],
            ['upper back', 'oberer rücken', 'obere rücken'],
            ['lower back', 'unterer rücken'],
            ['shoulders', 'schultern', 'deltoids', 'front delts', 'front shoulders',
                'lateral delts', 'seitliche schulter', 'schulterblattmuskulatur',
                'vordere schulter', 'vordere schultern', 'vordere deltamuskulatur',
                'vorderer deltamuskel', 'anterior deltoid', 'anterior delts'],
            ['rear delts', 'rear deltoids', 'posterior delts', 'hintere schulter',
                'hintere schultern', 'hintere deltamuskeln', 'posteriora schulter',
                'posteriores schultergürtel', 'schultern (hintere)'],
            ['biceps', 'bizeps', 'brachialis'],
            ['triceps', 'trizeps'],
            ['forearms', 'unterarm', 'unterarme', 'grip', 'griff', 'griffkraft'],
            ['core', 'rumpf', 'kern', 'bauch', 'bauchmuskeln', 'obliques',
                'schräge bauchmuskeln', 'schräger bauch', 'schrägmuskulatur',
                'seitliche bauchmuskeln', 'seitliche bauchmuskulatur',
                'seitliche rumpfmuskulatur', 'seitliche rumpf', 'upper core'],
            ['glutes', 'gluteus', 'glutaei', 'glutei', 'gluteus medius', 'gesäß', 'po'],
            ['quadriceps', 'quads', 'quadrizeps'],
            ['hamstrings', 'beinbeuger', 'hintere oberschenkel', 'hintere kette',
                'posterior chain'],
            ['calves', 'waden'],
            ['hip flexors', 'hüftbeuger', 'hüfte', 'hüften', 'hüftabduktoren',
                'adduktoren'],
            ['traps', 'trapez', 'nacken'],
            ['rhomboids', 'rhomboide', 'rhomboiden', 'rhomboideen', 'rhomboidei',
                'rhomboideus', 'rauten'],
            ['rotator cuff', 'external rotators'],
            ['legs', 'beine', 'beinmuskulatur', 'lower body'],
            ['full body', 'ganzkörper'],
            ['cardio', 'cardiovascular', 'kardiovaskulär', 'kardio',
                'kardiorespiratorisch', 'herz-kreislauf', 'kondition', 'aerobic system'],
        ];

        $enums = self::cases();
        $cache = [];

        foreach ($variants as $index => $names) {
            $enum = $enums[$index];

            foreach ($names as $name) {
                $cache[$name] = $enum;
            }
        }

        return $cache;
    }
}
