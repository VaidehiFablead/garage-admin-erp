<?php

namespace Tests\Unit;

use App\Http\Controllers\admin\PurchaseAiBillController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PurchaseAiBillControllerTest extends TestCase
{
    public function test_purchase_ai_bill_index_renders_without_sql_errors(): void
    {
        $user = User::first();
        if ($user) {
            Auth::login($user);
        }

        $controller = new PurchaseAiBillController();
        $view = $controller->index();

        $this->assertEquals('purchase.ai-bill', $view->name());
        $this->assertArrayHasKey('vendors', $view->getData());
        $this->assertArrayHasKey('categories', $view->getData());
        $this->assertArrayHasKey('products', $view->getData());
    }
}
