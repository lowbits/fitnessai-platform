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
     * Parse a bundle muscle string like "Chest (Pectoralis major), Shoulders (Deltoids), Triceps (Triceps brachii)"
     * into an array of canonical MuscleGroup values.
     *
     * @return string[]
     */
    public static function parseBundleMuscles(string $raw): array
    {
        if (empty(trim($raw)) || mb_strtolower(trim($raw)) === 'none') {
            return [];
        }

        // Split on "), " or just ", " when no parentheses
        // First, normalize: replace "), " with ")|||" as delimiter
        $normalized = preg_replace('/\)\s*,\s*/', ')|||', $raw);
        $parts = explode('|||', $normalized);

        $muscles = [];
        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) {
                continue;
            }

            // Extract the name before parenthesis: "Chest (Pectoralis major)" -> "Chest"
            $name = preg_replace('/\s*\(.*$/', '', $part);
            $name = trim($name);

            if (empty($name)) {
                continue;
            }

            $mapped = self::fromRaw($name);
            if ($mapped && ! in_array($mapped->value, $muscles)) {
                $muscles[] = $mapped->value;
            }
        }

        return $muscles;
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
                'rückenstrecker', 'middle back', 'latissimus dorsi', 'teres major'],
            ['upper back', 'oberer rücken', 'obere rücken'],
            ['lower back', 'unterer rücken', 'erector spinae'],
            ['shoulders', 'schultern', 'deltoids', 'front delts', 'front shoulders',
                'lateral delts', 'seitliche schulter', 'schulterblattmuskulatur',
                'vordere schulter', 'vordere schultern', 'vordere deltamuskulatur',
                'vorderer deltamuskel', 'anterior deltoid', 'anterior delts'],
            ['rear delts', 'rear deltoids', 'posterior delts', 'hintere schulter',
                'hintere schultern', 'hintere deltamuskeln', 'posteriora schulter',
                'posteriores schultergürtel', 'schultern (hintere)', 'rear shoulders'],
            ['biceps', 'bizeps', 'brachialis'],
            ['triceps', 'trizeps'],
            ['forearms', 'unterarm', 'unterarme', 'grip', 'griff', 'griffkraft',
                'forearm muscles', 'brachioradialis', 'wrist flexors', 'wrist extensors'],
            ['core', 'rumpf', 'kern', 'bauch', 'bauchmuskeln', 'obliques',
                'schräge bauchmuskeln', 'schräger bauch', 'schrägmuskulatur',
                'seitliche bauchmuskeln', 'seitliche bauchmuskulatur',
                'seitliche rumpfmuskulatur', 'seitliche rumpf', 'upper core',
                'abdominals', 'side abdominals', 'core muscles', 'rectus abdominis',
                'transverse abdominis', 'lower abs'],
            ['glutes', 'gluteus', 'glutaei', 'glutei', 'gluteus medius', 'gesäß', 'po',
                'buttocks', 'gluteus maximus', 'gluteus minimus'],
            ['quadriceps', 'quads', 'quadrizeps'],
            ['hamstrings', 'beinbeuger', 'hintere oberschenkel', 'hintere kette',
                'posterior chain'],
            ['calves', 'waden'],
            ['hip flexors', 'hüftbeuger', 'hüfte', 'hüften', 'hüftabduktoren',
                'adduktoren', 'hip abductors', 'adductors', 'inner thighs',
                'inner thigh', 'outer thigh', 'outer thighs', 'hips', 'iliopsoas'],
            ['traps', 'trapez', 'nacken', 'trapezius'],
            ['rhomboids', 'rhomboide', 'rhomboiden', 'rhomboideen', 'rhomboidei',
                'rhomboideus', 'rauten'],
            ['rotator cuff', 'external rotators'],
            ['legs', 'beine', 'beinmuskulatur', 'lower body', 'thighs'],
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
