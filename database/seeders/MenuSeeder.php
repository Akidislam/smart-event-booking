<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Chinese',
                'icon' => 'fas fa-bowl-rice',
                'description' => 'Authentic Chinese cuisine featuring stir-fries, noodles, and dim sum.',
                'items' => [
                    ['name' => 'Fried Rice', 'price_per_plate' => 350, 'description' => 'Classic Chinese fried rice with vegetables and egg'],
                    ['name' => 'Chicken Chowmein', 'price_per_plate' => 400, 'description' => 'Stir-fried noodles with tender chicken and vegetables'],
                    ['name' => 'Sweet & Sour Chicken', 'price_per_plate' => 500, 'description' => 'Crispy chicken in tangy sweet and sour sauce'],
                    ['name' => 'Prawn Crackers', 'price_per_plate' => 250, 'description' => 'Light and crispy prawn flavored crackers'],
                    ['name' => 'Chicken Manchurian', 'price_per_plate' => 550, 'description' => 'Indo-Chinese style chicken in spicy gravy'],
                    ['name' => 'Vegetable Spring Roll', 'price_per_plate' => 200, 'description' => 'Crispy rolls stuffed with mixed vegetables'],
                ],
            ],
            [
                'name' => 'Deshi',
                'icon' => 'fas fa-pepper-hot',
                'description' => 'Traditional Bangladeshi dishes rich in spices and flavor.',
                'items' => [
                    ['name' => 'Chicken Curry', 'price_per_plate' => 450, 'description' => 'Authentic Bangladeshi chicken curry with aromatic spices'],
                    ['name' => 'Mutton Curry', 'price_per_plate' => 700, 'description' => 'Slow-cooked mutton in rich spiced gravy'],
                    ['name' => 'Biryani (Chicken)', 'price_per_plate' => 500, 'description' => 'Fragrant basmati rice layered with spiced chicken'],
                    ['name' => 'Biryani (Mutton)', 'price_per_plate' => 750, 'description' => 'Premium biryani with tender mutton pieces'],
                    ['name' => 'Fish Curry', 'price_per_plate' => 400, 'description' => 'Traditional fish curry in mustard and spice sauce'],
                    ['name' => 'Dal & Rice Combo', 'price_per_plate' => 250, 'description' => 'Classic lentil soup with steamed rice'],
                ],
            ],
            [
                'name' => 'Continental',
                'icon' => 'fas fa-wine-glass',
                'description' => 'European-inspired dishes with elegant presentation.',
                'items' => [
                    ['name' => 'Grilled Chicken Steak', 'price_per_plate' => 650, 'description' => 'Juicy grilled chicken with herb butter sauce'],
                    ['name' => 'Pasta Alfredo', 'price_per_plate' => 450, 'description' => 'Creamy fettuccine alfredo with parmesan'],
                    ['name' => 'Caesar Salad', 'price_per_plate' => 350, 'description' => 'Fresh romaine lettuce with caesar dressing and croutons'],
                    ['name' => 'Fish & Chips', 'price_per_plate' => 500, 'description' => 'Battered fish fillet with golden fries'],
                    ['name' => 'Mushroom Soup', 'price_per_plate' => 300, 'description' => 'Creamy mushroom soup with herbs and croutons'],
                ],
            ],
            [
                'name' => 'BBQ & Grill',
                'icon' => 'fas fa-fire-burner',
                'description' => 'Smoky and chargrilled specialties fresh from the grill.',
                'items' => [
                    ['name' => 'BBQ Chicken', 'price_per_plate' => 550, 'description' => 'Smoky chargrilled chicken with BBQ glaze'],
                    ['name' => 'Seekh Kebab', 'price_per_plate' => 400, 'description' => 'Spiced minced meat skewers grilled to perfection'],
                    ['name' => 'Chicken Tikka', 'price_per_plate' => 500, 'description' => 'Marinated chicken pieces grilled in tandoor'],
                    ['name' => 'Fish Tikka', 'price_per_plate' => 600, 'description' => 'Spiced fish fillets grilled with lemon and herbs'],
                ],
            ],
            [
                'name' => 'Desserts',
                'icon' => 'fas fa-ice-cream',
                'description' => 'Sweet treats and traditional desserts to end your meal.',
                'items' => [
                    ['name' => 'Firni', 'price_per_plate' => 150, 'description' => 'Traditional rice pudding with cardamom and pistachios'],
                    ['name' => 'Chocolate Brownie', 'price_per_plate' => 200, 'description' => 'Rich chocolate brownie with vanilla ice cream'],
                    ['name' => 'Rasmalai', 'price_per_plate' => 180, 'description' => 'Soft cottage cheese dumplings in sweet milk'],
                    ['name' => 'Fruit Custard', 'price_per_plate' => 160, 'description' => 'Mixed seasonal fruits in creamy vanilla custard'],
                ],
            ],
            [
                'name' => 'Beverages',
                'icon' => 'fas fa-mug-hot',
                'description' => 'Refreshing drinks and traditional beverages.',
                'items' => [
                    ['name' => 'Mango Lassi', 'price_per_plate' => 120, 'description' => 'Creamy mango yogurt drink'],
                    ['name' => 'Fresh Lime Soda', 'price_per_plate' => 80, 'description' => 'Refreshing lime soda with mint'],
                    ['name' => 'Borhani', 'price_per_plate' => 100, 'description' => 'Traditional spiced yogurt drink'],
                    ['name' => 'Tea / Coffee', 'price_per_plate' => 60, 'description' => 'Hot tea or coffee per person'],
                ],
            ],
        ];

        foreach ($categories as $catData) {
            $items = $catData['items'];
            unset($catData['items']);
            $category = MenuCategory::create($catData);

            foreach ($items as $item) {
                $item['category_id'] = $category->id;
                MenuItem::create($item);
            }
        }
    }
}
