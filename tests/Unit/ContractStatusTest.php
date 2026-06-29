<?php

namespace Tests\Unit;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_contracted_employee_is_marked_inactive_after_contract_end_date()
    {
        $user = User::create([
            'name' => 'Test Karyawan',
            'email' => 'test-karyawan@example.com',
            'password' => bcrypt('password123'),
            'role' => 'karyawan',
            'status_kerja' => 'aktif',
            'status_karyawan' => 'kontrak',
            'kontrak_mulai' => now()->subMonths(3)->toDateString(),
            'kontrak_selesai' => now()->subDay()->toDateString(),
        ]);

        $user->refresh();
        $karyawan = $user->karyawan()->first();

        $this->assertNotNull($karyawan);
        $this->assertSame('tidak_aktif', $user->status_kerja);
        $this->assertSame('tidak_aktif', $karyawan->status_kerja);
    }
}
