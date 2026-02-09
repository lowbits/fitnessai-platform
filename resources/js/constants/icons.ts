import {
    Dumbbell,
    Fish,
    HouseHeart,
    Leaf,
    Mars,
    NonBinary,
    Salad,
    Sprout,
    UtensilsCrossed,
    Venus,
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
