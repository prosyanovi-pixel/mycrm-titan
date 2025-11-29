<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoicesController extends Controller
{
    /**
     * Список счетов клиента
     */
    public function index(Client $client)
    {
        $invoices = $client->invoices()->latest()->get();

        return view('clients.invoices.index', compact('client', 'invoices'));
    }

    /**
     * Создание счета
     */
    public function store(Request $request, Client $client)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'issued_at' => 'nullable|date',
            'description' => 'nullable|string|max:500',
        ], [
            'amount.required' => 'Поле "Сумма" обязательно для заполнения',
            'amount.numeric' => 'Сумма должна быть числом',
            'amount.min' => 'Сумма должна быть больше 0',
        ]);

        try {
            $invoice = Invoice::create([
                'client_id' => $client->id,
                'amount'    => $request->amount,
                'status'    => 'draft', // По умолчанию создаем как черновик
                'issued_at' => $request->issued_at ?? now(),
                'description' => $request->description,
            ]);

            $client->update(['last_activity_at' => now()]);

            return redirect()->back()->with('success', 'Счет успешно создан.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ошибка при создании счета: ' . $e->getMessage());
        }
    }

    /**
     * Обновление счета
     */
    public function update(Request $request, Client $client, Invoice $invoice)
    {
        if ($invoice->client_id !== $client->id) abort(403);

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'issued_at' => 'nullable|date',
            'description' => 'nullable|string|max:500',
        ]);

        $invoice->update([
            'amount' => $request->amount,
            'issued_at' => $request->issued_at,
            'description' => $request->description,
        ]);

        $client->update(['last_activity_at' => now()]);

        return redirect()->back()->with('success', 'Счет обновлен!');
    }

    /**
     * Быстрая смена статуса
     */
    public function changeStatus(Client $client, Invoice $invoice, $status)
    {
        if ($invoice->client_id !== $client->id) abort(403);

        if (!in_array($status, ['draft','sent','paid','overdue'])) {
            abort(400, 'Неверный статус');
        }

        $oldStatus = $invoice->status;
        
        $updateData = ['status' => $status];
        
        // Если статус меняется на "paid", устанавливаем дату оплаты
        if ($status === 'paid' && $oldStatus !== 'paid') {
            $updateData['paid_at'] = now();
        }
        
        // Если статус уходит из "paid", сбрасываем дату оплаты
        if ($oldStatus === 'paid' && $status !== 'paid') {
            $updateData['paid_at'] = null;
        }

        $invoice->update($updateData);

        // Обновление дохода
        if ($status === 'paid' && $oldStatus !== 'paid') {
            $client->update([
                'total_revenue' => $client->total_revenue + $invoice->amount,
            ]);
        }

        if ($oldStatus === 'paid' && $status !== 'paid') {
            // откат
            $client->update([
                'total_revenue' => max(0, $client->total_revenue - $invoice->amount),
            ]);
        }

        $client->update(['last_activity_at' => now()]);

        return redirect()->back()->with('success', 'Статус счета обновлен!');
    }

    /**
     * Отметить как отправленный
     */
    public function sendInvoice(Client $client, Invoice $invoice)
    {
        if ($invoice->client_id !== $client->id) abort(403);

        $invoice->update([
            'status' => 'sent',
            'issued_at' => now(),
        ]);

        $client->update(['last_activity_at' => now()]);

        return redirect()->back()->with('success', 'Счет отправлен клиенту!');
    }

    /**
     * Отметить как оплаченный
     */
    public function payInvoice(Client $client, Invoice $invoice)
    {
        if ($invoice->client_id !== $client->id) abort(403);

        $oldStatus = $invoice->status;
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Увеличиваем доход только если счет не был уже оплачен
        if ($oldStatus !== 'paid') {
            $client->update([
                'total_revenue' => $client->total_revenue + $invoice->amount,
            ]);
        }

        $client->update(['last_activity_at' => now()]);

        return redirect()->back()->with('success', 'Счет отмечен как оплаченный!');
    }

    /**
     * Удаление счета
     */
    public function destroy(Client $client, Invoice $invoice)
    {
        if ($invoice->client_id !== $client->id) abort(403);

        // если счет был оплачен – уменьшаем total_revenue
        if ($invoice->status === 'paid') {
            $client->update([
                'total_revenue' => max(0, $client->total_revenue - $invoice->amount)
            ]);
        }

        $invoice->delete();

        return redirect()->back()->with('success', 'Счет удалён.');
    }
}