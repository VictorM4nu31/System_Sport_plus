<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'full_name' => fake()->name(),
            'postal_code' => fake()->postcode(),
            'state' => fake()->state(),
            'municipality' => fake()->city(),
            'neighborhood' => fake()->streetName(),
            'street' => fake()->streetName(),
            'number' => (string) fake()->numberBetween(1, 500),
            'interior_number' => null,
            'contact_phone' => fake()->phoneNumber(),
            'additional_instructions' => null,
            'is_default' => true,
            'address_type' => 'shipping',
        ];
    }
}
