<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class InvoiceCreatesOrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function creating_an_invoice_also_creates_an_order()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'invoice_no' => 'INV-TEST-001',
            'invoice_date' => now()->toDateString(),
            'company_name' => 'PT Contoh',
            'company_address' => 'Jl. Contoh No.1',
            'client_name' => 'Budi',
            'payment_method' => 'Bank Transfer',
            'nama_layanan' => 'Website',
            'status_pembayaran' => 'pembayaran awal',
            'description' => 'Test invoice',
            'subtotal' => 100000,
            'tax' => 10000,
            'total' => 110000,
        ];

        $response = $this->postJson('/api/invoices', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('invoices', ['invoice_no' => 'INV-TEST-001']);
        $this->assertDatabaseHas('orders', ['invoice_no' => 'INV-TEST-001']);
    }
}
