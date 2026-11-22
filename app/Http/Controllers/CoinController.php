<?php

namespace App\Http\Controllers;

use App\Models\CoinTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CoinController extends Controller
{
    public const PAKET = [
        'p100' => ['koin' => 100, 'bonus' => 0, 'harga' => 10000],
        'p500' => ['koin' => 500, 'bonus' => 50, 'harga' => 45000],
        'p1000' => ['koin' => 1000, 'bonus' => 150, 'harga' => 85000],
        'p2500' => ['koin' => 2500, 'bonus' => 500, 'harga' => 200000],
    ];

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('coins.index', [
            'saldo' => $user->coin_balance,
            'riwayat' => $user->coinTransactions()->where('type', 'topup')->latest()->take(5)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'paket' => ['required', Rule::in(array_keys(self::PAKET))],
        ], [
            'paket.required' => 'Pilih paket koin terlebih dahulu.',
            'paket.in' => 'Paket koin tidak valid.',
        ]);

        $paket = self::PAKET[$validated['paket']];
        $nama = $paket['koin'] . ' Koin' . ($paket['bonus'] ? ' + ' . $paket['bonus'] . ' Bonus' : '');

        $trx = $request->user()->coinTransactions()->create([
            'type' => 'topup',
            'amount' => $paket['koin'] + $paket['bonus'],
            'description' => $nama,
            'order_id' => 'TOPUP-' . Str::ulid(),
            'status' => CoinTransaction::STATUS_PENDING,
            'price' => $paket['harga'],
        ]);

        $response = Http::withBasicAuth(config('services.midtrans.server_key'), '')
            ->acceptJson()
            ->when($request->isSecure(), fn ($http) => $http->withHeaders([
                'X-Override-Notification' => route('midtrans.notification'),
            ]))
            ->post(config('services.midtrans.snap_url'), [
                'transaction_details' => ['order_id' => $trx->order_id, 'gross_amount' => $trx->price],
                'item_details' => [['id' => $validated['paket'], 'price' => $trx->price, 'quantity' => 1, 'name' => $nama]],
                'customer_details' => ['first_name' => $request->user()->name],
                'callbacks' => ['finish' => route('coins.finish'), 'error' => route('coins.finish')],
            ]);

        if (! $response->successful() || ! $response->json('redirect_url')) {
            $trx->update(['status' => CoinTransaction::STATUS_FAILED]);

            return back()->with('error', 'Gagal menghubungi Midtrans. Coba lagi beberapa saat.');
        }

        return redirect()->away($response->json('redirect_url'));
    }

    public function notification(Request $request): Response
    {
        $trx = CoinTransaction::where('order_id', $request->input('order_id'))->first();

        if ($trx) {
            $this->sinkronkan($trx);
        }

        return response('OK');
    }

    public function finish(Request $request): RedirectResponse
    {
        $trx = $request->user()->coinTransactions()
            ->where('order_id', $request->query('order_id'))
            ->firstOrFail();

        $status = $this->sinkronkan($trx);

        return redirect()->route('coins.index')->with(...match ($status) {
            CoinTransaction::STATUS_SUCCESS => ['success', "Pembayaran berhasil! {$trx->amount} koin sudah masuk ke saldo Anda."],
            CoinTransaction::STATUS_FAILED => ['error', 'Pembayaran gagal atau dibatalkan.'],
            default => ['warning', 'Pembayaran belum selesai. Setelah membayar, klik Cek Status di riwayat transaksi.'],
        });
    }

    private function sinkronkan(CoinTransaction $trx): string
    {
        if ($trx->status !== CoinTransaction::STATUS_PENDING) {
            return $trx->status;
        }

        $data = Http::withBasicAuth(config('services.midtrans.server_key'), '')
            ->acceptJson()
            ->get(config('services.midtrans.api_url') . "/{$trx->order_id}/status")
            ->json();

        $berhasil = in_array($data['transaction_status'] ?? null, ['settlement', 'capture'], true)
            && ($data['fraud_status'] ?? 'accept') === 'accept'
            && (int) ($data['gross_amount'] ?? 0) === $trx->price;

        if ($berhasil) {
            DB::transaction(function () use ($trx) {
                $terkunci = CoinTransaction::whereKey($trx->id)->lockForUpdate()->first();

                if ($terkunci->status !== CoinTransaction::STATUS_PENDING) {
                    return;
                }

                $terkunci->user()->increment('coin_balance', $terkunci->amount);
                $terkunci->update([
                    'status' => CoinTransaction::STATUS_SUCCESS,
                    'balance_after' => $terkunci->user()->value('coin_balance'),
                ]);
            });
        } elseif (in_array($data['transaction_status'] ?? null, ['deny', 'cancel', 'expire', 'failure'], true)) {
            $trx->update(['status' => CoinTransaction::STATUS_FAILED]);
        }

        return $trx->fresh()->status;
    }
}
