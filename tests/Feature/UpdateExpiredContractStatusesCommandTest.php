<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UpdateExpiredContractStatusesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_contract_status_is_updated_for_users_and_karyawan(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'karyawan',
            'status_kerja' => 'aktif',
            'status_karyawan' => 'kontrak',
            'kontrak_mulai' => now()->subMonth()->toDateString(),
            'kontrak_selesai' => now()->subDay()->toDateString(),
        ]);

        $this->artisan('contracts:update-status')->assertSuccessful();

        $user->refresh();
        $this->assertSame('tidak_aktif', $user->status_kerja);

        $karyawan = $user->karyawan()->first();
        $this->assertNotNull($karyawan);
        $karyawan->refresh();
        $this->assertSame('tidak_aktif', $karyawan->status_kerja);
    }
}
