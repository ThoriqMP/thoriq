<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\KpiGrade;
use App\Models\OmsetLog;
use App\Models\PayrollDistribution;
use App\Models\CashTransaction;
use App\Models\Event;
use App\Models\EventExpense;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TreasuryPayrollHubTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed default roles
        $this->roleTreasury = Role::create(['name' => 'Treasury']);
        $this->roleHead = Role::create(['name' => 'Head']);
        $this->roleSales = Role::create(['name' => 'Sales']);
        $this->roleDeveloper = Role::create(['name' => 'Developer']);

        // Seed default kpi_grades
        KpiGrade::create(['grade_name' => 'A', 'weight_percentage' => 14.00]);
        KpiGrade::create(['grade_name' => 'B', 'weight_percentage' => 9.00]);
        KpiGrade::create(['grade_name' => 'C', 'weight_percentage' => 4.50]);

        // Seed 7 standard users
        $this->treasury = User::create([
            'name' => 'Treasury User',
            'username' => 'treasury',
            'email' => 'treasury@test.com',
            'role_id' => $this->roleTreasury->id,
            'password' => bcrypt('password'),
        ]);

        $this->head = User::create([
            'name' => 'Head User',
            'username' => 'head',
            'email' => 'head@test.com',
            'role_id' => $this->roleHead->id,
            'password' => bcrypt('password'),
        ]);

        $this->sales = User::create([
            'name' => 'Sales User',
            'username' => 'sales',
            'email' => 'sales@test.com',
            'role_id' => $this->roleSales->id,
            'password' => bcrypt('password'),
        ]);

        // 4 Devs to total 7 users
        for ($i = 1; $i <= 4; $i++) {
            User::create([
                'name' => "Dev User $i",
                'username' => "dev$i",
                'email' => "dev$i@test.com",
                'role_id' => $this->roleDeveloper->id,
                'password' => bcrypt('password'),
            ]);
        }
    }

    public function test_treasury_can_access_all_hub_routes()
    {
        $this->actingAs($this->treasury);

        $this->get(route('treasury.dashboard'))->assertOk();
        $this->get(route('treasury.omset'))->assertOk();
        $this->get(route('treasury.payroll'))->assertOk();
        $this->get(route('treasury.events'))->assertOk();
        $this->get(route('treasury.cashbook'))->assertOk();
    }

    public function test_sales_cannot_access_restricted_hub_routes()
    {
        $this->actingAs($this->sales);

        $this->get(route('treasury.dashboard'))->assertForbidden();
        $this->get(route('treasury.omset'))->assertOk(); // Sales is allowed to input omset
        $this->get(route('treasury.payroll'))->assertForbidden();
        $this->get(route('treasury.events'))->assertOk(); // Allowed to view events budget
        $this->get(route('treasury.cashbook'))->assertForbidden();
    }

    public function test_omset_math_formulas_and_payroll_auto_generation()
    {
        $this->actingAs($this->treasury);

        $response = $this->post(route('treasury.omset.store'), [
            'tanggal' => '2026-07-06',
            'nominal_omset' => 100000000, // Rp 100.000.000 (A)
            'sales_id' => $this->sales->id,
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('omset_logs', [
            'nominal_omset' => 100000000,
            'alokasi_gaji' => 70000000, // B (70% A)
            'alokasi_perusahaan' => 30000000, // C (30% A)
            'gaji_pokok_pool' => 49000000, // D (70% B)
            'tukin_pool' => 21000000, // E (30% B)
            'status' => 'approved',
        ]);

        // Gapok Pool divided equally among 7 users = 49.000.000 / 7 = 7.000.000
        $this->assertDatabaseCount('payroll_distributions', 7);
        $this->assertDatabaseHas('payroll_distributions', [
            'user_id' => $this->treasury->id,
            'nominal_gapok_diterima' => 7000000,
            'nominal_tukin_diterima' => 0,
            'status_pembayaran' => 'pending',
        ]);
    }

    public function test_kpi_tukin_allocation_and_payout()
    {
        $this->actingAs($this->treasury);

        // 1. Create approved Omset log
        $log = OmsetLog::create([
            'tanggal' => '2026-07-06',
            'nominal_omset' => 100000000,
            'alokasi_gaji' => 70000000,
            'alokasi_perusahaan' => 30000000,
            'gaji_pokok_pool' => 49000000,
            'tukin_pool' => 21000000,
            'sales_id' => $this->sales->id,
            'status' => 'approved',
        ]);

        // Generate distributions
        $users = User::all();
        foreach ($users as $user) {
            PayrollDistribution::create([
                'omset_log_id' => $log->id,
                'user_id' => $user->id,
                'kpi_grade_id' => null,
                'nominal_gapok_diterima' => 7000000,
                'nominal_tukin_diterima' => 0,
                'status_pembayaran' => 'pending',
            ]);
        }

        // 2. Set KPI Grade A for Sales User (14% of Tukin Pool = 14% * 21.000.000 = 2.940.000)
        $gradeA = KpiGrade::where('grade_name', 'A')->first();
        $dist = PayrollDistribution::where('user_id', $this->sales->id)->first();

        $this->post(route('treasury.payroll.kpi', $dist->id), [
            'kpi_grade_id' => $gradeA->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('payroll_distributions', [
            'id' => $dist->id,
            'kpi_grade_id' => $gradeA->id,
            'nominal_tukin_diterima' => 2940000,
        ]);

        // 3. Process final payroll payment
        $this->post(route('treasury.payroll.bayar', $log->id))->assertRedirect();

        // Verify status changed to paid
        $this->assertDatabaseHas('payroll_distributions', [
            'id' => $dist->id,
            'status_pembayaran' => 'paid',
        ]);

        // Cash flow ledger debit record
        $this->assertDatabaseHas('cash_transactions', [
            'tipe' => 'out',
            'kategori' => 'payroll',
        ]);
    }

    public function test_events_budgeting_and_detailed_actual_expenses()
    {
        $this->actingAs($this->treasury);

        // 1. Store planning budget first
        $response = $this->post(route('treasury.events.store'), [
            'nama_event' => 'Gala Dinner 2026',
            'tanggal_event' => '2026-08-10',
            'pic_id' => $this->head->id,
            'budget_transportasi' => 5000000,
            'budget_akomodasi' => 8000000,
            'budget_venue' => 12000000,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('events', [
            'nama_event' => 'Gala Dinner 2026',
            'budget_transportasi' => 5000000,
            'total_budget' => 25000000,
        ]);

        $event = Event::first();

        // 2. Store real itemized expense with quantity
        $responseExpense = $this->post(route('treasury.events.expense.store', $event->id), [
            'kategori' => 'transportasi',
            'nama_item' => 'Tiket Citilink PP',
            'quantity' => 7,
            'harga_satuan' => 600000,
            'tanggal_pengeluaran' => '2026-08-09',
            'catatan' => 'Tiket untuk 7 staff',
        ]);

        $responseExpense->assertRedirect();

        $this->assertDatabaseHas('event_expenses', [
            'event_id' => $event->id,
            'kategori' => 'transportasi',
            'nama_item' => 'Tiket Citilink PP',
            'quantity' => 7,
            'harga_satuan' => 600000,
            'total_harga' => 4200000,
        ]);

        // Verifikasi Kas Besar terpotong
        $this->assertDatabaseHas('cash_transactions', [
            'tipe' => 'out',
            'kategori' => 'event',
            'nominal' => 4200000,
        ]);
    }
}
