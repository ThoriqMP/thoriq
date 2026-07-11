<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\KpiGrade;
use App\Models\OmsetLog;
use App\Models\PayrollDistribution;
use App\Models\Event;
use App\Models\EventExpense;
use App\Models\CashTransaction;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TreasuryController extends Controller
{
    private function checkAccess(array $allowedRoles)
    {
        if (!Auth::check() || !Auth::user()->hasRole($allowedRoles)) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }
    }

    public function dashboard()
    {
        $this->checkAccess(['Treasury', 'Headman']);

        // Cash flow calculations
        $totalIn = CashTransaction::where('tipe', 'in')->sum('nominal');
        $totalOut = CashTransaction::where('tipe', 'out')->sum('nominal');
        $saldoKas = $totalIn - $totalOut;

        // Payroll pool stats
        $totalB = OmsetLog::where('status', 'approved')->sum('alokasi_gaji');
        $totalGapokPool = OmsetLog::where('status', 'approved')->sum('gaji_pokok_pool');
        $totalTukinPool = OmsetLog::where('status', 'approved')->sum('tukin_pool');

        // Paid vs Pending Payroll
        $payrollPending = PayrollDistribution::where('status_pembayaran', 'pending')->sum(
            \DB::raw('nominal_gapok_diterima + nominal_tukin_diterima')
        );
        $payrollPaid = PayrollDistribution::where('status_pembayaran', 'paid')->sum(
            \DB::raw('nominal_gapok_diterima + nominal_tukin_diterima')
        );

        $recentTransactions = CashTransaction::latest()->take(5)->get();

        return view('treasury.dashboard', compact(
            'saldoKas', 'totalIn', 'totalOut',
            'totalB', 'totalGapokPool', 'totalTukinPool',
            'payrollPending', 'payrollPaid', 'recentTransactions'
        ));
    }

    public function inputOmset()
    {
        $this->checkAccess(['Treasury', 'Marketing', 'Headman']);

        $omsetLogs = OmsetLog::with('sales')->latest()->paginate(10);
        $marketingUsers = User::whereHas('role', function($q) {
            $q->where('name', 'Marketing');
        })->get();

        return view('treasury.omset', compact('omsetLogs', 'marketingUsers'));
    }

    public function storeOmset(Request $request)
    {
        $this->checkAccess(['Treasury', 'Marketing', 'Headman']);

        $request->validate([
            'tanggal' => 'required|date',
            'nominal_omset' => 'required|numeric|min:0',
            'tahun' => 'required|integer|min:1|max:3',
            'sales_id' => 'required|exists:users,id',
        ]);

        $omset = $request->input('nominal_omset');
        $tahun = $request->input('tahun');

        // New Formula:
        // B (Anggaran Penggajian) = 60% dari A.
        // C (Kas Perusahaan) = 40% dari A.
        $alokasiGaji = $omset * 0.60;
        $alokasiPerusahaan = $omset * 0.40;

        // Sub-allocations of C (40%) based on Year
        if ($tahun == 1) {
            $alokasiDevelopment = $omset * 0.30;
            $alokasiPartnership = $omset * 0.10;
        } elseif ($tahun == 2) {
            $alokasiDevelopment = $omset * 0.20;
            $alokasiPartnership = $omset * 0.20;
        } else {
            $alokasiDevelopment = $omset * 0.10;
            $alokasiPartnership = $omset * 0.30;
        }

        // Sub-allocations of Partnership (Bagi Hasil)
        $alokasiPenasehat = $alokasiPartnership * 0.50;
        $alokasiSaham = $alokasiPartnership * 0.50;

        // Keep Gapok and Tukin pools for legacy view compatibility (70% and 30% of Gaji)
        $gapokPool = $alokasiGaji * 0.70;
        $tukinPool = $alokasiGaji * 0.30;

        // Auto approve if entered by Treasury, else wait for Treasury/Headman approval
        $status = (Auth::user()->role && Auth::user()->role->name === 'Treasury') ? 'approved' : 'pending';

        \DB::transaction(function () use ($request, $omset, $tahun, $alokasiGaji, $alokasiPerusahaan, $alokasiDevelopment, $alokasiPartnership, $alokasiPenasehat, $alokasiSaham, $gapokPool, $tukinPool, $status) {
            $log = OmsetLog::create([
                'tanggal' => $request->tanggal,
                'nominal_omset' => $omset,
                'tahun' => $tahun,
                'alokasi_gaji' => $alokasiGaji,
                'alokasi_perusahaan' => $alokasiPerusahaan,
                'alokasi_development' => $alokasiDevelopment,
                'alokasi_partnership' => $alokasiPartnership,
                'alokasi_penasehat' => $alokasiPenasehat,
                'alokasi_saham' => $alokasiSaham,
                'gaji_pokok_pool' => $gapokPool,
                'tukin_pool' => $tukinPool,
                'sales_id' => $request->sales_id,
                'status' => $status,
            ]);

            if ($status === 'approved') {
                // Record company cash allocation
                CashTransaction::create([
                    'tipe' => 'in',
                    'kategori' => 'omset',
                    'nominal' => $omset,
                    'deskripsi' => "Omset Masuk - Gaji (Rp " . number_format($alokasiGaji, 0, ',', '.') . "), Dev (Rp " . number_format($alokasiDevelopment, 0, ',', '.') . "), Penasehat (Rp " . number_format($alokasiPenasehat, 0, ',', '.') . "), Saham (Rp " . number_format($alokasiSaham, 0, ',', '.') . ")",
                ]);

                // Auto-generate payroll distributions draft for the users
                $this->generatePayrollDistributions($log);
            }
        });

        // Send notifications to Treasury & Headman if submitted by non-Treasury
        if ($status === 'pending') {
            $submitterName = Auth::user()->name;
            $treasuryAndHeadmen = User::whereHas('role', function ($q) {
                $q->whereIn('name', ['Treasury', 'Headman']);
            })->where('id', '!=', Auth::id())->get();

            NotificationController::send(
                $treasuryAndHeadmen->pluck('id')->toArray(),
                'omset_submitted',
                'Pengajuan Omset Baru',
                "{$submitterName} mengajukan omset baru yang memerlukan persetujuan Anda.",
                route('treasury.omset')
            );
        }

        return redirect()->back()->with('success', 'Data omset berhasil diinput!');
    }

    public function approveOmset($id)
    {
        $this->checkAccess(['Treasury', 'Headman']);

        $log = OmsetLog::findOrFail($id);

        if ($log->status === 'pending') {
            \DB::transaction(function () use ($log) {
                $log->update(['status' => 'approved']);

                // Record company cash allocation
                CashTransaction::create([
                    'tipe' => 'in',
                    'kategori' => 'omset',
                    'nominal' => $log->nominal_omset,
                    'deskripsi' => "Omset Masuk Disetujui - Alokasi Perusahaan (Rp " . number_format($log->alokasi_perusahaan, 0, ',', '.') . ") & Alokasi Gaji (Rp " . number_format($log->alokasi_gaji, 0, ',', '.') . ")",
                ]);

                $this->generatePayrollDistributions($log);
            });
        }

        // Notify the original submitter (sales user)
        if ($log->sales_id) {
            $approverName = Auth::user()->name;
            NotificationController::send(
                $log->sales_id,
                'omset_approved',
                'Pengajuan Omset Disetujui ✅',
                "{$approverName} telah menyetujui pengajuan omset Anda tanggal " . \Carbon\Carbon::parse($log->tanggal)->format('d M Y') . ". Payroll telah dibuat.",
                route('treasury.omset')
            );
        }

        return redirect()->back()->with('success', 'Omset berhasil disetujui dan alokasi dana dibuat!');
    }

    public function destroyOmset($id)
    {
        $this->checkAccess(['Treasury', 'Headman']);

        $log = OmsetLog::findOrFail($id);

        \DB::transaction(function () use ($log) {
            if ($log->status === 'approved') {
                $formattedPerusahaan = number_format($log->alokasi_perusahaan, 0, ',', '.');
                $formattedGaji = number_format($log->alokasi_gaji, 0, ',', '.');

                // Cari transaksi kas masuk yang sesuai untuk dihapus agar kas perusahaan kembali seimbang
                CashTransaction::where('kategori', 'omset')
                    ->where('tipe', 'in')
                    ->where('nominal', $log->nominal_omset)
                    ->where(function($query) use ($formattedPerusahaan, $formattedGaji) {
                        $query->where('deskripsi', 'like', "%Alokasi Perusahaan (Rp {$formattedPerusahaan})%")
                              ->orWhere('deskripsi', 'like', "%Alokasi Gaji (Rp {$formattedGaji})%");
                    })
                    ->first()
                    ?->delete();
            }

            // Hapus log omset
            $log->delete();
        });

        return redirect()->back()->with('success', 'Data omset berhasil dihapus dan alokasi kas dibatalkan!');
    }

    private function generatePayrollDistributions(OmsetLog $log)
    {
        $users = User::with('role')->get();

        foreach ($users as $user) {
            $roleName = $user->role?->name;
            if (!$roleName) continue;

            // Determine user group count (6 groups total)
            if (in_array($roleName, ['Penasehat', 'Front-man Officer'])) {
                $groupCount = User::whereHas('role', function($q) {
                    $q->whereIn('name', ['Penasehat', 'Front-man Officer']);
                })->count() ?: 1;
            } else {
                $groupCount = User::whereHas('role', function($q) use ($roleName) {
                    $q->where('name', $roleName);
                })->count() ?: 1;
            }

            // Gaji Pokok Pool divided equally into 6 shares, then split among group users
            $groupGapokShare = $log->gaji_pokok_pool / 6;
            $gapokPerUser = $groupGapokShare / $groupCount;

            PayrollDistribution::create([
                'omset_log_id' => $log->id,
                'user_id' => $user->id,
                'kpi_grade_id' => null, // Treasury will fill KPI later
                'nominal_gapok_diterima' => $gapokPerUser,
                'nominal_tukin_diterima' => 0,
                'status_pembayaran' => 'pending',
            ]);
        }
    }

    public function evaluasiPayroll(Request $request)
    {
        $this->checkAccess(['Treasury', 'Headman']);

        // Get omset logs that have distributions
        $omsetLogs = OmsetLog::where('status', 'approved')->latest()->get();
        $selectedLogId = $request->input('omset_log_id', $omsetLogs->first()?->id);

        $selectedLog = $selectedLogId ? OmsetLog::find($selectedLogId) : null;
        $distributions = $selectedLog ? PayrollDistribution::with('user', 'kpiGrade')->where('omset_log_id', $selectedLogId)->get() : collect();
        $kpiGrades = KpiGrade::all();

        return view('treasury.payroll', compact('omsetLogs', 'selectedLog', 'distributions', 'kpiGrades'));
    }

    public function updateKpi(Request $request, $id)
    {
        $this->checkAccess(['Treasury']);

        $request->validate([
            'kpi_grade_id' => 'nullable|exists:kpi_grades,id',
        ]);

        $distribution = PayrollDistribution::findOrFail($id);
        if ($distribution->status_pembayaran === 'paid') {
            return redirect()->back()->with('error', 'Gaji sudah dibayarkan, tidak dapat mengubah KPI.');
        }

        $log = $distribution->omsetLog;
        $grade = KpiGrade::find($request->kpi_grade_id);

        // Tukin calculation based on KPI Grade percentage and group count
        $tukin = 0;
        if ($grade) {
            $user = $distribution->user;
            $roleName = $user->role?->name;

            if (in_array($roleName, ['Penasehat', 'Front-man Officer'])) {
                $groupCount = User::whereHas('role', function($q) {
                    $q->whereIn('name', ['Penasehat', 'Front-man Officer']);
                })->count() ?: 1;
            } else {
                $groupCount = User::whereHas('role', function($q) use ($roleName) {
                    $q->where('name', $roleName);
                })->count() ?: 1;
            }

            // Tukin Pool divided equally into 6 shares, then split among group users based on KPI grade
            $groupTukinShare = $log->tukin_pool / 6;
            $tukin = ($grade->weight_percentage / 100) * ($groupTukinShare / $groupCount);
        }

        $distribution->update([
            'kpi_grade_id' => $request->kpi_grade_id,
            'nominal_tukin_diterima' => $tukin,
        ]);

        return redirect()->back()->with('success', 'Grade KPI dan Tukin berhasil diperbarui!');
    }

    public function bayarGaji(Request $request, $omsetLogId)
    {
        $this->checkAccess(['Treasury']);

        $log = OmsetLog::findOrFail($omsetLogId);
        $distributions = PayrollDistribution::where('omset_log_id', $omsetLogId)->get();

        if ($distributions->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data payroll untuk dibayar.');
        }

        $unpaidDistributions = $distributions->where('status_pembayaran', 'pending');
        if ($unpaidDistributions->isEmpty()) {
            return redirect()->back()->with('error', 'Semua payroll untuk omset ini sudah dibayarkan.');
        }

        \DB::transaction(function () use ($unpaidDistributions, $log) {
            $totalBayar = 0;

            foreach ($unpaidDistributions as $dist) {
                $dist->update(['status_pembayaran' => 'paid']);
                $totalBayar += ($dist->nominal_gapok_diterima + $dist->nominal_tukin_diterima);
            }

            // Record out flow in cash book
            CashTransaction::create([
                'tipe' => 'out',
                'kategori' => 'payroll',
                'nominal' => $totalBayar,
                'deskripsi' => "Pembayaran Payroll untuk Log Omset Tanggal " . $log->tanggal . " (Total Gaji: Rp " . number_format($totalBayar, 0, ',', '.') . ")",
            ]);
        });

        return redirect()->back()->with('success', 'Payroll berhasil dibayarkan secara penuh dan dicatat di Buku Kas Besar!');
    }

    public function events()
    {
        // All roles can view events
        $events = Event::with(['pic', 'expenses'])->latest()->paginate(10);
        $users = User::all();

        return view('treasury.events', compact('events', 'users'));
    }

    public function storeEvent(Request $request)
    {
        $this->checkAccess(['Treasury']);

        $request->validate([
            'nama_event' => 'required|string|max:255',
            'tanggal_event' => 'required|date',
            'pic_id' => 'required|exists:users,id',
            'budget_transportasi' => 'required|numeric|min:0',
            'budget_akomodasi' => 'required|numeric|min:0',
            'budget_venue' => 'required|numeric|min:0',
        ]);

        $totalBudget = $request->budget_transportasi + $request->budget_akomodasi + $request->budget_venue;

        Event::create([
            'nama_event' => $request->nama_event,
            'tanggal_event' => $request->tanggal_event,
            'pic_id' => $request->pic_id,
            'budget_transportasi' => $request->budget_transportasi,
            'budget_akomodasi' => $request->budget_akomodasi,
            'budget_venue' => $request->budget_venue,
            'total_budget' => $totalBudget,
        ]);

        // Note: Creating the planning budget does NOT debit the Cashbook yet.
        // Debit happens on actual/real itemized expenses registration.
        return redirect()->back()->with('success', 'Rencana Anggaran Event berhasil dibuat!');
    }

    public function storeExpense(Request $request, $eventId)
    {
        $this->checkAccess(['Treasury']);

        $request->validate([
            'kategori' => 'required|in:transportasi,akomodasi,venue',
            'nama_item' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'tanggal_pengeluaran' => 'required|date',
            'catatan' => 'nullable|string|max:255',
        ]);

        $event = Event::findOrFail($eventId);
        $totalHarga = $request->quantity * $request->harga_satuan;

        \DB::transaction(function () use ($request, $event, $totalHarga) {
            EventExpense::create([
                'event_id' => $event->id,
                'kategori' => $request->kategori,
                'nama_item' => $request->nama_item,
                'quantity' => $request->quantity,
                'harga_satuan' => $request->harga_satuan,
                'total_harga' => $totalHarga,
                'tanggal_pengeluaran' => $request->tanggal_pengeluaran,
                'catatan' => $request->catatan,
            ]);

            // Debits real Cash Transactions (out flow)
            CashTransaction::create([
                'tipe' => 'out',
                'kategori' => 'event',
                'nominal' => $totalHarga,
                'deskripsi' => "Pengeluaran Riel [{$request->kategori}]: {$request->nama_item} (Qty: {$request->quantity} @Rp " . number_format($request->harga_satuan, 0, ',', '.') . ") untuk Event: {$event->nama_event}",
            ]);
        });

        return redirect()->back()->with('success', 'Pengeluaran riil berhasil dicatat dan kas didebit!');
    }

    public function cashBook()
    {
        $this->checkAccess(['Treasury', 'Headman', 'Penasehat']);

        $transactions = CashTransaction::latest()->paginate(15);
        
        $totalIn = CashTransaction::where('tipe', 'in')->sum('nominal');
        $totalOut = CashTransaction::where('tipe', 'out')->sum('nominal');
        $saldoKas = $totalIn - $totalOut;

        return view('treasury.cashbook', compact('transactions', 'saldoKas'));
    }

    public function storeCash(Request $request)
    {
        $this->checkAccess(['Treasury']);

        $request->validate([
            'tipe' => 'required|in:in,out',
            'kategori' => 'required|in:operasional,event,payroll,omset',
            'nominal' => 'required|numeric|min:0',
            'deskripsi' => 'required|string|max:500',
        ]);

        CashTransaction::create($request->all());

        return redirect()->back()->with('success', 'Transaksi kas berhasil dicatat!');
    }

    public function users()
    {
        $this->checkAccess(['Treasury']);

        $users = User::with('role')->get();
        $roles = Role::all();

        return view('treasury.users', compact('users', 'roles'));
    }

    public function storeUser(Request $request)
    {
        $this->checkAccess(['Treasury']);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'role_id' => 'required|string|max:255',
            'custom_role' => 'required_if:role_id,custom|nullable|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        $roleId = $request->role_id;
        if ($roleId === 'custom') {
            // Create a new Role model record dynamically
            $newRole = Role::firstOrCreate(
                ['name' => $request->custom_role],
                ['name' => $request->custom_role, 'description' => "Custom Role"]
            );
            $roleId = $newRole->id;
        }

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $roleId,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'User baru berhasil ditambahkan!');
    }

    public function updateUser(Request $request, $id)
    {
        $this->checkAccess(['Treasury']);

        $request->validate([
            'role_id' => 'required|string|max:255',
            'custom_role' => 'required_if:role_id,custom|nullable|string|max:255',
        ]);

        $roleId = $request->role_id;
        if ($roleId === 'custom') {
            $newRole = Role::firstOrCreate(
                ['name' => $request->custom_role],
                ['name' => $request->custom_role, 'description' => "Custom Role"]
            );
            $roleId = $newRole->id;
        }

        $user = User::findOrFail($id);
        $user->update([
            'role_id' => $roleId,
        ]);

        return redirect()->back()->with('success', 'Peran (role) pengguna berhasil diperbarui!');
    }

    public function destroyUser($id)
    {
        $this->checkAccess(['Treasury']);

        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }
}
