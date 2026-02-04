import {
    Dumbbell,
    Fish,
    HouseHeart,
    Leaf,
    Mars,
    Salad,
    Sprout,
    UtensilsCrossed,
    Venus,
    NonBinary,
} from 'lucide-vue-next';

export const iconRegistry = {
    mars: Mars,
    venus: Venus,
    nonBinary: NonBinary,
    dumbbell: Dumbbell,
    houseHeart: HouseHeart,
    leaf: Leaf,
    sprout: Sprout,
    utensilsCrossed: UtensilsCrossed,
    salad: Salad,
    fish: Fish,
} as const;

export type IconName = keyof typeof iconRegistry;
