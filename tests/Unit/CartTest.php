<?php

namespace Tests\Unit;

use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class CartTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Session::flush();
        Session::put('cart', []);
    }

    public function test_item_count_returns_zero_for_empty_cart(): void
    {
        $this->assertSame(0, CartController::itemCount());
    }

    public function test_item_count_sums_all_quantities(): void
    {
        Session::put('cart', [
            1 => ['quantity' => 2, 'size' => '40'],
            2 => ['quantity' => 3, 'size' => null],
            3 => ['quantity' => 1, 'size' => '42'],
        ]);

        $this->assertSame(6, CartController::itemCount());
    }

    public function test_item_count_ignores_cart_without_quantity_key(): void
    {
        Session::put('cart', [
            1 => ['quantity' => 2],
            2 => ['foo' => 'bar'],
        ]);

        $this->assertSame(2, CartController::itemCount());
    }

    public function test_cart_controller_is_instantiable(): void
    {
        $this->assertInstanceOf(CartController::class, new CartController());
    }
}
