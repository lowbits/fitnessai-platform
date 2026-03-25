<?php

namespace App\Enums;

enum Equipment: string
{
    case Barbell = 'barbell';
    case Dumbbell = 'dumbbell';
    case Kettlebell = 'kettlebell';
    case EzBar = 'ez_bar';
    case TrapBar = 'trap_bar';
    case Cable = 'cable_machine';
    case SmithMachine = 'smith_machine';
    case Bench = 'bench';
    case InclineBench = 'incline_bench';
    case DeclineBench = 'decline_bench';
    case AdjustableBench = 'adjustable_bench';
    case SquatRack = 'squat_rack';
    case PowerRack = 'power_rack';
    case PullUpBar = 'pull_up_bar';
    case DipBars = 'dip_bars';
    case ParallelBars = 'parallel_bars';
    case Rings = 'rings';
    case ResistanceBand = 'resistance_band';
    case TRX = 'trx';
    case Bodyweight = 'bodyweight';
    case Mat = 'mat';
    case FoamRoller = 'foam_roller';
    case StabilityBall = 'stability_ball';
    case MedicineBall = 'medicine_ball';
    case AbWheel = 'ab_wheel';
    case BattleRopes = 'battle_ropes';
    case JumpRope = 'jump_rope';
    case RowingMachine = 'rowing_machine';
    case StationaryBike = 'stationary_bike';
    case Treadmill = 'treadmill';
    case LegPress = 'leg_press';
    case LegCurlMachine = 'leg_curl_machine';
    case CalfRaiseMachine = 'calf_raise_machine';
    case LatPulldownMachine = 'lat_pulldown_machine';
    case SeatedRowMachine = 'seated_row_machine';
    case ChestPressMachine = 'chest_press_machine';
    case ShoulderPressMachine = 'shoulder_press_machine';
    case HyperextensionBench = 'hyperextension_bench';
    case DipBelt = 'dip_belt';
    case WeightBelt = 'weight_belt';
    case WeightVest = 'weight_vest';
    case Plates = 'plates';
    case Box = 'box';
    case Step = 'step';
    case Chair = 'chair';
    case Wall = 'wall';
    case Doorframe = 'doorframe';
    case Towel = 'towel';
    case Backpack = 'backpack';
    case WaterBottle = 'water_bottle';
    case Sled = 'sled';
    case GHD = 'ghd';
    case PreacherCurl = 'preacher_curl_bench';
    case CableCrossover = 'cable_crossover';

    public function label(): string
    {
        return __('enums.equipment.'.$this->value);
    }

    /**
     * Map a raw (potentially non-English, inconsistent, or German) equipment name to a canonical Equipment value.
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

        // Each inner array's first element is the canonical value matching the enum case order.
        // All other elements are aliases (English + German).
        $variants = [
            ['barbell', 'langhantel', 'barbell or dumbbells'],
            ['dumbbell', 'dumbbells', 'kurzhantel', 'kurzhanteln',
                'kurzhantel (optional)', 'kurzhanteln (optional)', 'kurzhanteln optional',
                'kurzhantel optional', 'optional kurzhantel', 'optional kurzhanteln',
                'hantel oder schwere flasche', 'kurzhanteln oder flaschen',
                'dumbbells or water bottles', 'dumbbell or kettlebell',
                'dumbbell or heavy bag', 'light weight', 'keine/optional kurzhantel',
                'optional leichte wasserflaschen', 'dumbbell or backpack',
                'dumbbell or water bottle', 'dumbbells (optional)', 'dumbbells or bottles',
                'dumbbells or backpack', 'dumbbells optional', 'none or dumbbell',
                'dumbbell optional', 'light dumbbells', 'dumbbell or household weight',
                'household weight', 'household weights', 'gewichte', 'light weights',
                'hantel', 'hanteln (optional)', 'leichte hanteln',
                'dumbbell or kettlebell or heavy object', 'dumbbell/backpack',
                'dumbbells or heavy objects', 'none or dumbbells', 'optional dumbbell',
                'optional dumbbells', 'dumbbell (optional)', 'backpack or dumbbell',
                'backpack or dumbbells', 'water bottles or light dumbbells',
                'light dumbbells or water bottles', 'dumbbell or heavy object',
                'dumbbells or heavy backpack', 'dumbbells or household objects',
                'dumbbells or loaded backpack', 'dumbbells or resistance band',
                'dumbbells or resistance bands', 'light weights or water bottles',
                'optional weight', 'gewicht optional', 'light resistance band (optional)',
                'farmer handles'],
            ['kettlebell', 'kettlebells'],
            ['ez bar', 'ez-bar', 'ez-stange oder kurzhanteln', 'sz-stange'],
            ['trap bar', 't-bar'],
            ['cable machine', 'cable rope', 'cable row', 'cable row machine',
                'kabelmaschine', 'kabelzug', 'kabelzugmaschine', 'kabelzugstation',
                'kabelzug mit seil', 'rope', 'rope attachment', 'straight bar',
                'v-bar', 'gerader griff', 'seil', 'seil (optional)', 'seilgriff',
                'gerade stange oder seil', 'stange oder seil', 'rudergriff',
                'seated cable row machine', 'seated row handle', 'doorframe or cable',
                'kabelturm', 'cable', 'cable row machine', 'straight bar or rope',
                'anchor', 'anchor point', 'cable pulley machine', 'dual cable pulley machine',
                'crossover machine', 'resistance cable'],
            ['smith machine', 'smith maschine'],
            ['bench', 'bank', 'flat bench', 'flachbank', 'seated bench',
                'sitzbank', 'parkbank', 'parkbank (optional)', 'parkbank optional',
                'bank oder stuhl', 'stuhl/bank', 'kiste/bank', 'sofa/bank',
                'bank oder erhöhte kante', 'bank, rucksack', 'parkbank, rucksack',
                'bench or low wall', 'bench or pole', 'bench or chair',
                'chair or bench', 'hantelbank', 'flache bank',
                'bench (optional)', 'park bench', 'bench with back support',
                'bank mit rückenlehne', 'bank oder boden', 'flache hantelbank',
                'hantelablage'],
            ['incline bench', 'incline surface', 'schrägbank',
                'schrägbank oder stabile erhöhung', 'schräge bank'],
            ['decline bench'],
            ['adjustable bench', 'einstellbare bank',
                'verstellbare bank', 'bank mit lehne'],
            ['squat rack', 'rack', 'doorframe or rack', 'doorway or rack',
                'safety pins/rack', 'gewichtsständer', 'squatrack'],
            ['power rack'],
            ['pull-up bar', 'bar', 'klimmzugstange', 'klimmstange',
                'stange', 'stange oder baum', 'stange oder geländer',
                'stange oder pfosten', 'stange/geländer', 'niedrige stange',
                'stabile stange', 'reck/stange', 'stange oder stabiler tisch',
                'niedrige stange, bank oder trx', 'table or bar',
                'klimmzugstange oder türrahmen-tabelle', 'pfosten',
                'stange oder partner', 'laterne/geländer', 'geländer',
                'assisted pull-up machine', 'pull-up bar or doorway bar',
                'pull-up bar or sturdy door frame', 'sturdy table or low bar',
                'broomstick', 'pull up bar', 'assisted pull up machine',
                'dip pull up station', 'fixed pole bar'],
            ['dip bars', 'dip-barren oder stabile parallettes',
                'dip-stangen', 'dip-station', 'dip maschine',
                'assisted dip machine', 'dip machine', 'dip station',
                'dip stand', 'seated dip machine', 'triceps dips machine'],
            ['parallel bars', 'parallelbarren',
                'parallelstangen oder dip-station im park'],
            ['rings', 'ringe', 'turnringe oder trx'],
            ['resistance band', 'resistance_band', 'band',
                'widerstandsband', 'widerstandsband (optional)', 'widerstandsband optional',
                'optional widerstandsband', 'widerstandsband, baum oder poller',
                'towel or resistance band', 'handtuch oder widerstandsband optional',
                'stab oder widerstandsband (optional)', 'resistance band (optional)',
                'resistance band or towel', 'resistance band optional', 'theraband',
                'gummiband', 'none or resistance band', 'loop resistance band'],
            ['trx', 'trx-band (optional)', 'suspension trainer'],
            ['bodyweight', 'körpergewicht', 'none', 'none (bodyweight)', 'kein', 'keine',
                'keines', 'keins', 'kein equipment', 'boden', 'floor',
                'freier raum', 'freier platz, optionaler rucksack',
                'kein equipment (optional flasche)', 'kein equipment (optional rucksack)',
                'keine (optional gewicht)', 'freie fläche', 'freier platz',
                'eigengewicht', 'ground', 'open space'],
            ['mat', 'floor mat', 'matte', 'matte (optional)', 'matte optional',
                'bodenmatte', 'trainingsmatte', 'yogamatte optional', 'unterlage',
                'weiche unterlage', 'polster', 'wand, matte', 'parkboden',
                'mat (optional)', 'yogamatte', 'exercise mat', 'gym mat',
                'yoga mat', 'yoga mat (optional)', 'yogamatte (optional)',
                'übungsmatte', 'gym-matte'],
            ['foam roller'],
            ['stability ball', 'exercise ball'],
            ['medicine ball', 'medizinball'],
            ['ab wheel', 'ab-roller', 'ab roller'],
            ['battle ropes'],
            ['jump rope', 'springseil', 'springseil (optional)',
                'springseil optional', 'optional springseil'],
            ['rowing machine', 'rudergerät', 'rower', 'rudermaschine',
                'ruderergometer', 'ruderzugmaschine', 'rudergerät maschine'],
            ['stationary bike', 'ergometer', 'fahrradergometer',
                'fahrrad-ergometer', 'fahrrad', 'bike', 'indoor bike',
                'spinning bike', 'heimtrainer', 'hometrainer',
                'stationary exercise bike', 'airbike', 'air bike', 'elliptical machine'],
            ['treadmill', 'laufband'],
            ['leg press', 'leg press machine', 'beinpresse'],
            ['leg curl machine', 'seated leg curl machine',
                'beinbeuger maschine', 'beincurl maschine', 'beincurl-maschine',
                'beinbeugermaschine', 'beinbeuger-maschine', 'leg curl maschine',
                'lying leg curl machine', 'hammer strength iso-lateral leg curl machine',
                'mts iso-lateral kneeling leg curl machine', 'kneeling leg curl machine',
                'lever leg extension machine', 'leg extension machine'],
            ['calf raise machine', 'seated calf raise',
                'seated calf raise machine', 'standing calf raise machine',
                'wadenmaschine', 'stehende wadenmaschine', 'wadenmaschine oder step'],
            ['lat pulldown machine', 'lat-maschine',
                'lat-zug-maschine', 'lat pulldown-maschine', 'latziehen maschine',
                'lat pull down machine (cable)', 'lat pull down machine'],
            ['seated row machine', 'hammer strength plate loaded high row machine',
                'cable row machine'],
            ['chest press machine', 'chest press maschine', 'brustpresse maschine',
                'pec deck machine', 'pec deck maschine',
                'hammer strength plate-loaded iso-lateral chest press machine',
                'hammer strength mts iso-lateral decline press machine',
                'dual pec fly machine', 'dual pec deck machine',
                'pec fly/rear delt machine'],
            ['shoulder press machine', 'schulterpresse maschine',
                'iso-lateral shoulder press machine'],
            ['hyperextension bench', 'hyperextension bank', 'hyperextensionbank'],
            ['dip belt', 'dip-gürtel', 'dip-gürtel optional',
                'dipgürtel (optional)'],
            ['weight belt', 'gewichtsgürtel (optional)', 'weight belt (optional)',
                'gewichtsgürtel'],
            ['weight vest', 'gewichtsweste',
                'gewichtsweste oder rucksack', 'gewichtsweste optional',
                'optional gewichtsweste', 'weighted vest (optional)',
                'weighted vest', 'optional weighted backpack'],
            ['plates', 'gewichtsscheiben', 'gewicht',
                'gewicht (flaschen, bücher)', 'weight plates', 'weight plate',
                'plate', 'gewichtsscheibe', 'hantelscheiben', 'gewichtplatten'],
            ['box', 'kasten', 'kasten oder stein',
                'elevated platform', 'elevated surface', 'elevated surface optional',
                'stable elevated surface', 'sturdy elevated surface',
                'erhöhte fläche', 'stabile erhöhung', 'stabile erhöhte fläche',
                'erhöhung (couchkante, tisch)', 'erhöhung (z. b. stufe, sofa)',
                'parkbank oder erhöhte oberfläche optional',
                'parkbank oder stabile erhöhung'],
            ['step', 'stufe', 'treppe', 'treppenstufe', 'stufe optional',
                'stufe oder buch', 'stufe oder dickes buch', 'stufe/kante',
                'stabile stufe', 'sofa/stufe', 'kiste/stufe', 'bordstein',
                'bordsteinkante', 'kante', 'parkbank oder stufe',
                'stufe, rucksack', 'gelände mit steigung'],
            ['chair', 'stuhl', 'stuhl (optional)', 'stühle',
                'stühle oder couch', 'sturdy chair', 'seat with back support',
                'sitz', 'stuhl/couch', 'stuhl/wand', 'stuhl/bettkante',
                'stuhl oder stabile kante', 'stuhl/bank (für balance)',
                'couch', 'couch oder boden', 'couch oder stuhl', 'sofa',
                '2 stabile stühle', 'stabiler tisch', 'sturdy table', 'tisch',
                'bank oder geländer zur unterstützung', 'chair (optional)',
                'chair/bench', 'chair or couch', 'chair or low couch',
                'stabile kante', 'stabler tisch', 'stabile tischkante',
                'pad'],
            ['wall', 'wand', 'wand (optional)', 'mauer', 'parkmauer',
                'freie wand', 'wand oder erhöhter untergrund',
                'bank oder wand', 'bank oder wand optional', 'doorway or wall',
                'wall or doorway'],
            ['doorframe', 'doorway', 'türrahmen',
                'türrahmen optional', 'türanker', 'door anchor', 'door anchor (optional)',
                'door frame', 'door', 'türzarge'],
            ['towel', 'handtuch', 'handtuch (optional)', 'towel (optional)',
                'towel or band', 'towel or resistance band (optional)'],
            ['backpack', 'backpack optional', 'rucksack',
                'rucksack (optional)', 'rucksack optional', 'rucksack(s)',
                'rucksack oder gegenstände', 'optional rucksack',
                'parkbank oder rucksack optional', 'kurzhantel oder rucksack',
                'bank, geländer, rucksack', 'wasserkanister oder rucksack',
                'backpack (optional)', 'optional: rucksack', 'optional rucksack',
                'none or backpack', 'weighted backpack', 'rucksack mit gewicht',
                'optional backpack', 'optional: backpack',
                'wasserflasche oder rucksack'],
            ['water bottle', 'water bottles', 'wasserflasche',
                'wasserflaschen', 'wasserflaschen oder kleine hanteln',
                'kurzhanteln oder wasserflaschen', 'kurzhantel oder wasserflasche',
                'kurzhantel oder flasche', 'flaschen (optional)', 'cans',
                'evtl. wasserflasche', 'optional: wasserflaschen',
                'water jug', 'water jugs', 'wasserkanister',
                'gefüllte flasche', 'bottles'],
            ['sled', 'schlitten', 'prowler'],
            ['ghd', 'glute ham developer', 'ghd machine'],
            ['preacher curl bench', 'preacher bench', 'scott bench', 'scott-bank'],
            ['cable crossover', 'cable crossover machine', 'kabelcrossover'],
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
