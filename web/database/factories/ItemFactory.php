<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    protected static array $counters = [
        'Hand Tools' => 0,
        'Power Tools' => 0,
        'Construction Materials' => 0,
        'Locks and Security' => 0,
        'Plumbing' => 0,
        'Electrical' => 0,
        'Paint and Finishes' => 0,
        'Chemicals' => 0,
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Hand Tools',
            'Power Tools',
            'Construction Materials',
            'Locks and Security',
            'Plumbing',
            'Electrical',
            'Paint and Finishes',
            'Chemicals',
        ];

        $category = fake()->randomElement($categories);

        return [
            'supplier_id' => null,
            'item_code' => $this->generateItemCode($category),
            'brand_name' => fake()->randomElement([
                'Stanley',
                'DeWalt',
                'Makita',
                'Bosch',
                'Black & Decker',
                'Holcim',
                'Union Galvasteel',
                'Yale',
                'Pipelife',
                'American Standard',
                'Omni',
                'Panasonic',
                'Boysen',
                'Davies',
                'Sika',
                'WD-40'
            ]),
            'item_name' => fake()->words(3, true) . ' ' . fake()->randomElement(['Pro', 'Premium', 'Standard', 'Heavy Duty', 'Deluxe']),
            'description' => fake()->sentence(12),
            'category' => $category,
            'item_image_1' => null,
            'item_image_2' => null,
            'item_image_3' => null,
            'unit_price' => fake()->randomFloat(2, 50, 5000),
            'quantity' => fake()->numberBetween(0, 200),
            'restock_threshold' => fake()->numberBetween(5, 20),
        ];
    }

    /**
     * Generate item code based on category
     *
     * @param string $category
     * @return string
     */
    protected function generateItemCode(string $category): string
    {
        $prefix = match ($category) {
            'Hand Tools' => 'HT',
            'Power Tools' => 'PT',
            'Construction Materials' => 'CM',
            'Locks and Security' => 'LS',
            'Plumbing' => 'PL',
            'Electrical' => 'EL',
            'Paint and Finishes' => 'PF',
            'Chemicals' => 'CH',
            default => 'OT',
        };

        self::$counters[$category]++;

        return $prefix . str_pad(self::$counters[$category], 3, '0', STR_PAD_LEFT);
    }


    /**
     * Indicate item is out of stock
     *
     * @return static
     */
    public function outOfStock(): static
    {
        return $this->state(fn(array $attributes) => [
            'quantity' => 0,
        ]);
    }

    /**
     * Indicate item is low stock
     *
     * @return static
     */
    public function lowStock(): static
    {
        return $this->state(fn(array $attributes) => [
            'quantity' => fake()->numberBetween(1, $attributes['restock_threshold']),
        ]);
    }

    /**
     * Indicate item is in stock
     *
     * @return static
     */
    public function inStock(): static
    {
        return $this->state(fn(array $attributes) => [
            'quantity' => fake()->numberBetween($attributes['restock_threshold'] + 1, 200),
        ]);
    }
}
