<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    /**
     * Список платежей по счету
     */
    public function index(Client $client, Invoice $invoice)
    {
        if ($invoice->client_id !== $client->id) abort(403);

        $payments = $invoice->payments()->latest()->get();

        return view('clients.payments.index', compact('client', 'invoice', 'payments'));
    }

    /**
     * Добавление платежа
     */
    public function store(Request $request, Client $client, Invoice $invoice)
    {
        if ($invoice->client_id !== $client->id) abort(403);

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'nullable|string|max:255',
            'paid_at' => 'required|date',
        ]);

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount'    => $request->amount,
            'method'    => $request->method,
            'paid_at'   => $request->paid_at,
        ]);

        /** ============================
         * 1. Пересчёт оплаченной суммы
         * ============================ */
        $paidSum = $invoice->payments()->sum('amount');

        // Счет считается оплаченным если оплачено >= суммы счета
        if ($paidSum >= $invoice->amount) {
            $invoice->update([
                'status'  => 'paid',
                'paid_at' => $payment->paid_at,
            ]);

            // Добавляем в доход клиента финальную сумму счета
            $client->update([
                'total_revenue' => $client->total_revenue + $invoice->amount
            ]);
        }

        /** ================================
         * 2. Логирование активности клиента
         * ================================ */
        $client->update(['last_activity_at' => now()]);

        return redirect()->back()->with('success', 'Платеж добавлен.');
    }

    /**
     * Удаление платежа
     */
    public function destroy(Client $client, Invoice $invoice, Payment $payment)
    {
        if ($invoice->client_id !== $client->id) abort(403);
        if ($payment->invoice_id !== $invoice->id) abort(403);

        $amount = $payment->amount;
        $payment->delete();

        /** =========================================
         * 1. Пересчёт общих платежей после удаления
         * ========================================= */
        $paidSum = $invoice->payments()->sum('amount');

        if ($paidSum < $invoice->amount) {
            // Снова делаем счет неоплаченным
            // Выбираем статус: было ли что-то оплачено?
            $newStatus = $paidSum > 0 ? 'sent' : 'draft';

            $invoice->update([
                'status'  => $newStatus,
                'paid_at' => null,
            ]);

            // Откатываем доход клиента
            $client->update([
                'total_revenue' => max(0, $client->total_revenue - $invoice->amount)
            ]);
        }

        /** =====================================
         * 2. Логирование активности клиента
         * ===================================== */
        $client->update(['last_activity_at' => now()]);

        return redirect()->back()->with('success', 'Платеж удалён.');
    }
}
